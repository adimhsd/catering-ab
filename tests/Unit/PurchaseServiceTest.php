<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test untuk PurchaseService.
 * Fokus: logika kalkulasi dan generate nomor transaksi.
 */
class PurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    private PurchaseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PurchaseService();

        // Set locale Indonesia untuk format tanggal
        Carbon::setLocale('id');
    }

    /* ===== Test: hitungSubtotal ===== */

    /** @test */
    public function subtotal_dihitung_dengan_benar(): void
    {
        $this->assertEquals(50_000, $this->service->hitungSubtotal(5, 10_000));
        $this->assertEquals(0, $this->service->hitungSubtotal(0, 50_000));
        $this->assertEquals(15_000, $this->service->hitungSubtotal(1.5, 10_000));
    }

    /** @test */
    public function subtotal_dengan_harga_nol_adalah_nol(): void
    {
        $this->assertEquals(0.0, $this->service->hitungSubtotal(10, 0));
    }

    /* ===== Test: hitungTotal ===== */

    /** @test */
    public function total_dihitung_dari_semua_item(): void
    {
        $items = [
            ['qty' => 2, 'harga' => 10_000],  // 20.000
            ['qty' => 3, 'harga' => 5_000],   // 15.000
            ['qty' => 1, 'harga' => 25_000],  // 25.000
        ];

        $this->assertEquals(60_000, $this->service->hitungTotal($items));
    }

    /** @test */
    public function total_dengan_array_kosong_adalah_nol(): void
    {
        $this->assertEquals(0.0, $this->service->hitungTotal([]));
    }

    /** @test */
    public function total_mengabaikan_item_tanpa_qty_atau_harga(): void
    {
        $items = [
            ['qty' => '', 'harga' => 10_000],
            ['qty' => 5, 'harga' => ''],
            ['qty' => 2, 'harga' => 15_000],
        ];

        // Hanya item ketiga yang valid: 2 × 15.000 = 30.000
        $this->assertEquals(30_000, $this->service->hitungTotal($items));
    }

    /* ===== Test: generateNomorTransaksi ===== */

    /** @test */
    public function nomor_transaksi_pertama_berformat_benar(): void
    {
        $tanggal = Carbon::parse('2024-06-15');
        $nomor   = $this->service->generateNomorTransaksi($tanggal);

        $this->assertEquals('PB-20240615-001', $nomor);
    }

    /** @test */
    public function nomor_transaksi_increment_per_tanggal(): void
    {
        // Butuh user + supplier + produk
        $user     = User::factory()->create(['role' => 'admin_dapur']);
        $supplier = Supplier::factory()->create();
        $this->actingAs($user);

        // Simpan 2 transaksi di tanggal yang sama
        $tanggal = Carbon::parse('2024-06-15');

        $this->service->simpan(
            header: ['supplier_id' => $supplier->id, 'tanggal' => '2024-06-15', 'catatan' => null],
            items: [['product_id' => Product::factory()->create()->id, 'qty' => 1, 'harga' => 10000]],
        );

        // Nomor kedua harus 002
        $nomor = $this->service->generateNomorTransaksi($tanggal);
        $this->assertEquals('PB-20240615-002', $nomor);
    }

    /** @test */
    public function nomor_transaksi_tanggal_berbeda_mulai_dari_001(): void
    {
        $user     = User::factory()->create(['role' => 'admin_dapur']);
        $supplier = Supplier::factory()->create();
        $this->actingAs($user);

        // Simpan 1 transaksi di tanggal 15
        $this->service->simpan(
            header: ['supplier_id' => $supplier->id, 'tanggal' => '2024-06-15', 'catatan' => null],
            items: [['product_id' => Product::factory()->create()->id, 'qty' => 1, 'harga' => 10000]],
        );

        // Tanggal 16 harus mulai dari 001 lagi
        $nomor = $this->service->generateNomorTransaksi(Carbon::parse('2024-06-16'));
        $this->assertEquals('PB-20240616-001', $nomor);
    }

    /* ===== Test: simpan (integration) ===== */

    /** @test */
    public function simpan_membuat_transaksi_dengan_detail_yang_benar(): void
    {
        $user    = User::factory()->create(['role' => 'admin_dapur']);
        $supplier = Supplier::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $this->actingAs($user);

        $items = [
            ['product_id' => $product1->id, 'qty' => 2, 'harga' => 10_000],
            ['product_id' => $product2->id, 'qty' => 1, 'harga' => 25_000],
        ];

        $purchase = $this->service->simpan(
            header: ['supplier_id' => $supplier->id, 'tanggal' => '2024-06-15', 'catatan' => 'Test'],
            items: $items,
        );

        // Header
        $this->assertNotNull($purchase);
        $this->assertEquals($supplier->id, $purchase->supplier_id);
        $this->assertEquals(45_000, $purchase->total); // 20.000 + 25.000
        $this->assertStringStartsWith('PB-20240615-', $purchase->nomor_transaksi);

        // Detail
        $this->assertCount(2, $purchase->details);

        // Subtotal baris pertama
        $detail1 = $purchase->details->where('product_id', $product1->id)->first();
        $this->assertEquals(20_000, $detail1->subtotal);

        // Database
        $this->assertDatabaseHas('purchases', ['nomor_transaksi' => $purchase->nomor_transaksi]);
        $this->assertDatabaseCount('purchase_details', 2);
    }

    /** @test */
    public function hapus_menghapus_transaksi_dan_detail(): void
    {
        $user    = User::factory()->create(['role' => 'admin_dapur']);
        $supplier = Supplier::factory()->create();
        $product  = Product::factory()->create();

        $this->actingAs($user);

        $purchase = $this->service->simpan(
            header: ['supplier_id' => $supplier->id, 'tanggal' => '2024-06-15', 'catatan' => null],
            items: [['product_id' => $product->id, 'qty' => 1, 'harga' => 10_000]],
        );

        $purchaseId = $purchase->id;
        $this->service->hapus($purchase);

        $this->assertDatabaseMissing('purchases', ['id' => $purchaseId]);
        $this->assertDatabaseMissing('purchase_details', ['purchase_id' => $purchaseId]);
    }
}
