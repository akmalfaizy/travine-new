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
        ]);

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
            'payment_type'  => 'required|in:dp,lunas',
            'payment_proof' => 'required|image|max:4096',
            'agree_terms'   => 'required|accepted',
        ]);

        $tour        = Tour::with('category')->findOrFail($draft['tour_id']);
        $participants = (int) $draft['participants'];
        $dpPerPax    = 100000;
        $dpAmount    = $dpPerPax * $participants;
        $fullAmount  = $tour->price * $participants;
        $paymentType = $request->input('payment_type');
        $paidAmount  = $paymentType === 'dp' ? $dpAmount : $fullAmount;

        // Upload bukti bayar
        $proofPath = $request->file('payment_proof')->store('proofs', 'public');

        // Buat kode booking unik
        $bookingCode = Booking::generateBookingCode();

        $booking = Booking::create([
            'tour_id'        => $draft['tour_id'],
            'booking_code'   => $bookingCode,
            'name'           => $draft['name'],
            'whatsapp'       => $draft['whatsapp'],
            'email'          => $draft['email'],
            'departure_date' => $draft['departure_date'],
            'participants'   => $participants,
            'notes'          => $draft['notes'] ?? null,
            'total_price'    => $fullAmount,
            'discount'       => 0,
            'payment_type'   => $paymentType,
            'paid_amount'    => $paidAmount,
            'payment_proof'  => $proofPath,
            'payment_status' => $paymentType === 'dp' ? 'dp_paid' : 'paid',
            'booking_status' => 'waiting_verification',
        ]);

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

        $qrUrl    = url('/bookings/' . $booking->booking_code . '/verify');
        $writer   = new PngWriter();
        $qrCode   = new QrCode(data: $qrUrl, size: 150, margin: 5);
        $qrResult = $writer->write($qrCode);
        $qrBase64 = base64_encode($qrResult->getString());

        $pdf = Pdf::loadView('pdf.invoice', compact('booking', 'qrBase64'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("Invoice-{$booking->booking_code}.pdf");
    }
}
