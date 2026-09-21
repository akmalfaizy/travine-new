<?php

namespace Database\Seeders;

use App\Models\OfferingMasterItem;
use Illuminate\Database\Seeder;

class OfferingMasterItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // TRANSPORT
            ['category_name' => 'Transport', 'component' => 'Bus Pariwisata (Unit + Driver + BBM)', 'description' => 'Bis besar, umur min 2022 sesuai specs', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Transport', 'component' => 'Mobil Crew', 'description' => 'Kendaraan operasional crew', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Transport', 'component' => 'Mobil Operasional', 'description' => 'Kendaraan operasional lapangan', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Transport', 'component' => 'Parkir', 'description' => 'Biaya parkir seluruh kendaraan', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Transport', 'component' => 'Tol', 'description' => 'Biaya tol perjalanan PP', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Transport', 'component' => 'Retribusi', 'description' => 'Retribusi masuk area/destinasi', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Transport', 'component' => 'Banner Bus', 'description' => 'Desain dan cetak banner bus', 'default_type' => 'F', 'default_remark' => 'Optional', 'default_price' => 0],

            // DESTINATION
            ['category_name' => 'Destination', 'component' => 'Tiket Masuk Destinasi', 'description' => 'Tiket masuk lokasi wisata', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Destination', 'component' => 'Area Gathering', 'description' => 'Sewa area untuk kegiatan gathering', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Destination', 'component' => 'Pusat Oleh-Oleh', 'description' => 'Kunjungan ke pusat oleh-oleh', 'default_type' => 'F', 'default_remark' => 'Optional', 'default_price' => 0],
            ['category_name' => 'Destination', 'component' => 'Malioboro', 'description' => 'Kunjungan Malioboro Yogyakarta', 'default_type' => 'F', 'default_remark' => 'Optional', 'default_price' => 0],

            // F & B
            ['category_name' => 'F & B', 'component' => 'Snack Box', 'description' => 'Snack box perjalanan', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'F & B', 'component' => 'Hot Drink', 'description' => 'Minuman panas (teh/kopi)', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'F & B', 'component' => 'Lunch Buffet', 'description' => 'Makan siang prasmanan', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'F & B', 'component' => 'Dinner Buffet', 'description' => 'Makan malam prasmanan', 'default_type' => 'V', 'default_remark' => 'Optional', 'default_price' => 0],
            ['category_name' => 'F & B', 'component' => 'Air Mineral', 'description' => 'Air mineral botol per peserta', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],

            // GATHERING & CHARACTER TEAM BUILDING
            ['category_name' => 'Gathering & Character Team Building', 'component' => 'MC', 'description' => 'Master of Ceremony profesional', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Gathering & Character Team Building', 'component' => 'Entertainment', 'description' => 'Hiburan / games interaktif', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Gathering & Character Team Building', 'component' => 'Totebag / Souvenir', 'description' => 'Tas/souvenir peserta', 'default_type' => 'V', 'default_remark' => 'Optional', 'default_price' => 0],
            ['category_name' => 'Gathering & Character Team Building', 'component' => 'Kaos Seragam', 'description' => 'Kaos peserta gathering', 'default_type' => 'V', 'default_remark' => 'Optional', 'default_price' => 0],

            // DOCUMENTATION
            ['category_name' => 'Documentation', 'component' => 'Fotografer', 'description' => 'Fotografer profesional', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Documentation', 'component' => 'Videografer', 'description' => 'Videografer + editing', 'default_type' => 'F', 'default_remark' => 'Optional', 'default_price' => 0],
            ['category_name' => 'Documentation', 'component' => 'Cetak Foto', 'description' => 'Cetak foto kenangan', 'default_type' => 'V', 'default_remark' => 'Optional', 'default_price' => 0],

            // SAFETY & MEDICAL
            ['category_name' => 'Safety & Medical', 'component' => 'Medis / P3K', 'description' => 'Tim medis dan perlengkapan P3K', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],

            // INSURANCES
            ['category_name' => 'Insurances', 'component' => 'Asuransi Perjalanan', 'description' => 'Asuransi kecelakaan perjalanan per peserta', 'default_type' => 'V', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],

            // TOUR OPERATION
            ['category_name' => 'Tour Operation', 'component' => 'Tour Leader', 'description' => 'Tour Leader profesional', 'default_type' => 'F', 'default_remark' => 'Included (Wajib)', 'default_price' => 0],
            ['category_name' => 'Tour Operation', 'component' => 'Local Guide', 'description' => 'Pemandu wisata lokal', 'default_type' => 'F', 'default_remark' => 'Optional', 'default_price' => 0],
        ];

        foreach ($items as $item) {
            OfferingMasterItem::firstOrCreate(
                ['category_name' => $item['category_name'], 'component' => $item['component']],
                $item
            );
        }
    }
}
