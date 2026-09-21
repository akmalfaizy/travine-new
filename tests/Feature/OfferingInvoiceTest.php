<?php

namespace Tests\Feature;

use App\Models\Offering;
use App\Models\OfferingCategory;
use App\Models\OfferingItem;
use App\Models\OfferingMasterItem;
use App\Models\ProjectInvoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferingInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@travine.id',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    public function test_offering_creation_with_financial_calculations(): void
    {
        $this->actingAs($this->adminUser);

        $offeringData = [
            'client_name'               => 'PT Multitrada Arah Sarana',
            'provider'                  => 'travine.id',
            'pic_client'                => 'Hendrawan',
            'pic_project'               => 'Rifqi Pratama',
            'pic_contact'               => '081298765432',
            'package_name'              => 'Corporate Gathering Bandung 3D2N',
            'date_duration'             => '20-22 November 2026',
            'participants'              => 50,
            'description'               => 'Gathering tahunan divisi engineering',
            'other_cost_percentage'     => 5,
            'management_fee_percentage' => 15,
            'status'                    => 'draft',
            'notes'                     => ['DP 50% saat penandatanganan penawaran', 'Pelunasan H-7 keberangkatan'],
            'categories'                => [
                [
                    'name'  => 'Transportasi & Armada',
                    'items' => [
                        [
                            'component'   => 'Big Bus Pariwisata 59 Seat',
                            'description' => '1 unit executive bus include BBM & Tol',
                            'remark'      => 'Include Driver',
                            'type'        => 'F',
                            'quantity'    => 1,
                            'unit_price'  => 12000000,
                        ],
                    ],
                ],
                [
                    'name'  => 'Akomodasi & Hotel',
                    'items' => [
                        [
                            'component'   => 'Hotel Grand Mercure Setiabudi',
                            'description' => 'Twin share room include breakfast 2 malam',
                            'remark'      => '25 kamar x 2 malam',
                            'type'        => 'V',
                            'quantity'    => 50,
                            'unit_price'  => 800000,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->post(route('admin.offerings.store'), $offeringData);
        $response->assertRedirect();

        $offering = Offering::with(['categories.items'])->first();
        $this->assertNotNull($offering);
        $this->assertStringStartsWith('TRV/OFF/', $offering->offering_number);

        // Verify calculations:
        // Bus: 1 * 12,000,000 = 12,000,000
        // Hotel: 50 * 800,000 = 40,000,000
        // Production Cost = 52,000,000
        $expectedProductionCost = 52000000;
        $expectedOtherCost = $expectedProductionCost * 0.05; // 2,600,000
        $expectedManagementFee = $expectedProductionCost * 0.15; // 7,800,000
        $expectedGrandTotal = $expectedProductionCost + $expectedOtherCost + $expectedManagementFee; // 62,400,000
        $expectedPricePerPack = $expectedGrandTotal / 50; // 1,248,000

        $this->assertEquals($expectedProductionCost, $offering->production_cost);
        $this->assertEquals($expectedOtherCost, $offering->other_cost);
        $this->assertEquals($expectedManagementFee, $offering->management_fee);
        $this->assertEquals($expectedGrandTotal, $offering->total_amount);
        $this->assertEquals($expectedPricePerPack, $offering->price_per_pack);
    }

    public function test_offering_status_workflow(): void
    {
        $this->actingAs($this->adminUser);

        $offering = Offering::create([
            'offering_number'           => Offering::generateOfferingNumber(),
            'client_name'               => 'Test Client',
            'package_name'              => 'Test Package',
            'participants'              => 20,
            'production_cost'           => 10000000,
            'other_cost_percentage'     => 5,
            'other_cost'                => 500000,
            'management_fee_percentage' => 15,
            'management_fee'            => 1500000,
            'total_amount'              => 12000000,
            'price_per_pack'            => 600000,
            'status'                    => 'draft',
        ]);

        // Draft -> Sent
        $response = $this->post(route('admin.offerings.status', $offering), ['status' => 'sent']);
        $response->assertRedirect();
        $this->assertEquals('sent', $offering->fresh()->status);

        // Sent -> Approved
        $response = $this->post(route('admin.offerings.status', $offering), ['status' => 'approved']);
        $response->assertRedirect();
        $this->assertEquals('approved', $offering->fresh()->status);
    }

    public function test_offering_proposal_pdf_generation(): void
    {
        $this->actingAs($this->adminUser);

        $offering = Offering::create([
            'offering_number'           => Offering::generateOfferingNumber(),
            'client_name'               => 'PT Solusi Teknologi Nusantara',
            'provider'                  => 'travine.id',
            'package_name'              => 'Outbound Bromo & Malang',
            'participants'              => 35,
            'production_cost'           => 30000000,
            'other_cost_percentage'     => 5,
            'other_cost'                => 1500000,
            'management_fee_percentage' => 15,
            'management_fee'            => 4500000,
            'total_amount'              => 36000000,
            'price_per_pack'            => 36000000 / 35,
            'status'                    => 'approved',
            'notes'                     => ['Harga belum termasuk tip driver opsional', 'Validitas penawaran 14 hari kerja'],
            'recommendation'            => 'Disarankan membawa pakaian hangat untuk Bromo sunrise.',
        ]);

        $category = $offering->categories()->create(['name' => 'Transportasi & Armada', 'sort_order' => 1]);
        $category->items()->create([
            'component'   => 'Toyota Hiace Premio',
            'description' => '3 unit armada standard pariwisata',
            'remark'      => 'Include BBM & Driver',
            'type'        => 'V',
            'quantity'    => 3,
            'unit_price'  => 5000000,
            'total_cost'  => 15000000,
            'sort_order'  => 1,
        ]);

        $response = $this->get(route('admin.offerings.proposal-pdf', $offering));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_convert_approved_offering_to_project_invoice(): void
    {
        $this->actingAs($this->adminUser);

        $offering = Offering::create([
            'offering_number'           => Offering::generateOfferingNumber(),
            'client_name'               => 'PT Karya Utama',
            'pic_client'                => 'Siti Rahmawati',
            'pic_contact'               => '081122334455',
            'package_name'              => 'Gathering Jogja Istimewa',
            'participants'              => 40,
            'production_cost'           => 40000000,
            'other_cost_percentage'     => 5,
            'other_cost'                => 2000000,
            'management_fee_percentage' => 15,
            'management_fee'            => 6000000,
            'total_amount'              => 48000000,
            'price_per_pack'            => 1200000,
            'status'                    => 'approved',
        ]);

        $cat1 = $offering->categories()->create(['name' => 'Transportasi', 'sort_order' => 1]);
        $cat1->items()->create([
            'component'   => 'Medium Bus Pariwisata',
            'description' => '2 unit medium bus',
            'remark'      => 'Included (Wajib)',
            'type'        => 'F',
            'quantity'    => 2,
            'unit_price'  => 10000000,
            'total_cost'  => 20000000,
            'sort_order'  => 1,
        ]);

        $cat2 = $offering->categories()->create(['name' => 'Destinasi & Tiket Wisata', 'sort_order' => 2]);
        $cat2->items()->create([
            'component'   => 'Tiket Candi Prambanan + Lava Tour Merapi',
            'description' => 'All access pass & Jeep Merapi',
            'remark'      => 'Included (Wajib)',
            'type'        => 'V',
            'quantity'    => 40,
            'unit_price'  => 500000,
            'total_cost'  => 20000000,
            'sort_order'  => 1,
        ]);

        // Convert to Invoice via POST
        $invoiceData = [
            'offering_id'           => $offering->id,
            'client_company'        => 'PT Karya Utama Holding',
            'pic_client'            => 'Siti Rahmawati',
            'pic_contact'           => '081122334455',
            'project_name'          => 'Gathering Jogja Istimewa',
            'description'           => 'Invoice resmi paket gathering Jogja',
            'issue_date'            => now()->toDateString(),
            'due_date'              => now()->addDays(14)->toDateString(),
            'pph_percentage'        => 2,
            'include_pph_deduction' => 1,
            'bank_name'             => 'BCA (Bank Central Asia)',
            'bank_account'          => '842-089-1234',
            'bank_holder'           => 'PT Travine Nusantara',
            'notes'                 => 'Harap konfirmasi setelah melakukan pembayaran.',
        ];

        $response = $this->post(route('admin.project-invoices.store'), $invoiceData);
        $response->assertRedirect();

        $invoice = ProjectInvoice::with('items')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals($offering->id, $invoice->offering_id);
        $this->assertEquals(48000000, $invoice->gross_total);
        $this->assertEquals(960000, $invoice->pph_amount); // 2% of 48,000,000
        $this->assertEquals(47040000, $invoice->net_amount);
        $this->assertEquals(0, $invoice->amount_paid);
        $this->assertEquals(48000000, $invoice->remaining_balance);
        $this->assertEquals('unpaid', $invoice->payment_status);

        // Snapshot items count
        $this->assertCount(2, $invoice->items);

        // Check Invoice PDF download
        $pdfResponse = $this->get(route('admin.project-invoices.pdf', $invoice));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }

    public function test_invoice_payment_recording_and_status_progression(): void
    {
        $this->actingAs($this->adminUser);

        $invoice = ProjectInvoice::create([
            'invoice_number'        => ProjectInvoice::generateInvoiceNumber(),
            'client_name'           => 'Client Demo',
            'client_company'        => 'Client Demo PT',
            'project_name'          => 'Demo Project',
            'issue_date'            => now(),
            'due_date'              => now()->addDays(7),
            'subtotal'              => 20000000,
            'other_cost'            => 1000000,
            'management_fee'        => 3000000,
            'gross_total'           => 24000000,
            'pph_percentage'        => 0,
            'pph_amount'            => 0,
            'net_amount'            => 24000000,
            'include_pph_deduction' => false,
            'amount_paid'           => 0,
            'remaining_balance'     => 24000000,
            'payment_status'        => 'unpaid',
        ]);

        // 1. Partial payment: DP 10,000,000
        $response = $this->post(route('admin.project-invoices.payment', $invoice), [
            'amount_paid' => 10000000,
        ]);
        $response->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(10000000, $invoice->amount_paid);
        $this->assertEquals(14000000, $invoice->remaining_balance);
        $this->assertEquals('partial', $invoice->payment_status);

        // 2. Full settlement: Remaining 14,000,000 added (total amount_paid becomes 24,000,000)
        $response = $this->post(route('admin.project-invoices.payment', $invoice), [
            'amount_paid' => 24000000,
        ]);
        $response->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(24000000, $invoice->amount_paid);
        $this->assertEquals(0, $invoice->remaining_balance);
        $this->assertEquals('paid', $invoice->payment_status);
    }

    public function test_send_offering_proposal_via_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $this->actingAs($this->adminUser);

        $offering = Offering::create([
            'offering_number'           => Offering::generateOfferingNumber(),
            'client_name'               => 'PT Email Test Corp',
            'client_email'              => 'procurement@emailtest.com',
            'package_name'              => 'Paket Gathering Anyer',
            'participants'              => 25,
            'production_cost'           => 15000000,
            'other_cost_percentage'     => 5,
            'other_cost'                => 750000,
            'management_fee_percentage' => 15,
            'management_fee'            => 2250000,
            'total_amount'              => 18000000,
            'price_per_pack'            => 720000,
            'status'                    => 'draft',
        ]);

        $cat = $offering->categories()->create(['name' => 'Transportasi', 'sort_order' => 1]);
        $cat->items()->create([
            'component'   => 'Hiace Commuter',
            'description' => '2 unit',
            'type'        => 'V',
            'quantity'    => 2,
            'unit_price'  => 7500000,
            'total_cost'  => 15000000,
            'sort_order'  => 1,
        ]);

        $response = $this->post(route('admin.offerings.send-email', $offering), [
            'recipient_email' => 'client@emailtest.com',
            'email_message'   => 'Mohon review penawaran terlampir.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check status updated to sent
        $offering->refresh();
        $this->assertEquals('sent', $offering->status);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\OfferingProposalMail::class, function ($mail) use ($offering) {
            return $mail->hasTo('client@emailtest.com') &&
                   $mail->offering->id === $offering->id;
        });
    }

    public function test_send_project_invoice_via_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        $this->actingAs($this->adminUser);

        $invoice = ProjectInvoice::create([
            'invoice_number'        => ProjectInvoice::generateInvoiceNumber(),
            'client_name'           => 'PT Invoice Email Test',
            'client_email'          => 'finance@invoicetest.com',
            'project_name'          => 'Project Launching Brand',
            'issue_date'            => now(),
            'due_date'              => now()->addDays(14),
            'subtotal'              => 10000000,
            'other_cost'            => 500000,
            'management_fee'        => 1500000,
            'gross_total'           => 12000000,
            'pph_percentage'        => 2,
            'pph_amount'            => 240000,
            'net_amount'            => 11760000,
            'include_pph_deduction' => true,
            'amount_paid'           => 6000000,
            'remaining_balance'     => 6000000,
            'payment_status'        => 'partial',
        ]);

        $response = $this->post(route('admin.project-invoices.send-email', $invoice), [
            'recipient_email' => 'finance@invoicetest.com',
            'email_message'   => 'Berikut invoice partial/DP.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ProjectInvoiceMail::class, function ($mail) use ($invoice) {
            return $mail->hasTo('finance@invoicetest.com') &&
                   $mail->projectInvoice->id === $invoice->id;
        });
    }
}
