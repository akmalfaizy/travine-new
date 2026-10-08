<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Offering;
use App\Models\OfferingCategory;
use App\Models\OfferingItem;
use App\Models\OfferingMasterItem;
use App\Models\ProjectInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OfferingProposalMail;

class OfferingController extends Controller
{
    // ─── Listing ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Offering::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('offering_number', 'like', "%{$request->search}%")
                  ->orWhere('client_name', 'like', "%{$request->search}%")
                  ->orWhere('package_name', 'like', "%{$request->search}%");
            });
        }

        $offerings = $query->paginate(20)->withQueryString();
        $statuses  = ['draft', 'sent', 'revised', 'approved', 'rejected', 'expired'];

        return view('admin.offerings.index', compact('offerings', 'statuses'));
    }

    // ─── Create / Store ────────────────────────────────────────────────────────

    public function create()
    {
        $masterItems   = OfferingMasterItem::orderBy('category_name')->orderBy('component')->get();
        $masterByCategory = $masterItems->groupBy('category_name');
        $defaultCategories = $masterItems->pluck('category_name')->unique()->values();

        return view('admin.offerings.create', compact('masterByCategory', 'defaultCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name'                => 'required|string|max:255',
            'client_email'               => 'nullable|email|max:255',
            'provider'                   => 'nullable|string|max:255',
            'pic_client'                 => 'nullable|string|max:255',
            'pic_project'                => 'nullable|string|max:255',
            'pic_contact'                => 'nullable|string|max:50',
            'package_name'               => 'required|string|max:255',
            'date_duration'              => 'nullable|string|max:255',
            'participants'               => 'required|integer|min:1',
            'description'                => 'nullable|string',
            'other_cost_percentage'      => 'required|numeric|min:0|max:100',
            'management_fee_percentage'  => 'required|numeric|min:0|max:100',
            'notes'                      => 'nullable|array',
            'notes.*'                    => 'nullable|string|max:500',
            'revision_notes'             => 'nullable|array',
            'revision_notes.*.text'      => 'nullable|string',
            'recommendation'             => 'nullable|string',
            // categories
            'categories'                 => 'nullable|array',
            'categories.*.name'          => 'nullable|string|max:255',
            'categories.*.sort_order'    => 'nullable|integer',
            'categories.*.items'         => 'nullable|array',
            'categories.*.items.*.component'   => 'nullable|string|max:255',
            'categories.*.items.*.description' => 'nullable|string',
            'categories.*.items.*.remark'      => 'nullable|string|max:255',
            'categories.*.items.*.type'        => 'nullable|string|max:20',
            'categories.*.items.*.quantity'    => 'nullable|numeric|min:0',
            'categories.*.items.*.unit_price'  => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Compute financials server-side
            $financials = $this->computeFinancials(
                $validated['categories'] ?? [],
                $validated['participants'],
                (float) $validated['other_cost_percentage'],
                (float) $validated['management_fee_percentage']
            );

            // 2. Clean up notes (remove empty)
            $notes = array_values(array_filter($validated['notes'] ?? [], fn($n) => !empty(trim($n ?? ''))));
            $revisionNotes = array_values(array_filter($validated['revision_notes'] ?? [], fn($n) => !empty(trim($n['text'] ?? ''))));

            // 3. Create offering
            $offering = Offering::create([
                'offering_number'           => Offering::generateOfferingNumber(),
                'client_name'               => $validated['client_name'],
                'client_email'              => $validated['client_email'] ?? null,
                'provider'                  => $validated['provider'] ?? 'CV. Nara Tour',
                'pic_client'                => $validated['pic_client'] ?? null,
                'pic_project'               => $validated['pic_project'] ?? null,
                'pic_contact'               => $validated['pic_contact'] ?? null,
                'package_name'              => $validated['package_name'],
                'date_duration'             => $validated['date_duration'] ?? null,
                'participants'              => $validated['participants'],
                'description'               => $validated['description'] ?? null,
                'other_cost_percentage'     => $financials['other_cost_percentage'],
                'management_fee_percentage' => $financials['management_fee_percentage'],
                'production_cost'           => $financials['production_cost'],
                'other_cost'                => $financials['other_cost'],
                'management_fee'            => $financials['management_fee'],
                'total_amount'              => $financials['total_amount'],
                'price_per_pack'            => $financials['price_per_pack'],
                'notes'                     => $notes,
                'revision_notes'            => $revisionNotes,
                'recommendation'            => $validated['recommendation'] ?? null,
                'status'                    => 'draft',
            ]);

            // 4. Create categories + items
            $this->syncCategoriesAndItems($offering, $validated['categories'] ?? []);

            ActivityLog::record("Membuat Offering: {$offering->offering_number}");
        });

        return redirect()->route('admin.offerings.index')
            ->with('success', 'Offering berhasil dibuat.');
    }

    // ─── Show / Detail ─────────────────────────────────────────────────────────

    public function show(Offering $offering)
    {
        $offering->load(['categories.items', 'invoices']);
        return view('admin.offerings.show', compact('offering'));
    }

    // ─── Edit / Update ─────────────────────────────────────────────────────────

    public function edit(Offering $offering)
    {
        // Only draft, sent, revised can be edited
        if (!in_array($offering->status, ['draft', 'sent', 'revised'])) {
            return redirect()->route('admin.offerings.show', $offering)
                ->with('error', 'Offering dengan status "' . $offering->status . '" tidak dapat diedit.');
        }

        $offering->load('categories.items');
        $masterItems      = OfferingMasterItem::orderBy('category_name')->orderBy('component')->get();
        $masterByCategory = $masterItems->groupBy('category_name');
        $defaultCategories = $masterItems->pluck('category_name')->unique()->values();

        return view('admin.offerings.edit', compact('offering', 'masterByCategory', 'defaultCategories'));
    }

    public function update(Request $request, Offering $offering)
    {
        if (!in_array($offering->status, ['draft', 'sent', 'revised'])) {
            return redirect()->route('admin.offerings.show', $offering)
                ->with('error', 'Offering ini tidak dapat diubah.');
        }

        $validated = $request->validate([
            'client_name'                => 'required|string|max:255',
            'client_email'               => 'nullable|email|max:255',
            'provider'                   => 'nullable|string|max:255',
            'pic_client'                 => 'nullable|string|max:255',
            'pic_project'               => 'nullable|string|max:255',
            'pic_contact'               => 'nullable|string|max:50',
            'package_name'               => 'required|string|max:255',
            'date_duration'              => 'nullable|string|max:255',
            'participants'               => 'required|integer|min:1',
            'description'                => 'nullable|string',
            'other_cost_percentage'      => 'required|numeric|min:0|max:100',
            'management_fee_percentage'  => 'required|numeric|min:0|max:100',
            'notes'                      => 'nullable|array',
            'notes.*'                    => 'nullable|string|max:500',
            'revision_notes'             => 'nullable|array',
            'revision_notes.*.text'      => 'nullable|string',
            'recommendation'             => 'nullable|string',
            'categories'                 => 'nullable|array',
            'categories.*.name'          => 'nullable|string|max:255',
            'categories.*.sort_order'    => 'nullable|integer',
            'categories.*.items'         => 'nullable|array',
            'categories.*.items.*.component'   => 'nullable|string|max:255',
            'categories.*.items.*.description' => 'nullable|string',
            'categories.*.items.*.remark'      => 'nullable|string|max:255',
            'categories.*.items.*.type'        => 'nullable|string|max:20',
            'categories.*.items.*.quantity'    => 'nullable|numeric|min:0',
            'categories.*.items.*.unit_price'  => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $offering) {
            $financials = $this->computeFinancials(
                $validated['categories'] ?? [],
                $validated['participants'],
                (float) $validated['other_cost_percentage'],
                (float) $validated['management_fee_percentage']
            );

            $notes = array_values(array_filter($validated['notes'] ?? [], fn($n) => !empty(trim($n ?? ''))));
            $revisionNotes = array_values(array_filter($validated['revision_notes'] ?? [], fn($n) => !empty(trim($n['text'] ?? ''))));

            $offering->update([
                'client_name'               => $validated['client_name'],
                'client_email'              => $validated['client_email'] ?? null,
                'provider'                  => $validated['provider'] ?? 'CV. Nara Tour',
                'pic_client'                => $validated['pic_client'] ?? null,
                'pic_project'               => $validated['pic_project'] ?? null,
                'pic_contact'               => $validated['pic_contact'] ?? null,
                'package_name'              => $validated['package_name'],
                'date_duration'             => $validated['date_duration'] ?? null,
                'participants'              => $validated['participants'],
                'description'               => $validated['description'] ?? null,
                'other_cost_percentage'     => $financials['other_cost_percentage'],
                'management_fee_percentage' => $financials['management_fee_percentage'],
                'production_cost'           => $financials['production_cost'],
                'other_cost'                => $financials['other_cost'],
                'management_fee'            => $financials['management_fee'],
                'total_amount'              => $financials['total_amount'],
                'price_per_pack'            => $financials['price_per_pack'],
                'notes'                     => $notes,
                'revision_notes'            => $revisionNotes,
                'recommendation'            => $validated['recommendation'] ?? null,
            ]);

            // Delete old categories (cascades to items)
            $offering->categories()->delete();
            $this->syncCategoriesAndItems($offering, $validated['categories'] ?? []);

            ActivityLog::record("Edit Offering: {$offering->offering_number}");
        });

        return redirect()->route('admin.offerings.show', $offering)
            ->with('success', 'Offering berhasil diperbarui.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(Offering $offering)
    {
        if ($offering->invoices()->count()) {
            return back()->with('error', 'Tidak dapat menghapus Offering yang sudah memiliki Invoice.');
        }
        ActivityLog::record("Hapus Offering: {$offering->offering_number}");
        $offering->delete();
        return redirect()->route('admin.offerings.index')
            ->with('success', 'Offering berhasil dihapus.');
    }

    // ─── Status Transitions ───────────────────────────────────────────────────

    /**
     * Update offering status (with state-machine validation).
     * Allowed transitions:
     *   draft   → sent
     *   sent    → revised | approved | rejected | expired
     *   revised → sent | approved | rejected
     */
    public function updateStatus(Request $request, Offering $offering)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,revised,approved,rejected,expired',
        ]);

        $allowed = [
            'draft'   => ['sent'],
            'sent'    => ['revised', 'approved', 'rejected', 'expired'],
            'revised' => ['sent', 'approved', 'rejected'],
        ];

        $current = $offering->status;
        $new     = $request->status;

        if (!in_array($new, $allowed[$current] ?? [])) {
            return back()->with('error', "Tidak bisa mengubah status dari \"{$current}\" ke \"{$new}\".");
        }

        $offering->update(['status' => $new]);
        ActivityLog::record("Update status Offering {$offering->offering_number}: {$current} → {$new}");

        return back()->with('success', "Status Offering berhasil diubah menjadi \"{$new}\".");
    }

    // ─── PDF Download ─────────────────────────────────────────────────────────

    public function downloadProposal(Offering $offering)
    {
        $offering->load(['categories.items']);
        $pdf = Pdf::loadView('pdf.offering-proposal', compact('offering'))
                  ->setPaper('a4', 'portrait');
        $filename = 'Offering-' . str_replace(['/', '\\'], '-', $offering->offering_number) . '.pdf';
        return $pdf->download($filename);
    }

    // ─── Send Email ────────────────────────────────────────────────────────────

    public function sendEmail(Request $request, Offering $offering)
    {
        $request->validate([
            'recipient_email' => 'required|email|max:255',
            'email_message'   => 'nullable|string|max:2000',
            'cc_emails'       => 'nullable|string|max:2000',
        ]);

        $recipientEmail = $request->recipient_email;

        // Parse CC emails from comma/semicolon-separated string
        $ccEmails = [];
        if ($request->filled('cc_emails')) {
            $ccEmails = array_values(array_filter(
                array_map('trim', preg_split('/[,;]+/', $request->cc_emails)),
                fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)
            ));
        }

        // If client_email in record was empty, update it
        if (empty($offering->client_email)) {
            $offering->update(['client_email' => $recipientEmail]);
        }

        // Extend time limit — PDF generation + SMTP can exceed 30s
        set_time_limit(120);

        // Generate proposal PDF
        $offering->load(['categories.items']);
        $pdf = Pdf::loadView('pdf.offering-proposal', compact('offering'))
                  ->setPaper('a4', 'portrait');
        $pdfData = $pdf->output();

        // Send Email
        Mail::to($recipientEmail)->send(new OfferingProposalMail($offering, $pdfData, $request->email_message, $ccEmails));

        // Advance draft to sent
        if ($offering->status === 'draft') {
            $offering->update(['status' => 'sent']);
        }

        $ccNote = count($ccEmails) ? ' | CC: ' . implode(', ', $ccEmails) : '';
        ActivityLog::record("Kirim Proposal Offering {$offering->offering_number} via email ke: {$recipientEmail}{$ccNote}");

        return back()->with('success', "Proposal penawaran berhasil dikirim via email ke {$recipientEmail}" . (count($ccEmails) ? ' + ' . count($ccEmails) . ' CC' : '') . '.');
    }

    // ─── Invoice Conversion ───────────────────────────────────────────────────

    public function createInvoice(Offering $offering)
    {
        if ($offering->status !== 'approved') {
            return redirect()->route('admin.offerings.show', $offering)
                ->with('error', 'Hanya Offering berstatus Approved yang dapat dikonversi menjadi Invoice.');
        }

        $offering->load('categories.items');
        return view('admin.project-invoices.create', compact('offering'));
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Calculate all financials server-side.
     * Formula:
     *   production_cost  = SUM(qty × unit_price) for all items
     *   other_cost       = production_cost × other_cost_pct%
     *   management_fee   = production_cost × management_fee_pct%   (NOT from subtotal+other)
     *   total_amount     = production_cost + other_cost + management_fee
     *   price_per_pack   = total_amount / participants
     */
    private function computeFinancials(array $categories, int $participants, float $otherPct, float $mgmtPct): array
    {
        $productionCost = 0.0;

        foreach ($categories as $cat) {
            foreach ($cat['items'] ?? [] as $item) {
                $qtyRaw   = $item['quantity'] ?? null;
                $priceRaw = $item['unit_price'] ?? null;

                if ($qtyRaw !== null && $qtyRaw !== '' && $priceRaw !== null && $priceRaw !== '') {
                    $qty   = max(0, (float) $qtyRaw);
                    $price = max(0, (float) $priceRaw);
                    $productionCost += round($qty * $price, 2);
                }
            }
        }

        $otherCost     = round($productionCost * ($otherPct / 100), 2);
        $managementFee = round($productionCost * ($mgmtPct / 100), 2);
        $totalAmount   = $productionCost + $otherCost + $managementFee;
        $pricePerPack  = $participants > 0 ? round($totalAmount / $participants, 2) : 0;

        return [
            'production_cost'           => $productionCost,
            'other_cost_percentage'     => $otherPct,
            'other_cost'                => $otherCost,
            'management_fee_percentage' => $mgmtPct,
            'management_fee'            => $managementFee,
            'total_amount'              => $totalAmount,
            'price_per_pack'            => $pricePerPack,
        ];
    }

    private function syncCategoriesAndItems(Offering $offering, array $categories): void
    {
        foreach ($categories as $sortIndex => $catData) {
            $catName   = trim($catData['name'] ?? '');
            $itemsData = $catData['items'] ?? [];

            // If category has no name and no items, skip it
            if ($catName === '' && empty($itemsData)) {
                continue;
            }

            $cat = OfferingCategory::create([
                'offering_id' => $offering->id,
                'name'        => $catName !== '' ? $catName : 'Kategori ' . ($sortIndex + 1),
                'sort_order'  => (int) ($catData['sort_order'] ?? $sortIndex),
            ]);

            foreach ($itemsData as $itemSortIndex => $itemData) {
                $component   = isset($itemData['component']) && trim($itemData['component']) !== '' ? trim($itemData['component']) : null;
                $description = isset($itemData['description']) && trim($itemData['description']) !== '' ? trim($itemData['description']) : null;
                $remark      = isset($itemData['remark']) && trim($itemData['remark']) !== '' ? trim($itemData['remark']) : null;
                $type        = isset($itemData['type']) && trim($itemData['type']) !== '' ? trim($itemData['type']) : null;

                $qtyRaw   = $itemData['quantity'] ?? null;
                $priceRaw = $itemData['unit_price'] ?? null;

                $hasQty   = ($qtyRaw !== null && $qtyRaw !== '');
                $hasPrice = ($priceRaw !== null && $priceRaw !== '');

                $qty   = $hasQty ? max(0, (float) $qtyRaw) : null;
                $price = $hasPrice ? max(0, (float) $priceRaw) : null;

                $totalCost = ($hasQty && $hasPrice) ? round($qty * $price, 2) : null;

                OfferingItem::create([
                    'offering_category_id' => $cat->id,
                    'component'            => $component,
                    'description'          => $description,
                    'remark'               => $remark,
                    'type'                 => $type,
                    'quantity'             => $qty,
                    'unit_price'           => $price,
                    'total_cost'           => $totalCost,
                    'sort_order'           => (int) ($itemData['sort_order'] ?? $itemSortIndex),
                ]);
            }
        }
    }
}
