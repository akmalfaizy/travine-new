<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Offering;
use App\Models\ProjectInvoice;
use App\Models\ProjectInvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectInvoiceMail;

class ProjectInvoiceController extends Controller
{
    // ─── Listing ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = ProjectInvoice::with('offering')->latest();

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                  ->orWhere('client_name', 'like', "%{$request->search}%")
                  ->orWhere('project_name', 'like', "%{$request->search}%");
            });
        }

        $invoices = $query->paginate(20)->withQueryString();
        $statuses = ['unpaid', 'partial', 'paid', 'overdue', 'cancelled'];

        return view('admin.project-invoices.index', compact('invoices', 'statuses'));
    }

    // ─── Create from Offering (snapshot) ──────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'offering_id'          => 'required|exists:offerings,id',
            'client_email'         => 'nullable|email|max:255',
            'issue_date'           => 'required|date',
            'due_date'             => 'required|date|after_or_equal:issue_date',
            'pph_percentage'       => 'required|numeric|min:0|max:100',
            'include_pph_deduction'=> 'nullable|boolean',
            'bank_name'            => 'nullable|string|max:100',
            'bank_account'         => 'nullable|string|max:100',
            'bank_holder'          => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
        ]);

        $offering = Offering::with('categories.items')->findOrFail($validated['offering_id']);

        if ($offering->status !== 'approved') {
            return back()->with('error', 'Hanya Offering Approved yang dapat dikonversi ke Invoice.');
        }

        DB::transaction(function () use ($validated, $offering) {
            $grossTotal  = (float) $offering->total_amount;
            $pphPct      = (float) $validated['pph_percentage'];
            $pphAmount   = round($grossTotal * ($pphPct / 100), 2);
            $netAmount   = round($grossTotal - $pphAmount, 2);
            $includePph  = (bool) ($validated['include_pph_deduction'] ?? false);

            $invoice = ProjectInvoice::create([
                'invoice_number'       => ProjectInvoice::generateInvoiceNumber(),
                'offering_id'          => $offering->id,
                'client_name'          => $offering->client_name,
                'client_email'         => !empty($validated['client_email']) ? $validated['client_email'] : $offering->client_email,
                'client_company'       => $offering->client_name,
                'pic_client'           => $offering->pic_client,
                'pic_contact'          => $offering->pic_contact,
                'project_name'         => $offering->package_name,
                'description'          => $offering->description,
                'issue_date'           => $validated['issue_date'],
                'due_date'             => $validated['due_date'],
                'subtotal'             => $offering->production_cost,
                'other_cost'           => $offering->other_cost,
                'management_fee'       => $offering->management_fee,
                'gross_total'          => $grossTotal,
                'pph_percentage'       => $pphPct,
                'pph_amount'           => $pphAmount,
                'net_amount'           => $netAmount,
                'include_pph_deduction'=> $includePph,
                'amount_paid'          => 0,
                'remaining_balance'    => $grossTotal,
                'payment_status'       => 'unpaid',
                'bank_name'            => $validated['bank_name'] ?? null,
                'bank_account'         => $validated['bank_account'] ?? null,
                'bank_holder'          => $validated['bank_holder'] ?? null,
                'notes'                => $validated['notes'] ?? null,
            ]);

            // Snapshot items from offering (immutability)
            foreach ($offering->categories as $cat) {
                foreach ($cat->items as $item) {
                    ProjectInvoiceItem::create([
                        'project_invoice_id' => $invoice->id,
                        'category_name'      => $cat->name,
                        'component'          => $item->component,
                        'description'        => $item->description,
                        'remark'             => $item->remark,
                        'type'               => $item->type,
                        'quantity'           => $item->quantity,
                        'unit_price'         => $item->unit_price,
                        'total_cost'         => $item->total_cost,
                        'sort_order'         => $item->sort_order,
                    ]);
                }
            }

            ActivityLog::record("Membuat Project Invoice {$invoice->invoice_number} dari Offering {$offering->offering_number}");
        });

        return redirect()->route('admin.project-invoices.index')
            ->with('success', 'Invoice berhasil dibuat dari Offering.');
    }

    // ─── Show / Detail ─────────────────────────────────────────────────────────

    public function show(ProjectInvoice $projectInvoice)
    {
        $projectInvoice->load(['offering', 'items']);
        return view('admin.project-invoices.show', compact('projectInvoice'));
    }

    // ─── Payment Recording ─────────────────────────────────────────────────────

    public function recordPayment(Request $request, ProjectInvoice $projectInvoice)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $amountPaid = (float) $request->amount_paid;
        $grossTotal = (float) $projectInvoice->gross_total;

        // Clamp to gross total
        $amountPaid = min($amountPaid, $grossTotal);

        $remaining = max(0, round($grossTotal - $amountPaid, 2));

        // Auto-determine payment status
        $status = 'unpaid';
        if ($projectInvoice->payment_status === 'cancelled') {
            return back()->with('error', 'Invoice yang dibatalkan tidak dapat menerima pembayaran.');
        }
        if ($amountPaid <= 0) {
            $status = now()->gt($projectInvoice->due_date) ? 'overdue' : 'unpaid';
        } elseif ($remaining <= 0) {
            $status = 'paid';
        } else {
            $status = now()->gt($projectInvoice->due_date) ? 'overdue' : 'partial';
        }

        $projectInvoice->update([
            'amount_paid'       => $amountPaid,
            'remaining_balance' => $remaining,
            'payment_status'    => $status,
        ]);

        ActivityLog::record("Rekam pembayaran Invoice {$projectInvoice->invoice_number}: Rp " . number_format($amountPaid, 0, ',', '.'));

        return back()->with('success', 'Pembayaran berhasil direkam.');
    }

    // ─── Update Status ─────────────────────────────────────────────────────────

    public function updateStatus(Request $request, ProjectInvoice $projectInvoice)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,partial,paid,overdue,cancelled',
        ]);

        $projectInvoice->update(['payment_status' => $request->payment_status]);
        ActivityLog::record("Update status Invoice {$projectInvoice->invoice_number}: {$request->payment_status}");

        return back()->with('success', 'Status Invoice berhasil diperbarui.');
    }

    // ─── PDF Download ─────────────────────────────────────────────────────────

    public function downloadPdf(ProjectInvoice $projectInvoice)
    {
        $projectInvoice->load(['offering', 'items']);
        $pdf = Pdf::loadView('pdf.project-invoice', compact('projectInvoice'))
                  ->setPaper('a4', 'portrait');
        $filename = 'Invoice-' . str_replace(['/', '\\'], '-', $projectInvoice->invoice_number) . '.pdf';
        return $pdf->download($filename);
    }

    // ─── Send Email ────────────────────────────────────────────────────────────

    public function sendEmail(Request $request, ProjectInvoice $projectInvoice)
    {
        $request->validate([
            'recipient_email' => 'required|email|max:255',
            'email_message'   => 'nullable|string|max:2000',
        ]);

        $recipientEmail = $request->recipient_email;

        // If client_email in record was empty, update it
        if (empty($projectInvoice->client_email)) {
            $projectInvoice->update(['client_email' => $recipientEmail]);
        }

        // Generate invoice PDF
        $projectInvoice->load(['offering', 'items']);
        $pdf = Pdf::loadView('pdf.project-invoice', compact('projectInvoice'))
                  ->setPaper('a4', 'portrait');
        $pdfData = $pdf->output();

        // Send Email
        Mail::to($recipientEmail)->send(new ProjectInvoiceMail($projectInvoice, $pdfData, $request->email_message));

        ActivityLog::record("Kirim Project Invoice {$projectInvoice->invoice_number} ({$projectInvoice->payment_status}) via email ke: {$recipientEmail}");

        return back()->with('success', "Invoice berhasil dikirim via email ke {$recipientEmail}.");
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(ProjectInvoice $projectInvoice)
    {
        ActivityLog::record("Hapus Project Invoice: {$projectInvoice->invoice_number}");
        $projectInvoice->delete();
        return redirect()->route('admin.project-invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }
}
