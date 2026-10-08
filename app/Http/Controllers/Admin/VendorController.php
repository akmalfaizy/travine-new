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
            'products.*.name'        => 'nullable|string|max:255',
            'products.*.unit'        => 'nullable|string|max:50',
            'products.*.price'       => 'nullable',
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
            'products.*.name'        => 'nullable|string|max:255',
            'products.*.unit'        => 'nullable|string|max:50',
            'products.*.price'       => 'nullable',
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

    // ─── Single Product Operations ───────────────────────────────────────────

    public function storeProduct(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'unit'  => 'nullable|string|max:50',
            'price' => 'nullable',
            'notes' => 'nullable|string|max:255',
        ]);

        $priceRaw = $validated['price'] ?? 0;
        if (is_string($priceRaw)) {
            $priceRaw = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $priceRaw));
        }
        $price = max(0, (float) ($priceRaw ?? 0));

        $product = $vendor->products()->create([
            'name'  => trim($validated['name']),
            'unit'  => $validated['unit'] ?? null,
            'price' => $price,
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLog::record("Tambah Produk Vendor {$vendor->name}: {$product->name}");

        return back()->with('success', "Produk \"{$product->name}\" berhasil ditambahkan.");
    }

    public function updateProduct(Request $request, Vendor $vendor, VendorProduct $product)
    {
        if ($product->vendor_id !== $vendor->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'unit'  => 'nullable|string|max:50',
            'price' => 'nullable',
            'notes' => 'nullable|string|max:255',
        ]);

        $priceRaw = $validated['price'] ?? 0;
        if (is_string($priceRaw)) {
            $priceRaw = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $priceRaw));
        }
        $price = max(0, (float) ($priceRaw ?? 0));

        $product->update([
            'name'  => trim($validated['name']),
            'unit'  => $validated['unit'] ?? null,
            'price' => $price,
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLog::record("Update Produk Vendor {$vendor->name}: {$product->name}");

        return back()->with('success', "Produk \"{$product->name}\" berhasil diperbarui.");
    }

    public function destroyProduct(Vendor $vendor, VendorProduct $product)
    {
        if ($product->vendor_id !== $vendor->id) {
            abort(404);
        }

        $name = $product->name;
        $product->delete();

        ActivityLog::record("Hapus Produk Vendor {$vendor->name}: {$name}");

        return back()->with('success', "Produk \"{$name}\" berhasil dihapus.");
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function syncProducts(Vendor $vendor, array $products): void
    {
        foreach ($products as $p) {
            $name = trim($p['name'] ?? '');
            if (empty($name)) continue;

            $priceRaw = $p['price'] ?? 0;
            if (is_string($priceRaw)) {
                $priceRaw = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $priceRaw));
            }
            $price = max(0, (float) ($priceRaw ?? 0));

            VendorProduct::create([
                'vendor_id' => $vendor->id,
                'name'      => $name,
                'unit'      => $p['unit'] ?? null,
                'price'     => $price,
                'notes'     => $p['notes'] ?? null,
            ]);
        }
    }
}
