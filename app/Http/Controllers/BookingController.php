<?php

namespace App\Http\Controllers;

use App\Mail\BookingInvoiceMail;
use App\Models\Booking;
use App\Models\Tour;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    /**
     * Step 1: Terima data form dari halaman detail tour, simpan ke session, redirect ke halaman payment.
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'tour_id'        => 'required|exists:tours,id',
            'name'           => 'required|string|max:255',
            'whatsapp'       => 'required|string|max:20',
            'email'          => 'required|email|max:255',
            'departure_date' => 'required|date|after:today',
            'participants'   => 'required|integer|min:1|max:100',
            'notes'          => 'nullable|string|max:1000',
            'referral_code'  => 'nullable|string|max:50',
        ]);

        if (!empty($data['referral_code'])) {
            $partner = \App\Models\Partner::where('referral_code', strtoupper(trim($data['referral_code'])))
                ->whereIn('status', ['active', 'pending'])
                ->first();
            if ($partner) {
                session(['travine_referral_code' => $partner->referral_code]);
                $data['referral_code'] = $partner->referral_code;
            }
        }

        // Simpan data booking ke session
        session(['booking_draft' => $data]);

        return redirect()->route('bookings.payment');
    }

    /**
     * Step 2: Tampilkan halaman pembayaran dengan pilihan DP / Lunas.
     */
    public function paymentPage()
    {
        $draft = session('booking_draft');
        if (!$draft) {
            return redirect()->route('home')->with('error', 'Sesi pemesanan tidak ditemukan. Silakan ulangi dari awal.');
        }

        $tour = Tour::with('category')->findOrFail($draft['tour_id']);

        $dpPerPax    = 100000; // Rp 100.000 per peserta
        $dpAmount    = $dpPerPax * $draft['participants'];
        $fullAmount  = $tour->price * $draft['participants'];

        $bankName    = \App\Models\Setting::get('bank_name', 'Bank BCA');
        $bankAccount = \App\Models\Setting::get('bank_account', '1234567890');
        $bankHolder  = \App\Models\Setting::get('bank_holder', 'PT Travine Indonesia');
        $whatsApp    = \App\Models\Setting::get('phone_whatsapp', '6282137925585');

        return view('bookings.payment', compact(
            'draft', 'tour', 'dpAmount', 'fullAmount',
            'bankName', 'bankAccount', 'bankHolder', 'whatsApp'
        ));
    }

    /**
     * Step 3: Finalisasi pemesanan – simpan ke DB, upload bukti, kirim email.
     */
    public function store(Request $request)
    {
        $draft = session('booking_draft');
        if (!$draft) {
            return redirect()->route('home')->with('error', 'Sesi pemesanan habis. Silakan ulangi.');
        }

        $request->validate([
            'payment_type'              => 'required|in:dp,lunas',
            'payment_proof'             => 'required|image|max:4096',
            'agree_terms'               => 'required|accepted',
            'participants'              => 'required|array',
            'participants.*.name'       => 'required|string|max:255',
            'participants.*.nik'        => 'required|string|max:50',
            'participants.*.birth_date' => 'required|date',
            'participants.*.phone'      => 'required|string|max:30',
            'participants.*.address'    => 'required|string|max:500',
        ], [
            'participants.*.name.required'       => 'Nama lengkap seluruh peserta wajib diisi untuk keperluan asuransi.',
            'participants.*.nik.required'        => 'NIK / No. KTP / KITAS seluruh peserta wajib diisi.',
            'participants.*.birth_date.required' => 'Tanggal lahir seluruh peserta wajib diisi.',
            'participants.*.phone.required'      => 'Nomor telepon seluruh peserta wajib diisi.',
            'participants.*.address.required'    => 'Alamat seluruh peserta wajib diisi.',
            'payment_proof.required'             => 'Bukti pembayaran wajib diunggah.',
            'agree_terms.accepted'               => 'Anda harus menyetujui Syarat & Ketentuan.',
        ]);

        $tour        = Tour::with('category')->findOrFail($draft['tour_id']);
        $participants = (int) $draft['participants'];
        $dpPerPax    = 100000;
        $dpAmount    = $dpPerPax * $participants;
        $fullAmount  = $tour->price * $participants;
        $paymentType = $request->input('payment_type');
        $paidAmount  = $paymentType === 'dp' ? $dpAmount : $fullAmount;

        // Data Peserta Asuransi
        $participantsData = [];
        if ($request->has('participants') && is_array($request->input('participants'))) {
            foreach ($request->input('participants') as $idx => $p) {
                $participantsData[] = [
                    'index'      => $idx + 1,
                    'name'       => trim($p['name'] ?? ''),
                    'nik'        => trim($p['nik'] ?? ''),
                    'birth_date' => trim($p['birth_date'] ?? ''),
                    'phone'      => trim($p['phone'] ?? ''),
                    'address'    => trim($p['address'] ?? ''),
                ];
            }
        }

        // Upload bukti bayar
        $proofPath = $request->file('payment_proof')->store('proofs', 'public');

        // Buat kode booking unik
        $bookingCode = Booking::generateBookingCode();

        // Referral attribution from draft, session or cookie
        $refCode = $draft['referral_code'] ?? session('travine_referral_code') ?? $request->cookie('travine_referral_code');
        $partnerId = null;
        if ($refCode) {
            $partner = \App\Models\Partner::where('referral_code', strtoupper(trim($refCode)))->whereIn('status', ['active', 'pending'])->first();
            if ($partner) {
                $partnerId = $partner->id;
                $refCode   = $partner->referral_code;
            } else {
                $refCode   = null;
            }
        }

        $booking = Booking::create([
            'tour_id'           => $draft['tour_id'],
            'partner_id'        => $partnerId,
            'referral_code'     => $refCode,
            'referral_source'   => $refCode ? 'web_link' : null,
            'booking_code'      => $bookingCode,
            'name'              => $draft['name'],
            'whatsapp'          => $draft['whatsapp'],
            'email'             => $draft['email'],
            'departure_date'    => $draft['departure_date'],
            'participants'      => $participants,
            'participants_data' => $participantsData ?: null,
            'notes'             => $draft['notes'] ?? null,
            'total_price'       => $fullAmount,
            'discount'          => 0,
            'payment_type'      => $paymentType,
            'paid_amount'       => $paidAmount,
            'payment_proof'     => $proofPath,
            'payment_status'    => $paymentType === 'dp' ? 'dp_paid' : 'paid',
            'booking_status'    => 'waiting_verification',
        ]);

        // Record commission snapshot if partner referral exists
        if ($partnerId) {
            \App\Services\CommissionService::createOrUpdateForBooking($booking);
        }

        // Kirim email invoice ke pemesan
        try {
            Mail::to($booking->email)->send(new BookingInvoiceMail($booking->load('tour.category')));
        } catch (\Throwable $e) {
            Log::error('Booking invoice mail failed: ' . $e->getMessage());
        }

        // Hapus session draft
        session()->forget('booking_draft');

        return redirect()->route('bookings.show', $booking->booking_code)
                         ->with('success', 'Pemesanan berhasil! Bukti pembayaran Anda sedang diverifikasi. Email konfirmasi telah dikirim ke ' . $booking->email);
    }

    public function show(string $code)
    {
        $booking = Booking::where('booking_code', $code)->with('tour.category')->firstOrFail();
        return view('bookings.show', compact('booking'));
    }

    public function uploadProof(Request $request, string $code)
    {
        $booking = Booking::where('booking_code', $code)->firstOrFail();

        $request->validate([
            'payment_proof' => 'required|image|max:4096',
        ]);

        $path = $request->file('payment_proof')->store('proofs', 'public');
        $booking->update([
            'payment_proof'  => $path,
            'booking_status' => 'waiting_verification',
            'payment_status' => 'dp_paid',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Tim kami akan segera memverifikasi.');
    }

    public function verify(string $code)
    {
        $booking = Booking::where('booking_code', $code)->with('tour')->firstOrFail();
        return view('bookings.verify', compact('booking'));
    }

    public function downloadInvoice(string $code)
    {
        $booking = Booking::where('booking_code', $code)->with('tour')->firstOrFail();

        abort_if(!$booking->invoice_number, 404, 'Invoice belum tersedia.');
        abort_if(
            !in_array($booking->booking_status, ['confirmed', 'departed', 'completed']),
            403,
            'Invoice hanya dapat diunduh setelah pemesanan dikonfirmasi oleh admin.'
        );

        $qrUrl    = url('/bookings/' . $booking->booking_code . '/verify');
        $writer   = new PngWriter();
        $qrCode   = new QrCode(data: $qrUrl, size: 150, margin: 5);
        $qrResult = $writer->write($qrCode);
        $qrBase64 = base64_encode($qrResult->getString());

        $pdf = Pdf::loadView('pdf.invoice', compact('booking', 'qrBase64'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("Invoice-{$booking->booking_code}.pdf");
    }

    /**
     * Halaman form pencarian status pemesanan.
     */
    public function trackPage()
    {
        return view('bookings.track');
    }

    /**
     * Proses pencarian booking berdasarkan kode yang diinput customer.
     */
    public function trackSearch(Request $request)
    {
        $request->validate([
            'booking_code' => 'required|string|min:4|max:20',
        ], [
            'booking_code.required' => 'Kode booking wajib diisi.',
            'booking_code.min'      => 'Kode booking tidak valid.',
        ]);

        $code    = strtoupper(trim($request->input('booking_code')));
        $booking = Booking::where('booking_code', $code)->first();

        if (!$booking) {
            return back()
                ->withInput()
                ->withErrors(['booking_code' => 'Kode booking "' . $code . '" tidak ditemukan. Periksa kembali kode yang Anda masukkan.']);
        }

        return redirect()->route('bookings.show', $booking->booking_code);
    }
}
