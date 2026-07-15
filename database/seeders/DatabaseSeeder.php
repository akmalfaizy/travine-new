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
        User::create([
            'name'     => 'Admin Travine',
            'username' => 'admin',
            'email'    => 'admin@travine.id',
            'password' => Hash::make('admintravine123'),
            'role'     => 'admin',
        ]);

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
            Setting::create(['key' => $key, 'value' => $value]);
        }

        // Categories
        $categories = [
            ['name' => 'Open Trip', 'icon' => '🌎', 'slug' => 'open-trip'],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['is_active' => true]));
        }

        // Sample Tours
        $openTripId = Category::where('slug', 'open-trip')->first()->id;
        $alamId   = $openTripId;
        $bahariId = $openTripId;
        $budayaId = $openTripId;

        $tours = [
            [
                'category_id'       => $alamId,
                'title'             => 'Paket Wisata Raja Ampat 4D3N',
                'slug'              => 'paket-wisata-raja-ampat-4d3n',
                'price'             => 4500000,
                'location'          => 'Raja Ampat, Papua Barat',
                'duration'          => '4 Hari 3 Malam',
                'short_description' => 'Nikmati keindahan bawah laut Raja Ampat yang menakjubkan dengan paket wisata eksklusif kami.',
                'description'       => '<p>Raja Ampat adalah surga tersembunyi di ujung timur Indonesia. Dengan keanekaragaman hayati laut terkaya di dunia, Raja Ampat menawarkan pengalaman snorkeling dan diving yang tak terlupakan.</p><p>Paket ini mencakup akomodasi di resort tepi pantai, pemandu lokal berpengalaman, dan akses ke spot-spot diving terbaik.</p>',
                'itinerary'         => "Hari 1: Tiba di Sorong, transfer ke Raja Ampat\nHari 2: Island hopping & snorkeling\nHari 3: Diving di Blue Magic & Cape Kri\nHari 4: Check out & kembali ke Sorong",
                'facilities'        => "✓ Akomodasi resort\n✓ Makan 3x sehari\n✓ Transportasi kapal\n✓ Pemandu wisata\n✓ Peralatan snorkeling",
                'is_active'         => true,
            ],
            [
                'category_id'       => $bahariId,
                'title'             => 'Paket Wisata Lombok 3D2N',
                'slug'              => 'paket-wisata-lombok-3d2n',
                'price'             => 2800000,
                'location'          => 'Lombok, NTB',
                'duration'          => '3 Hari 2 Malam',
                'short_description' => 'Eksplorasi keindahan pantai Lombok dan Gili Islands yang memukau.',
                'description'       => '<p>Lombok menawarkan keindahan pantai yang memukau dengan pasir putih dan air laut yang jernih. Kunjungi Gili Trawangan, Gili Meno, dan Gili Air dalam satu paket wisata yang terjangkau.</p>',
                'itinerary'         => "Hari 1: Tiba di Lombok, check-in hotel\nHari 2: Gili Islands tour\nHari 3: Pantai Kuta & perjalanan pulang",
                'facilities'        => "✓ Hotel bintang 3\n✓ Sarapan\n✓ Speed boat Gili\n✓ Guide lokal",
                'is_active'         => true,
            ],
            [
                'category_id'       => $budayaId,
                'title'             => 'Paket Wisata Yogyakarta 3D2N',
                'slug'              => 'paket-wisata-yogyakarta-3d2n',
                'price'             => 1800000,
                'location'          => 'Yogyakarta',
                'duration'          => '3 Hari 2 Malam',
                'short_description' => 'Jelajahi kekayaan budaya Yogyakarta — Borobudur, Prambanan, dan Keraton.',
                'description'       => '<p>Yogyakarta adalah kota budaya yang kaya sejarah. Kunjungi Candi Borobudur yang megah, Candi Prambanan yang indah, dan Keraton Ngayogyakarta Hadiningrat yang penuh sejarah.</p>',
                'itinerary'         => "Hari 1: Tiba Yogya, Malioboro & Keraton\nHari 2: Borobudur & Prambanan\nHari 3: Parangtritis & perjalanan pulang",
                'facilities'        => "✓ Hotel bintang 3\n✓ Sarapan\n✓ Transport AC\n✓ Tiket wisata\n✓ Guide",
                'is_active'         => true,
            ],
        ];

        foreach ($tours as $tour) {
            Tour::create($tour);
        }

        // Sample News
        News::create([
            'title'        => 'travine.id Hadirkan Paket Wisata Baru ke Raja Ampat',
            'slug'         => 'travine-hadirkan-paket-wisata-baru-raja-ampat',
            'summary'      => 'travine.id dengan bangga menghadirkan paket wisata eksklusif ke Raja Ampat, Papua Barat, dengan harga yang terjangkau dan fasilitas premium.',
            'content'      => '<p>travine.id terus berinovasi dalam menghadirkan paket wisata terbaik bagi pelanggan setia kami. Kali ini, kami dengan bangga memperkenalkan <strong>Paket Wisata Raja Ampat 4D3N</strong> yang menawarkan pengalaman tak terlupakan di surga tersembunyi Indonesia.</p><p>Raja Ampat dikenal sebagai salah satu destinasi diving terbaik di dunia, dengan keanekaragaman hayati laut yang luar biasa. Paket ini dirancang khusus untuk memberikan pengalaman terbaik dengan harga yang kompetitif.</p>',
            'author'       => 'Admin Travine',
            'published_at' => now(),
            'status'       => 'published',
        ]);

        News::create([
            'title'        => 'Tips Persiapan Wisata ke Pulau-Pulau Indah Indonesia',
            'slug'         => 'tips-persiapan-wisata-pulau-indonesia',
            'summary'      => 'Sebelum memulai petualangan ke pulau-pulau indah di Indonesia, ada beberapa persiapan penting yang perlu Anda lakukan.',
            'content'      => '<p>Berwisata ke pulau-pulau indah di Indonesia adalah pengalaman yang luar biasa. Namun, persiapan yang matang akan membuat perjalanan Anda semakin menyenangkan. Berikut adalah beberapa tips yang perlu Anda perhatikan.</p><h2>1. Pilih Waktu yang Tepat</h2><p>Musim kemarau (April-Oktober) adalah waktu terbaik untuk berwisata ke pulau-pulau di Indonesia.</p><h2>2. Siapkan Dokumen Penting</h2><p>Pastikan KTP dan dokumen perjalanan Anda selalu dalam kondisi baik dan mudah diakses.</p>',
            'author'       => 'Admin Travine',
            'published_at' => now()->subDays(3),
            'status'       => 'published',
        ]);

        $this->call(PortfolioSeeder::class);
    }
}
