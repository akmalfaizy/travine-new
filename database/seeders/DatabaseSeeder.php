<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\News;
use App\Models\Setting;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@travine.id'],
            [
                'name'     => 'admin travine.id',
                'username' => 'admin',
                'password' => Hash::make('admintravine123'),
                'role'     => 'admin',
            ]
        );

        // Default Settings
        $settings = [
            'site_name'      => 'travine.id',
            'site_tagline'   => 'Jelajahi Keindahan Nusantara',
            'phone_whatsapp' => '6282137925585',
            'email_contact'  => 'travineid@gmail.com',
            'address'        => 'Perum Griya Pratama Asri No. 3B, Jl. Munggur, Sidoarum, Godean, DIY',
            'facebook'       => 'https://facebook.com/travine.id',
            'instagram'      => 'https://instagram.com/travine.id',
            'dp_percentage'  => '30',
            'bank_name'      => 'Bank BCA',
            'bank_account'   => '1234567890',
            'bank_holder'    => 'PT Travine Indonesia',
        ];
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Categories
        $categories = [
            ['name' => 'Open Trip', 'icon' => '🌎', 'slug' => 'open-trip'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }

        // Sample Tours
        $openTripId = Category::where('slug', 'open-trip')->first()->id;
        $alamId   = $openTripId;
        $bahariId = $openTripId;
        $budayaId = $openTripId;

        $tours = [
            [
                'category_id'           => $openTripId,
                'title'                 => 'Open Trip Merapi Lava Tour & Adventure Jogja',
                'slug'                  => 'open-trip-merapi-lava-tour-adventure-jogja',
                'thumbnail'             => 'tours/opentrip_adventure.jpeg',
                'price'                 => 350000,
                'hpp_price'             => 280000,
                'commission_percentage' => 10,
                'location'              => 'Sleman, D.I. Yogyakarta',
                'duration'              => '1 Hari (Adventure Trip)',
                'short_description'     => 'Rasakan sensasi petualangan offroad Jeep Lava Tour Merapi, Bunker Kaliadem, Museum Sisa Hartaku, dan keseruan susur alam Jogja.',
                'description'           => '<p>Paket <strong>Open Trip Merapi Lava Tour &amp; Adventure</strong> mengajak Anda menjelajahi keindahan alam serta jejak erupsi Gunung Merapi dengan mobil Jeep 4x4 yang memacu adrenalin. Nikmati pemandangan megah Gunung Merapi dari dekat, kunjungi spot bersejarah, dan rasakan kesegaran wisata alam Jogja.</p><p>Sangat cocok untuk solo traveler, teman kerja, komunitas, maupun keluarga yang ingin liburan seru tanpa ribet!</p>',
                'itinerary'             => "07.30 - 08.00: Kumpul di meeting point (Stasiun Tugu / Pusat Kota Jogja)\n08.00 - 09.30: Perjalanan menuju Basecamp Lava Tour Merapi\n09.30 - 12.00: Adventure Jeep Lava Tour (Bunker Kaliadem, Batu Alien, Museum Sisa Hartaku & Manuver Air Kalikuning)\n12.00 - 13.30: Makan siang & istirahat\n13.30 - 15.30: Wisata alam & tubing seru\n15.30 - 17.00: Wisata kuliner khas Jogja\n17.00 - 18.00: Perjalanan kembali ke Meeting Point & Trip Selesai",
                'facilities'            => "✓ Transportasi AC PP\n✓ Jeep 4x4 Lava Tour (Helm & Driver Berpengalaman)\n✓ Tiket Masuk Semua Destinasi\n✓ Makan Siang 1x & Air Mineral\n✓ Tour Leader Profesional\n✓ Dokumentasi Foto Selama Trip\n✓ P3K Standar",
                'is_active'             => true,
            ],
            [
                'category_id'           => $openTripId,
                'title'                 => 'Open Trip Eksplorasi Pesona Perbukitan Menoreh',
                'slug'                  => 'open-trip-eksplorasi-pesona-perbukitan-menoreh',
                'thumbnail'             => 'tours/opentrip_menoreh.jpeg',
                'price'                 => 385000,
                'hpp_price'             => 300000,
                'commission_percentage' => 10,
                'location'              => 'Kulon Progo, D.I. Yogyakarta',
                'duration'              => '1 Hari (Sunrise & Nature)',
                'short_description'     => 'Saksikan magical sunrise di Tumpeng Menoreh dengan panorama 4 gunung, sejuknya Kebun Teh Nglinggo, dan kesegaran Air Terjun Kedung Pedut.',
                'description'           => '<p>Jelajahi perbukitan barat Yogyakarta dalam <strong>Open Trip Pesona Menoreh</strong>. Mulai dari menikmati matahari terbit dari ketinggian 1.000 mdpl di Tumpeng Menoreh dengan panorama gunung yang memukau, berjalan santai di hamparan hijau Kebun Teh Nglinggo, hingga berenang di kolam alami bertingkat Air Terjun Kedung Pedut.</p><p>Paket ini dirancang untuk Anda yang menyukai ketenangan alam, udara sejuk, dan spot foto estetik kekinian.</p>',
                'itinerary'             => "03.30 - 04.00: Penjemputan di Meeting Point Jogja Kota\n04.00 - 05.00: Perjalanan menuju Tumpeng Menoreh\n05.00 - 07.30: Golden Sunrise & Sarapan di Tumpeng Menoreh\n07.30 - 09.30: Hunting foto & eksplor Kebun Teh Nglinggo\n09.30 - 12.00: Trekking & berenang di Air Terjun Kedung Pedut\n12.00 - 13.30: Makan siang kuliner lokal Menoreh\n13.30 - 15.30: Santai ngopi di kedai kopi lereng Menoreh\n15.30 - 17.00: Drop off Meeting Point & Tour selesai",
                'facilities'            => "✓ Transportasi AC PP Jogja - Menoreh\n✓ Tiket Masuk Tumpeng Menoreh\n✓ Tiket Kebun Teh Nglinggo & Kedung Pedut\n✓ Sarapan & Makan Siang Khas Menoreh\n✓ Air Mineral selama perjalanan\n✓ Tour Leader & Dokumentasi\n✓ P3K Standar",
                'is_active'             => true,
            ],
            [
                'category_id'           => $openTripId,
                'title'                 => 'Open Trip Ocean Paradise & Snorkeling Pantai Selatan',
                'slug'                  => 'open-trip-ocean-paradise-snorkeling-pantai-selatan',
                'thumbnail'             => 'tours/opentrip_ocean.jpeg',
                'price'                 => 420000,
                'hpp_price'             => 330000,
                'commission_percentage' => 10,
                'location'              => 'Gunungkidul, D.I. Yogyakarta',
                'duration'              => '1 Hari (Beach & Snorkeling)',
                'short_description'     => 'Petualangan snorkeling melihat terumbu karang & ikan laut di Pantai Nglambor, tebing eksotis Timang, dan pesona sunset pantai selatan.',
                'description'           => '<p>Rasakan eksotisme pesisir selatan Jogja dalam <strong>Open Trip Ocean Paradise</strong>. Paket ini mengajak Anda snorkeling bersama ikan-ikan karang di laguna alami, mengagumi tebing karang megah, dan bersantai menikmati matahari terbenam dengan deburan ombak pantai berpasir putih.</p><p>Pengalaman bahari terbaik di Yogyakarta dengan fasilitas lengkap dan pendampingan instruktur profesional.</p>',
                'itinerary'             => "06.30 - 07.00: Kumpul di meeting point Jogja Kota\n07.00 - 09.30: Perjalanan menuju kawasan pesisir Gunungkidul\n09.30 - 12.30: Snorkeling di Pantai Nglambor / Sadranan (Full Gear & Foto Underwater)\n12.30 - 14.00: Makan siang hidangan laut (Seafood) di tepi pantai\n14.00 - 16.30: Eksplorasi spot tebing eksotis & pantai pasir putih\n16.30 - 18.00: Menikmati Sunset di tepi pantai\n18.00 - 20.30: Perjalanan pulang ke Jogja Kota & Tour selesai",
                'facilities'            => "✓ Transportasi AC Wisata PP\n✓ Alat Snorkeling Lengkap (Mask, Snorkel, Life Jacket)\n✓ Pemandu Snorkeling & Rescue Team\n✓ Foto Underwater GoPro / Camera\n✓ Tiket Masuk Kawasan Pantai\n✓ Makan Siang Seafood 1x & Air Mineral\n✓ Tour Leader & Dokumentasi Trip",
                'is_active'             => true,
            ],
        ];

        foreach ($tours as $tour) {
            Tour::create($tour);
        }

        // Sample News
        News::firstOrCreate(
            ['slug' => 'travine-hadirkan-paket-wisata-baru-raja-ampat'],
            [
                'title'        => 'travine.id Hadirkan Paket Wisata Baru ke Raja Ampat',
                'summary'      => 'travine.id dengan bangga menghadirkan paket wisata eksklusif ke Raja Ampat, Papua Barat, dengan harga yang terjangkau dan fasilitas premium.',
                'content'      => '<p>travine.id terus berinovasi dalam menghadirkan paket wisata terbaik bagi pelanggan setia kami. Kali ini, kami dengan bangga memperkenalkan <strong>Paket Wisata Raja Ampat 4D3N</strong> yang menawarkan pengalaman tak terlupakan di surga tersembunyi Indonesia.</p><p>Raja Ampat dikenal sebagai salah satu destinasi diving terbaik di dunia, dengan keanekaragaman hayati laut yang luar biasa. Paket ini dirancang khusus untuk memberikan pengalaman terbaik dengan harga yang kompetitif.</p>',
                'author'       => 'admin travine.id',
                'published_at' => now(),
                'status'       => 'published',
            ]
        );

        News::firstOrCreate(
            ['slug' => 'tips-persiapan-wisata-pulau-indonesia'],
            [
                'title'        => 'Tips Persiapan Wisata ke Pulau-Pulau Indah Indonesia',
                'summary'      => 'Sebelum memulai petualangan ke pulau-pulau indah di Indonesia, ada beberapa persiapan penting yang perlu Anda lakukan.',
                'content'      => '<p>Berwisata ke pulau-pulau indah di Indonesia adalah pengalaman yang luar biasa. Namun, persiapan yang matang akan membuat perjalanan Anda semakin menyenangkan. Berikut adalah beberapa tips yang perlu Anda perhatikan.</p><h2>1. Pilih Waktu yang Tepat</h2><p>Musim kemarau (April-Oktober) adalah waktu terbaik untuk berwisata ke pulau-pulau di Indonesia.</p><h2>2. Siapkan Dokumen Penting</h2><p>Pastikan KTP dan dokumen perjalanan Anda selalu dalam kondisi baik dan mudah diakses.</p>',
                'author'       => 'admin travine.id',
                'published_at' => now()->subDays(3),
                'status'       => 'published',
            ]
        );

        $this->call(PortfolioSeeder::class);
    }
}
