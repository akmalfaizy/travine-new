<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Vendor::withCount('products')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%")
                  ->orWhere('pic_name', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $vendors    = $query->paginate(20)->withQueryString();
        $categories = Vendor::$categories;
        $totalVendors   = Vendor::count();
        $activeVendors  = Vendor::where('is_active', true)->count();
        $totalProducts  = VendorProduct::count();
        $totalCategories = Vendor::distinct()->count('category');

        return view('admin.vendors.index', compact(
            'vendors', 'categories',
            'totalVendors', 'activeVendors', 'totalProducts', 'totalCategories'
        ));
    }

    // ─── Create / Store ───────────────────────────────────────────────────────

    public function create()
    {
        $categories = Vendor::$categories;
        return view('admin.vendors.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'required|string|max:100',
            'phone'      => 'required|string|max:50',
            'email'      => 'nullable|email|max:255',
            'location'   => 'nullable|string|max:255',
            'pic_name'   => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
            'is_active'  => 'nullable|boolean',
            // Products
            'products'               => 'nullable|array',
            'products.*.name'        => 'required_with:products|string|max:255',
            'products.*.unit'        => 'nullable|string|max:50',
            'products.*.price'       => 'nullable|numeric|min:0',
            'products.*.notes'       => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $vendor = Vendor::create([
                'name'      => $validated['name'],
                'category'  => $validated['category'],
                'phone'     => $validated['phone'],
                'email'     => $validated['email'] ?? null,
                'location'  => $validated['location'] ?? null,
                'pic_name'  => $validated['pic_name'] ?? null,
                'notes'     => $validated['notes'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $this->syncProducts($vendor, $validated['products'] ?? []);

            ActivityLog::record("Tambah Vendor: {$vendor->name}");
        });

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Vendor $vendor)
    {
        $vendor->load('products');
        return view('admin.vendors.show', compact('vendor'));
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(Vendor $vendor)
    {
        $vendor->load('products');
        $categories = Vendor::$categories;
        return view('admin.vendors.edit', compact('vendor', 'categories'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'required|string|max:100',
            'phone'      => 'required|string|max:50',
            'email'      => 'nullable|email|max:255',
            'location'   => 'nullable|string|max:255',
            'pic_name'   => 'nullable|string|max:255',
            'notes'      => 'nullable|string',
            'is_active'  => 'nullable|boolean',
            'products'               => 'nullable|array',
            'products.*.name'        => 'required_with:products|string|max:255',
            'products.*.unit'        => 'nullable|string|max:50',
            'products.*.price'       => 'nullable|numeric|min:0',
            'products.*.notes'       => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $vendor) {
            $vendor->update([
                'name'      => $validated['name'],
                'category'  => $validated['category'],
                'phone'     => $validated['phone'],
                'email'     => $validated['email'] ?? null,
                'location'  => $validated['location'] ?? null,
                'pic_name'  => $validated['pic_name'] ?? null,
                'notes'     => $validated['notes'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Sync products: delete all then re-insert
            $vendor->products()->delete();
            $this->syncProducts($vendor, $validated['products'] ?? []);

            ActivityLog::record("Edit Vendor: {$vendor->name}");
        });

        return redirect()->route('admin.vendors.show', $vendor)
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(Vendor $vendor)
    {
        ActivityLog::record("Hapus Vendor: {$vendor->name}");
        $vendor->delete(); // cascade deletes products
        return redirect()->route('admin.vendors.index')
            ->with('success', "Vendor \"{$vendor->name}\" berhasil dihapus.");
    }

    // ─── Toggle Active ────────────────────────────────────────────────────────

    public function toggleActive(Vendor $vendor)
    {
        $vendor->update(['is_active' => !$vendor->is_active]);
        $label = $vendor->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::record("Vendor \"{$vendor->name}\" {$label}.");
        return back()->with('success', "Vendor \"{$vendor->name}\" berhasil {$label}.");
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function syncProducts(Vendor $vendor, array $products): void
    {
        foreach ($products as $p) {
            $name = trim($p['name'] ?? '');
            if (empty($name)) continue;

            VendorProduct::create([
                'vendor_id' => $vendor->id,
                'name'      => $name,
                'unit'      => $p['unit'] ?? null,
                'price'     => max(0, (float) ($p['price'] ?? 0)),
                'notes'     => $p['notes'] ?? null,
            ]);
        }
    }
}
