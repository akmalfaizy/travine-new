<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ExpenditureController;
use App\Http\Controllers\Admin\OfferingController;
use App\Http\Controllers\Admin\ProjectInvoiceController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\InsightController;
use App\Http\Controllers\Admin\BannerController;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Fallback login route for standard Laravel auth guards & packages
Route::get('/login', function (\Illuminate\Http\Request $request) {
    if ($request->is('admin*')) {
        return redirect()->route('admin.login');
    }
    return redirect()->route('partner.login');
})->name('login');

// Locale switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Tours
Route::get('/wisata/{category:slug}', [TourController::class, 'category'])->name('tours.category');
Route::get('/wisata/detail/{tour:slug}', [TourController::class, 'show'])->name('tours.show');

// Bookings (public)
Route::post('/bookings/initiate', [BookingController::class, 'initiate'])->name('bookings.initiate');
Route::get('/bookings/payment', [BookingController::class, 'paymentPage'])->name('bookings.payment');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{code}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('/bookings/{code}/proof', [BookingController::class, 'uploadProof'])->name('bookings.proof');
Route::get('/bookings/{code}/verify', [BookingController::class, 'verify'])->name('bookings.verify');
Route::get('/bookings/{code}/invoice', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice');

// Booking Tracker (Cek Status Pemesanan)
Route::get('/cek-booking', [BookingController::class, 'trackPage'])->name('bookings.track');
Route::post('/cek-booking', [BookingController::class, 'trackSearch'])->middleware('throttle:15,1')->name('bookings.track.search');

// Contact
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

// News (public)
Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{news:slug}', [NewsController::class, 'show'])->name('news.show');

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth (guest only with rate limiter)
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');

        // Portfolios (existing)
        Route::resource('portfolios', AdminPortfolioController::class);

        // Categories
        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        // Tours
        Route::resource('tours', AdminTourController::class)->except(['show']);

        // News
        Route::resource('news', AdminNewsController::class)->except(['show']);

        // Banners (Promo & Open Trip Slider)
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::post('/banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');

        // Bookings
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/export', [AdminBookingController::class, 'exportCsv'])->name('bookings.export');
        Route::get('/bookings/export-insurance', [AdminBookingController::class, 'exportInsuranceCsv'])->name('bookings.export-insurance');
        Route::get('/bookings/export-manifest-pdf', [AdminBookingController::class, 'exportManifestPdf'])->name('bookings.export-manifest-pdf');
        Route::get('/bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [AdminBookingController::class, 'storeBooking'])->name('bookings.store');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::get('/bookings/{booking}/invoice', [AdminBookingController::class, 'downloadInvoice'])->name('bookings.invoice');
        Route::post('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
        Route::post('/bookings/{booking}/payment', [AdminBookingController::class, 'updatePayment'])->name('bookings.payment');
        Route::post('/bookings/{booking}/resend', [AdminBookingController::class, 'resendInvoice'])->name('bookings.resend');
        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

        // Manual Invoice routes
        Route::get('/manual-invoices', [AdminBookingController::class, 'manualInvoiceList'])->name('bookings.manualInvoiceList');
        Route::get('/manual-invoices/create', [AdminBookingController::class, 'showCreateManualInvoiceForm'])->name('bookings.manualInvoiceCreateForm');
        Route::post('/manual-invoices', [AdminBookingController::class, 'storeCreateManualInvoice'])->name('bookings.manualInvoiceCreate');
        Route::get('/bookings/{booking}/manual-invoice/create', [AdminBookingController::class, 'showManualInvoiceForm'])->name('bookings.manualInvoiceForm');
        Route::post('/bookings/{booking}/manual-invoice', [AdminBookingController::class, 'storeManualInvoice'])->name('bookings.manualInvoiceStore');

        // Partners Management
        Route::get('/partners', [\App\Http\Controllers\Admin\PartnerController::class, 'index'])->name('partners.index');
        Route::get('/partners/{partner}', [\App\Http\Controllers\Admin\PartnerController::class, 'show'])->name('partners.show');
        Route::post('/partners/{partner}/status', [\App\Http\Controllers\Admin\PartnerController::class, 'toggleStatus'])->name('partners.status');

        // Withdrawals Management
        Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show');
        Route::post('/withdrawals/{withdrawal}/status', [\App\Http\Controllers\Admin\WithdrawalController::class, 'updateStatus'])->name('withdrawals.status');

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        // Social Links management
        Route::get('/social-links/edit', [SocialLinkController::class, 'edit'])->name('social-links.edit');
        Route::post('/social-links', [SocialLinkController::class, 'update'])->name('social-links.update');

        // ─── Offerings ────────────────────────────────────────────────────
        Route::get('/offerings', [OfferingController::class, 'index'])->name('offerings.index');
        Route::get('/offerings/create', [OfferingController::class, 'create'])->name('offerings.create');
        Route::post('/offerings', [OfferingController::class, 'store'])->name('offerings.store');
        Route::get('/offerings/{offering}', [OfferingController::class, 'show'])->name('offerings.show');
        Route::get('/offerings/{offering}/edit', [OfferingController::class, 'edit'])->name('offerings.edit');
        Route::put('/offerings/{offering}', [OfferingController::class, 'update'])->name('offerings.update');
        Route::delete('/offerings/{offering}', [OfferingController::class, 'destroy'])->name('offerings.destroy');
        Route::post('/offerings/{offering}/status', [OfferingController::class, 'updateStatus'])->name('offerings.status');
        Route::get('/offerings/{offering}/proposal-pdf', [OfferingController::class, 'downloadProposal'])->name('offerings.proposal-pdf');
        Route::get('/offerings/{offering}/create-invoice', [OfferingController::class, 'createInvoice'])->name('offerings.create-invoice');
        Route::post('/offerings/{offering}/send-email', [OfferingController::class, 'sendEmail'])->name('offerings.send-email');

        // ─── Project Invoices ─────────────────────────────────────────────
        Route::get('/project-invoices', [ProjectInvoiceController::class, 'index'])->name('project-invoices.index');
        Route::post('/project-invoices', [ProjectInvoiceController::class, 'store'])->name('project-invoices.store');
        Route::get('/project-invoices/{projectInvoice}', [ProjectInvoiceController::class, 'show'])->name('project-invoices.show');
        Route::post('/project-invoices/{projectInvoice}/payment', [ProjectInvoiceController::class, 'recordPayment'])->name('project-invoices.payment');
        Route::post('/project-invoices/{projectInvoice}/status', [ProjectInvoiceController::class, 'updateStatus'])->name('project-invoices.status');
        Route::get('/project-invoices/{projectInvoice}/pdf', [ProjectInvoiceController::class, 'downloadPdf'])->name('project-invoices.pdf');
        Route::post('/project-invoices/{projectInvoice}/send-email', [ProjectInvoiceController::class, 'sendEmail'])->name('project-invoices.send-email');
        Route::delete('/project-invoices/{projectInvoice}', [ProjectInvoiceController::class, 'destroy'])->name('project-invoices.destroy');

        // ─── Vendors ──────────────────────────────────────────────────────
        Route::resource('vendors', VendorController::class);
        Route::post('/vendors/{vendor}/toggle', [VendorController::class, 'toggleActive'])->name('vendors.toggle');
        Route::post('/vendors/{vendor}/products', [VendorController::class, 'storeProduct'])->name('vendors.products.store');
        Route::put('/vendors/{vendor}/products/{product}', [VendorController::class, 'updateProduct'])->name('vendors.products.update');
        Route::delete('/vendors/{vendor}/products/{product}', [VendorController::class, 'destroyProduct'])->name('vendors.products.destroy');

        // Expenditures / Keuangan (Private — PIN protected)
        Route::get('/expenditures', [ExpenditureController::class, 'index'])->name('expenditures.index');
        Route::post('/expenditures/verify-pin', [ExpenditureController::class, 'verifyPin'])->name('expenditures.verify-pin');
        Route::post('/expenditures/lock', [ExpenditureController::class, 'lockPin'])->name('expenditures.lock');
        Route::post('/expenditures/ledger', [ExpenditureController::class, 'storeLedger'])->name('expenditures.ledger');
        Route::post('/expenditures/change-pin', [ExpenditureController::class, 'changePin'])->name('expenditures.change-pin');
        Route::get('/expenditures/monthly-data', [ExpenditureController::class, 'monthlyData'])->name('expenditures.monthly-data');
    });
});

