<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\NotificationLog;
use App\Models\Tour;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\CashLedger;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('tour')->latest();

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }
        if ($request->filled('departure_date')) {
            $query->whereDate('departure_date', $request->departure_date);
        }
        if ($request->filled('departure_from')) {
            $query->whereDate('departure_date', '>=', $request->departure_from);
        }
        if ($request->filled('departure_to')) {
            $query->whereDate('departure_date', '<=', $request->departure_to);
        }
        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('whatsapp', 'like', "%{$request->search}%");
            });
        }

        $totalPax = (clone $query)->sum('participants');
        $totalRevenue = (clone $query)->sum('total_price');
        $bookings = $query->paginate(20);

        $tours = Tour::orderBy('title')->get();
        $selectedTour = $request->filled('tour_id') ? Tour::find($request->tour_id) : null;

        return view('admin.bookings.index', compact('bookings', 'tours', 'selectedTour', 'totalPax', 'totalRevenue'));
    }

    public function show(Booking $booking)
    {
        $booking->load('tour', 'notificationLogs');
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'booking_status' => 'required|in:waiting_payment,waiting_verification,processing,confirmed,departed,completed,cancelled',
            'admin_notes'    => 'nullable|string',
        ]);

        $booking->update($data);

        // Sync commission status
        \App\Services\CommissionService::syncStatusForBooking($booking);

        // Generate invoice & send confirmation email with PDF when confirmed
        if ($data['booking_status'] === 'confirmed') {
            if (!$booking->invoice_number) {
                $booking->update(['invoice_number' => Booking::generateInvoiceNumber()]);
            }
            $freshBooking = $booking->fresh(['tour.category']);
            try {
                $this->sendConfirmedInvoiceEmail($freshBooking);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send confirmed invoice email for booking #' . $booking->id . ': ' . $e->getMessage());
            }

            // Auto-add margin to cash ledger
            try {
                CashLedger::addFromBooking($booking->fresh(['tour']));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to add cash ledger for booking #' . $booking->id . ': ' . $e->getMessage());
            }
        }

        // Send WhatsApp notification
        $wa = new WhatsAppService();
        if ($data['booking_status'] === 'confirmed') {
            $wa->sendBookingConfirmation($booking->fresh(['tour']));
        } else {
            $wa->sendStatusUpdate($booking->fresh(['tour']), $booking->booking_status_label);
        }

        ActivityLog::record("Update status booking {$booking->booking_code} → {$data['booking_status']}");

        return back()->with('success', 'Status pemesanan berhasil diperbarui. Email invoice resmi & notifikasi terverifikasi telah dikirim ke pelanggan.');
    }

    public function updatePayment(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'payment_status' => 'required|in:unpaid,dp_paid,paid,refund,failed',
        ]);

        $booking->update($data);

        // Sync commission status
        \App\Services\CommissionService::syncStatusForBooking($booking);

        // If payment status is updated to paid/dp_paid and booking is confirmed, send updated invoice email
        if (in_array($data['payment_status'], ['paid', 'dp_paid']) && in_array($booking->booking_status, ['confirmed', 'completed'])) {
            if (!$booking->invoice_number) {
                $booking->update(['invoice_number' => Booking::generateInvoiceNumber()]);
            }
            $freshBooking = $booking->fresh(['tour.category']);
            try {
                $this->sendConfirmedInvoiceEmail($freshBooking);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send updated payment invoice email for booking #' . $booking->id . ': ' . $e->getMessage());
            }
        }

        ActivityLog::record("Update pembayaran booking {$booking->booking_code} → {$data['payment_status']}");

        return back()->with('success', 'Status pembayaran berhasil diperbarui. Email invoice terverifikasi telah dikirim ke pelanggan.');
    }

    /**
     * Helper to generate PDF invoice and send confirmed email to customer.
     */
    private function sendConfirmedInvoiceEmail(Booking $booking): void
    {
        $qrUrl    = url('/bookings/' . $booking->booking_code . '/verify');
        $writer   = new PngWriter();
        $qrCode   = new QrCode(data: $qrUrl, size: 150, margin: 5);
        $qrResult = $writer->write($qrCode);
        $qrBase64 = base64_encode($qrResult->getString());

        $pdf = Pdf::loadView('pdf.invoice', compact('booking', 'qrBase64'))
                  ->setPaper('a4', 'portrait');
        $pdfData = $pdf->output();

        Mail::to($booking->email)->send(new \App\Mail\BookingConfirmedMail($booking, $pdfData));

        NotificationLog::create([
            'booking_id' => $booking->id,
            'recipient'  => $booking->email,
            'type'       => 'email',
            'status'     => 'success',
            'sent_at'    => now(),
        ]);
    }

    // Existing method unchanged
    public function downloadInvoice(Booking $booking)
    {
        // Generate QR code
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
     * Resend the invoice email to the customer.
     */
    public function resendInvoice(Booking $booking)
    {
        if (!$booking->invoice_number) {
            $booking->update(['invoice_number' => Booking::generateInvoiceNumber()]);
        }
        $freshBooking = $booking->fresh(['tour.category']);
        $this->sendConfirmedInvoiceEmail($freshBooking);

        return back()->with('success', 'Invoice email berhasil dikirim ulang.');
    }


    public function exportCsv()
    {
        $bookings = Booking::with('tour')->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-' . now()->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kode Booking', 'Invoice', 'Nama', 'WhatsApp', 'Email', 'Paket Wisata', 'Tgl Berangkat', 'Peserta', 'Total', 'Status Booking', 'Status Bayar', 'Tanggal Pesan']);
            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->booking_code, $b->invoice_number, $b->name, $b->whatsapp, $b->email,
                    $b->tour?->title, $b->departure_date->format('d/m/Y'), $b->participants,
                    $b->total_price, $b->booking_status_label, $b->payment_status_label,
                    $b->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data peserta asuransi — satu baris per peserta.
     * Kolom: Kode Booking, Paket Wisata, Tanggal Keberangkatan,
     *        Nama Peserta, NIK/KTP/KITAS, Tanggal Lahir, Alamat, No. Telepon.
     */
    public function exportInsuranceCsv(Request $request)
    {
        $query = Booking::with('tour')
            ->whereNotNull('participants_data')
            ->whereIn('booking_status', ['confirmed', 'departed', 'completed'])
            ->latest();

        // Optional filters
        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }
        if ($request->filled('departure_from')) {
            $query->whereDate('departure_date', '>=', $request->departure_from);
        }
        if ($request->filled('departure_to')) {
            $query->whereDate('departure_date', '<=', $request->departure_to);
        }

        $bookings = $query->get();

        $filename = 'data-asuransi-' . now()->format('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens correctly
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Booking',
                'Paket Wisata',
                'Tanggal Keberangkatan',
                'Nama Peserta',
                'NIK / No. KTP / KITAS',
                'Tanggal Lahir',
                'Alamat',
                'No. Telepon',
            ]);

            foreach ($bookings as $b) {
                $participants = $b->participants_data ?? [];

                // If no participant detail data, output one row with PIC only
                if (empty($participants)) {
                    fputcsv($handle, [
                        $b->booking_code,
                        $b->tour?->title ?? '-',
                        $b->departure_date ? $b->departure_date->format('d/m/Y') : '-',
                        $b->name,
                        '-', '-', '-', $b->whatsapp,
                    ]);
                    continue;
                }

                foreach ($participants as $p) {
                    fputcsv($handle, [
                        $b->booking_code,
                        $b->tour?->title ?? '-',
                        $b->departure_date ? $b->departure_date->format('d/m/Y') : '-',
                        $p['name']       ?? '-',
                        $p['nik']        ?? ($p['ktp'] ?? '-'),
                        $p['birth_date'] ?? ($p['tanggal_lahir'] ?? '-'),
                        $p['address']    ?? ($p['alamat'] ?? '-'),
                        $p['phone']      ?? ($p['telepon'] ?? ($p['whatsapp'] ?? '-')),
                    ]);
                }
            }
            fclose($handle);
        };

        ActivityLog::record('Export CSV Asuransi diunduh.');
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Manifest Data Peserta Open Trip ke PDF untuk tim lapangan / tour leader.
     * Filter berdasarkan tour_id, tanggal keberangkatan, status booking, status bayar.
     */
    public function exportManifestPdf(Request $request)
    {
        $query = Booking::with(['tour.category', 'partner']);

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }
        if ($request->filled('departure_date')) {
            $query->whereDate('departure_date', $request->departure_date);
        }
        if ($request->filled('departure_from')) {
            $query->whereDate('departure_date', '>=', $request->departure_from);
        }
        if ($request->filled('departure_to')) {
            $query->whereDate('departure_date', '<=', $request->departure_to);
        }
        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        } else {
            // Default manifest untuk operasional: tidak tampilkan yang cancelled kecuali difilter manual
            $query->where('booking_status', '!=', 'cancelled');
        }
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('whatsapp', 'like', "%{$request->search}%");
            });
        }

        $bookings = $query->orderBy('departure_date', 'asc')->orderBy('created_at', 'asc')->get();

        $selectedTour = $request->filled('tour_id') ? Tour::find($request->tour_id) : null;
        $departureDate = $request->filled('departure_date') ? \Carbon\Carbon::parse($request->departure_date) : null;

        $logoPath = public_path('images/logo_tagline.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $totalBookings = $bookings->count();
        $totalPax = $bookings->sum('participants');
        $totalPaidPax = $bookings->where('payment_status', 'paid')->sum('participants');
        $totalDpPax = $bookings->where('payment_status', 'dp_paid')->sum('participants');
        $totalUnpaidPax = $bookings->whereIn('payment_status', ['unpaid', 'failed'])->sum('participants');

        $totalRevenue = $bookings->sum('total_price');
        $totalPaidAmount = $bookings->sum('paid_amount');
        $totalRemainingAmount = $bookings->sum('remaining_amount');

        $pdf = Pdf::loadView('pdf.manifest', compact(
            'bookings',
            'selectedTour',
            'departureDate',
            'logoBase64',
            'totalBookings',
            'totalPax',
            'totalPaidPax',
            'totalDpPax',
            'totalUnpaidPax',
            'totalRevenue',
            'totalPaidAmount',
            'totalRemainingAmount'
        ))->setPaper('a4', 'portrait');

        ActivityLog::record('Export PDF Manifest Peserta Open Trip diunduh.');

        $tourSlug = $selectedTour ? \Illuminate\Support\Str::slug($selectedTour->title) : 'semua-trip';
        $dateStr = $departureDate ? $departureDate->format('Y-m-d') : date('Y-m-d');
        $filename = "manifest-open-trip-{$tourSlug}-{$dateStr}.pdf";

        return $pdf->download($filename);
    }


    public function destroy(Booking $booking)
    {
        ActivityLog::record("Menghapus booking: {$booking->booking_code}");
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Data pemesanan berhasil dihapus.');
    }

    /**
     * Form buat booking manual oleh admin.
     */
    public function create()
    {
        $tours = Tour::with('category')->where('is_active', true)->get();
        return view('admin.bookings.create', compact('tours'));
    }

    /**
     * Simpan booking manual dari admin.
     */
    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'tour_id'       => 'required|exists:tours,id',
            'name'          => 'required|string|max:255',
            'whatsapp'      => 'required|string|max:20',
            'email'         => 'required|email|max:255',
            'departure_date'=> 'required|date',
            'duration'      => 'nullable|string|max:255',
            'duration_days' => 'nullable|integer|min:1',
            'participants'  => 'nullable|integer|min:1',
            'discount'      => 'nullable|integer|min:0',
            'total_price'   => 'nullable|integer|min:0',
            'notes'         => 'nullable|string',
            'booking_status'=> 'required|in:waiting_payment,waiting_verification,processing,confirmed,departed,completed,cancelled',
            'payment_status'=> 'required|in:unpaid,dp_paid,paid,refund,failed',
        ]);

        $tour     = Tour::with('category')->findOrFail($data['tour_id']);
        $discount = (int) ($data['discount'] ?? 0);
        $data['booking_code'] = Booking::generateBookingCode();

        // Jika admin mengisi total_price manual, pakai itu langsung
        if (!empty($data['total_price'])) {
            $data['total_price'] = (int) $data['total_price'];
        } else {
            // Hitung berdasarkan kategori
            $isTransport = $tour->category?->slug === 'transportasi';
            if ($isTransport) {
                $days = (int) ($data['duration_days'] ?? 1);
                $data['total_price'] = max(0, ($tour->price * $days) - $discount);
                // Participants default 1 untuk transport
                $data['participants'] = $data['participants'] ?? 1;
            } else {
                $participants = (int) ($data['participants'] ?? 1);
                $data['total_price'] = max(0, ($tour->price * $participants) - $discount);
                $data['participants'] = $participants;
            }
        }

        // Pastikan participants terisi
        $data['participants'] = $data['participants'] ?? 1;

        // Jika status confirmed, langsung generate invoice
        if ($data['booking_status'] === 'confirmed') {
            $data['invoice_number'] = Booking::generateInvoiceNumber();
        }

        // Hapus duration_days dari data (tidak ada kolom ini di bookings)
        unset($data['duration_days']);

        $booking = Booking::create($data);

        ActivityLog::record("Admin membuat booking manual: {$booking->booking_code}");

        return redirect()->route('admin.bookings.index')->with('success', 'Booking manual berhasil dibuat dengan kode: ' . $booking->booking_code);
    }

    /**
    * Show a form to manually create an invoice for any booking.
    */
    public function showCreateManualInvoiceForm()
    {
        $bookings = Booking::all();
        $categories = [
            'wisata' => 'Wisata',
            'transport' => 'Sewa Transportasi',
            'akomodasi' => 'Akomodasi',
        ];
        return view('admin.bookings.manual-invoice-create', compact('bookings', 'categories'));
    }

    /**
    * Store a manually created invoice for a selected booking.
    */
    public function storeCreateManualInvoice(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'invoice_category' => 'required|string|in:wisata,transport,akomodasi',
        ]);
        $booking = Booking::findOrFail($data['booking_id']);
        if (!$booking->invoice_number) {
            $booking->update(['invoice_number' => Booking::generateInvoiceNumber()]);
        }
        $booking->update(['invoice_category' => $data['invoice_category']]);
        ActivityLog::record("Manual invoice created for {$booking->booking_code}");
        return redirect()->route('admin.bookings.invoice', $booking);
    }


    /**
     * Show form for manual invoice creation (admin).
     */
    public function showManualInvoiceForm(Booking $booking)
    {
        // Only allow if invoice does not exist yet
        abort_if($booking->invoice_number, 403, 'Invoice already exists.');
        $categories = [
            'wisata' => 'Wisata',
            'transport' => 'Sewa Transportasi',
            'akomodasi' => 'Akomodasi',
        ];
        return view('admin.bookings.manual-invoice', compact('booking', 'categories'));
    }

    /**
     * Store manual invoice data and generate PDF.
     */
    public function storeManualInvoice(Request $request, Booking $booking)
    {
        $request->validate([
            'invoice_category' => 'required|string|in:wisata,transport,akomodasi',
        ]);
        // Generate invoice number if missing
        if (!$booking->invoice_number) {
            $booking->update([
                'invoice_number' => Booking::generateInvoiceNumber(),
                'invoice_category' => $request->invoice_category,
            ]);
        }
        ActivityLog::record("Manual invoice generated for {$booking->booking_code}");
        // Redirect to PDF download (reuse existing method)
        return redirect()->route('admin.bookings.invoice', $booking);
    }

    /**
     * Generate a manual invoice for a booking that does not yet have one.
     * This is used for bookings created outside the normal web flow (e.g., via chat).
     * It creates an invoice number if missing and returns the PDF.
     */
    public function manualInvoice(Request $request, Booking $booking)
    {
        // Legacy endpoint – keep for compatibility
        if (!$booking->invoice_number) {
            $booking->update(['invoice_number' => Booking::generateInvoiceNumber()]);
        }
        ActivityLog::record("Manual invoice generated for {$booking->booking_code}");
        return $this->downloadInvoice($booking);
    }

/**
     * List bookings that do not have an invoice yet.
     */
    public function manualInvoiceList()
    {
        $bookings = Booking::whereNull('invoice_number')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings.manual-invoice-list', compact('bookings'));
    }
}