// ─── Partner (Partner/Affiliate) Routes ────────────────────────────────────────
Route::prefix('partner')->name('partner.')->group(function () {
    // Guest routes
    Route::middleware('guest:partner')->group(function () {
        Route::get('register', [\App\Http\Controllers\Partner\AuthController::class, 'showRegister'])->name('register');
        Route::post('register', [\App\Http\Controllers\Partner\AuthController::class, 'register'])->middleware('throttle:5,1');
        Route::get('login', [\App\Http\Controllers\Partner\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\Partner\AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::get('forgot-password', [\App\Http\Controllers\Partner\AuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('forgot-password', [\App\Http\Controllers\Partner\AuthController::class, 'sendResetLink'])->middleware('throttle:3,1');
        Route::get('reset-password/{token}', [\App\Http\Controllers\Partner\AuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('reset-password', [\App\Http\Controllers\Partner\AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
        Route::get('otp', [\App\Http\Controllers\Partner\AuthController::class, 'showOtp'])->name('otp');
        Route::get('verify-otp', [\App\Http\Controllers\Partner\AuthController::class, 'showOtp'])->name('verifyOtpPage');
        Route::post('otp', [\App\Http\Controllers\Partner\AuthController::class, 'verifyOtp'])->middleware('throttle:5,1');
        Route::post('otp/resend', [\App\Http\Controllers\Partner\AuthController::class, 'resendOtp'])->middleware('throttle:3,1')->name('otp.resend');
    });

    // Authenticated partner routes
    Route::middleware('auth:partner')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'index'])->name('dashboard');
        Route::get('profile', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'profile'])->name('profile');
        Route::put('profile', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('bank-details', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'updateBankDetails'])->name('bank.update');
        Route::get('referrals', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'referrals'])->name('referrals');
        Route::get('orders', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'orders'])->name('orders');
        Route::get('commissions', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'commissions'])->name('commissions');
        Route::get('withdrawals', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'withdrawals'])->name('withdrawals');
        Route::post('withdrawals', [\App\Http\Controllers\Partner\PartnerDashboardController::class, 'storeWithdrawal'])->name('withdrawals.store');
        Route::post('logout', [\App\Http\Controllers\Partner\AuthController::class, 'logout'])->name('logout');
    });
});

?>
