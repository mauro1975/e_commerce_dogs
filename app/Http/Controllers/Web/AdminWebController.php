<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\CampaignMail;
use App\Models\Category;
use App\Models\CookieConsent;
use App\Models\Discount;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignLog;
use App\Models\GalleryPhoto;
use App\Models\Order;
use App\Models\PageVisit;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AdminWebController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'Admin access only.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $totalRevenue  = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders   = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalProducts = Product::count();

        $salesByMonth = Order::where('payment_status', 'paid')
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total) as revenue, COUNT(*) as count')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->limit(12)
            ->get();

        $topProducts = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('products.id, products.name, SUM(order_items.quantity) as total_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $salesByCategory = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw('categories.name, SUM(order_items.quantity) as total_sold')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        $recentOrders = Order::with('items')->latest()->limit(5)->get();

        $pageViews = PageVisit::selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(14)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'pendingOrders', 'totalProducts',
            'salesByMonth', 'topProducts', 'salesByCategory', 'recentOrders', 'pageViews'
        ));
    }

    public function products(Request $request)
    {
        $products = Product::with('category')
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->category))
            ->latest()
            ->paginate(20);
        $categories = Category::orderBy('name')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function createProduct()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'name'             => 'required|string|max:255',
            'name_en'          => 'nullable|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'sku'              => 'nullable|string|max:100',
            'description'      => 'nullable|string',
            'description_en'   => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'compare_price'    => 'nullable|numeric|min:0',
            'discount_percent' => 'integer|min:0|max:100',
            'stock'            => 'integer|min:0',
            'is_featured'      => 'boolean',
            'is_best_seller'   => 'boolean',
            'seo_title'        => 'nullable|string|max:255',
            'seo_description'  => 'nullable|string',
        ]);

        $data['slug']           = Str::slug($data['name']) . '-' . uniqid();
        $data['is_active']      = $request->boolean('is_active', true);
        $data['is_featured']    = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['stock']          = (int) $request->input('stock', 0);

        $colorMap = [
            'Arancione' => '#E8832A',
            'Rosa'      => '#F4A7B9',
            'Verde'     => '#9BC3B1',
            'Marrone'   => '#8B5E3C',
        ];

        $selectedColors = $request->input('colors', []);
        $selectedSizes  = $request->input('sizes', []);

        // Build available_colors array
        $data['available_colors'] = array_values(array_map(
            fn($n) => ['name' => $n, 'hex' => $colorMap[$n] ?? '#cccccc'],
            $selectedColors
        ));
        $data['available_sizes'] = array_values($selectedSizes);

        // Handle per-color multi-image uploads
        $dir = public_path('images/products');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $allPaths      = [];
        $colorImageMap = []; // colorName => [path1, path2, ...]

        foreach ($selectedColors as $colorName) {
            $files = $request->file("color_images.{$colorName}") ?? [];
            if (!is_array($files)) $files = [$files];
            $colorPaths = [];
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $filename);
                    $path = 'images/products/' . $filename;
                    $allPaths[]   = $path;
                    $colorPaths[] = $path;
                }
            }
            if (!empty($colorPaths)) {
                $colorImageMap[$colorName] = $colorPaths;
            }
        }

        if (!empty($allPaths)) {
            $data['images'] = $allPaths;
            $data['image']  = $allPaths[0];
        }

        // Store per-color images arrays into available_colors
        $data['available_colors'] = array_values(array_map(function($c) use ($colorImageMap) {
            $name = $c['name'];
            if (isset($colorImageMap[$name])) {
                $c['images'] = $colorImageMap[$name];
                $c['image']  = $colorImageMap[$name][0];
            }
            return $c;
        }, $data['available_colors']));

        $product = Product::create($data);

        // Create variants for all color/size combos
        foreach ($selectedColors as $color) {
            foreach ($selectedSizes as $size) {
                $product->variants()->create([
                    'color'     => $color,
                    'color_hex' => $colorMap[$color] ?? '#888888',
                    'size'      => strtoupper($size),
                    'stock'     => 0,
                    'sku'       => strtoupper($product->id . '-' . $color . '-' . $size),
                ]);
            }
        }

        return redirect()->route('admin.products')->with('success', 'Product created!');
    }

    public function editProduct(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id'      => 'exists:categories,id',
            'name'             => 'string|max:255',
            'name_en'          => 'nullable|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'description_en'   => 'nullable|string',
            'price'            => 'numeric|min:0',
            'compare_price'    => 'nullable|numeric|min:0',
            'discount_percent' => 'integer|min:0|max:100',
            'stock'            => 'integer|min:0',
            'sku'              => 'nullable|string|max:100',
            'image'            => 'nullable|image|max:2048',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
            'is_best_seller'   => 'boolean',
            'seo_title'        => 'nullable|string|max:255',
            'seo_description'  => 'nullable|string',
        ]);

        $data['is_active']      = $request->boolean('is_active');
        $data['is_featured']    = $request->boolean('is_featured');
        $data['is_best_seller'] = $request->boolean('is_best_seller');

        // Product gallery (add / remove individual photos)
        $galleryImages = is_array($product->images)
            ? $product->images
            : (json_decode($product->images ?? '[]', true) ?? []);

        foreach ($request->input('delete_product_images', []) as $delPath) {
            if (! is_string($delPath) || $delPath === '') {
                continue;
            }
            $abs = public_path($delPath);
            if (file_exists($abs)) {
                @unlink($abs);
            }
            $galleryImages = array_values(array_filter(
                $galleryImages,
                fn ($p) => $p !== $delPath
            ));
        }

        $productUploadDir = public_path('images/products');
        if (! is_dir($productUploadDir)) {
            mkdir($productUploadDir, 0755, true);
        }

        $newProductFiles = $request->file('product_images', []);
        if (! is_array($newProductFiles)) {
            $newProductFiles = [$newProductFiles];
        }
        foreach ($newProductFiles as $file) {
            if ($file && $file->isValid()) {
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move($productUploadDir, $filename);
                $galleryImages[] = 'images/products/'.$filename;
            }
        }

        // Update variant stocks
        foreach ($request->input('variant_stock', []) as $variantId => $stock) {
            ProductVariant::where('id', $variantId)->update(['stock' => (int) $stock]);
        }

        // Colors & Sizes from checkboxes
        $colorMap = [
            'Arancione' => '#E8832A',
            'Rosa'      => '#F4A7B9',
            'Verde'     => '#9BC3B1',
            'Marrone'   => '#8B5E3C',
        ];
        $selectedColors = $request->input('colors', []);

        // Build existing per-color images map (supports both old 'image' and new 'images')
        $existingColors = is_array($product->available_colors)
            ? $product->available_colors
            : (json_decode($product->available_colors ?? '[]', true) ?? []);
        $existingColorImagesMap = [];
        foreach ($existingColors as $ec) {
            if (is_array($ec) && isset($ec['name'])) {
                if (!empty($ec['images'])) {
                    $existingColorImagesMap[$ec['name']] = $ec['images'];
                } elseif (!empty($ec['image'])) {
                    $existingColorImagesMap[$ec['name']] = [$ec['image']];
                } else {
                    $existingColorImagesMap[$ec['name']] = [];
                }
            }
        }

        // delete_color_images[ColorName][] = array of paths to remove
        $deleteColorImages = $request->input('delete_color_images', []);

        $colorUploadDir = $productUploadDir;

        $data['available_colors'] = array_values(array_map(
            function ($n) use ($colorMap, $existingColorImagesMap, $deleteColorImages, $request, $colorUploadDir) {
                $entry = ['name' => $n, 'hex' => $colorMap[$n] ?? '#cccccc'];

                // Start with existing images for this color
                $currentImgs = $existingColorImagesMap[$n] ?? [];

                // Remove images marked for deletion
                $toDelete = $deleteColorImages[$n] ?? [];
                foreach ($toDelete as $delPath) {
                    if (file_exists(public_path($delPath))) {
                        @unlink(public_path($delPath));
                    }
                    $currentImgs = array_values(array_filter($currentImgs, fn($p) => $p !== $delPath));
                }

                // Append newly uploaded images
                $files = $request->file("color_images.{$n}") ?? [];
                if (!is_array($files)) $files = [$files];
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($colorUploadDir, $filename);
                        $currentImgs[] = 'images/products/' . $filename;
                    }
                }

                if (!empty($currentImgs)) {
                    $entry['images'] = array_values($currentImgs);
                    $entry['image']  = $currentImgs[0];
                }
                return $entry;
            },
            $selectedColors
        ));
        $data['available_sizes'] = array_values($request->input('sizes', []));

        // Merge general gallery with per-color images (unique, gallery first)
        $allColorImgs = [];
        foreach ($data['available_colors'] as $col) {
            foreach ($col['images'] ?? (isset($col['image']) ? [$col['image']] : []) as $img) {
                $allColorImgs[] = $img;
            }
        }
        $mergedImages = array_values(array_unique(array_merge($galleryImages, $allColorImgs)));
        $data['images'] = $mergedImages;
        $data['image']  = $mergedImages[0] ?? null;

        $product->update($data);
        return redirect()->route('admin.products')->with('success', 'Product updated!');
    }

    public function destroyProduct(Product $product)
    {
        foreach ($product->images ?? [] as $imgPath) {
            $abs = public_path($imgPath);
            if (file_exists($abs)) unlink($abs);
        }
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted.');
    }

    public function categories()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();
        return view('admin.categories', compact('categories'));
    }

    public function collectionImages()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();

        return view('admin.collection', compact('categories'));
    }

    public function updateCollectionCategoryImage(Request $request, Category $category)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
        ]);

        $this->deleteCategoryImageFile($category->image);
        $category->update(['image' => $this->storeCategoryImageFile($request->file('image'))]);

        return redirect()->route('admin.collection')->with('success', "Immagine aggiornata per «{$category->getRawOriginal('name')}».");
    }

    public function destroyCollectionCategoryImage(Category $category)
    {
        $this->deleteCategoryImageFile($category->image);
        $category->update(['image' => null]);

        return redirect()->route('admin.collection')->with('success', 'Immagine rimossa.');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'name_en'        => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'description_en' => 'nullable|string',
            'image'          => 'nullable|image|max:4096',
            'sort_order'     => 'integer',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->hasFile('image')) {
            $data['image'] = $this->storeCategoryImageFile($request->file('image'));
        } else {
            unset($data['image']);
        }
        Category::create($data);
        return redirect()->route('admin.categories')->with('success', 'Category added!');
    }

    public function orders(Request $request)
    {
        $orders = Order::with('items')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load('items')]);
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status'         => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);
        $order->update($data);
        return redirect()->back()->with('success', 'Order status updated.');
    }

    public function discounts()
    {
        $discounts = Discount::latest()->paginate(20);
        return view('admin.discounts', compact('discounts'));
    }

    public function storeDiscount(Request $request)
    {
        $data = $request->validate([
            'code'       => 'required|string|unique:discounts',
            'type'       => 'required|in:percent,fixed',
            'value'      => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_uses'   => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);
        $data['code'] = strtoupper($data['code']);
        Discount::create($data);
        return redirect()->route('admin.discounts')->with('success', 'Discount created!');
    }

    public function reports()
    {
        $salesByMonth = Order::where('payment_status', 'paid')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as revenue, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $salesByCategory = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw('categories.name, SUM(order_items.quantity) as total_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        $topProducts = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('products.name, SUM(order_items.quantity) as total_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $pageViews = PageVisit::selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->limit(30)
            ->get();

        return view('admin.reports', compact('salesByMonth', 'salesByCategory', 'topProducts', 'pageViews'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'name_en'        => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'description_en' => 'nullable|string',
            'image'          => 'nullable|image|max:4096',
            'sort_order'     => 'nullable|integer',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        if ($request->hasFile('image')) {
            $this->deleteCategoryImageFile($category->image);
            $data['image'] = $this->storeCategoryImageFile($request->file('image'));
        } else {
            unset($data['image']);
        }
        $category->update($data);
        return redirect()->route('admin.categories')->with('success', 'Category updated!');
    }

    public function destroyCategory(Category $category)
    {
        $this->deleteCategoryImageFile($category->image);
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category deleted!');
    }

    protected function storeCategoryImageFile(\Illuminate\Http\UploadedFile $file): string
    {
        $dir = public_path('images/categories');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'images/categories/' . $filename;
    }

    protected function deleteCategoryImageFile(?string $path): void
    {
        if (!$path) {
            return;
        }
        $abs = public_path($path);
        if (file_exists($abs)) {
            unlink($abs);
        }
    }

    public function destroyDiscount(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('admin.discounts')->with('success', 'Discount deleted!');
    }

    public function gallery()
    {
        $photos = GalleryPhoto::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.gallery', compact('photos'));
    }

    public function storeGalleryPhoto(Request $request)
    {
        $request->validate([
            'photos'   => 'required|array|min:1',
            'photos.*' => 'required|image|max:4096',
        ]);

        $dir = public_path('images/gallery');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $maxOrder = GalleryPhoto::max('sort_order') ?? 0;

        foreach ($request->file('photos') as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            GalleryPhoto::create([
                'path'       => 'images/gallery/' . $filename,
                'alt'        => $request->input('alt', ''),
                'sort_order' => ++$maxOrder,
                'is_active'  => true,
            ]);
        }

        return redirect()->route('admin.gallery')->with('success', 'Foto aggiunte!');
    }

    public function destroyGalleryPhoto(GalleryPhoto $photo)
    {
        $abs = public_path($photo->path);
        if (file_exists($abs)) unlink($abs);
        $photo->delete();
        return redirect()->route('admin.gallery')->with('success', 'Foto eliminata!');
    }

    public function reorderGallery(Request $request)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);
        foreach ($request->input('order') as $index => $id) {
            GalleryPhoto::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        return response()->json(['ok' => true]);
    }

    public function analytics()
    {
        $days = 30;

        $viewsPerDay = PageVisit::selectRaw('DATE(created_at) as day, COUNT(*) as views')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $sessionsPerDay = PageVisit::selectRaw('DATE(created_at) as day, COUNT(DISTINCT session_id) as sessions')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $dayLabels    = [];
        $viewsData    = [];
        $sessionsData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $dayLabels[]    = $d;
            $viewsData[]    = $viewsPerDay->get($d)->views ?? 0;
            $sessionsData[] = $sessionsPerDay->get($d)->sessions ?? 0;
        }

        $devices = PageVisit::selectRaw('COALESCE(device_type, "desktop") as device, COUNT(*) as count')
            ->groupBy('device')
            ->pluck('count', 'device');

        $topPages = PageVisit::selectRaw('path, page_title, COUNT(*) as views')
            ->groupBy('path', 'page_title')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        $totalViews    = PageVisit::count();
        $totalSessions = PageVisit::distinct('session_id')->count('session_id');
        $todayViews    = PageVisit::whereDate('created_at', today())->count();

        return view('admin.analytics', compact(
            'dayLabels', 'viewsData', 'sessionsData',
            'devices', 'topPages',
            'totalViews', 'totalSessions', 'todayViews'
        ));
    }

    public function cookieConsents(Request $request)
    {
        $total     = CookieConsent::count();
        $byChoice  = CookieConsent::selectRaw('choice, COUNT(*) as count')->groupBy('choice')->pluck('count', 'choice');
        $analytics = CookieConsent::where('analytics', true)->count();
        $marketing = CookieConsent::where('marketing', true)->count();
        $byLocale  = CookieConsent::selectRaw('locale, COUNT(*) as count')->groupBy('locale')->pluck('count', 'locale');
        $byDay     = CookieConsent::selectRaw('DATE(created_at) as day, COUNT(*) as count')
                        ->groupBy('day')->orderBy('day')->limit(30)->get();
        $recent    = CookieConsent::latest()->limit(50)->get();

        return view('admin.cookie-consents', compact(
            'total', 'byChoice', 'analytics', 'marketing', 'byLocale', 'byDay', 'recent'
        ));
    }

    // ─── UTENTI ────────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $search = $request->input('q');

        // Registered users
        $registeredQuery = User::where('role', '!=', 'admin')
            ->withCount('orders')
            ->withSum('orders', 'total');

        if ($search) {
            $registeredQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $registeredUsers = $registeredQuery->orderByDesc('created_at')->paginate(20, ['*'], 'rp');

        // Guest buyers (orders with no user_id)
        $guestQuery = Order::whereNull('user_id')
            ->selectRaw('email, MAX(name) as name, MAX(phone) as phone, COUNT(*) as orders_count, SUM(total) as total_spent, MAX(created_at) as last_order_at')
            ->groupBy('email')
            ->orderByDesc('last_order_at');

        if ($search) {
            $guestQuery->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $guestBuyers = $guestQuery->paginate(20, ['*'], 'gp');

        return view('admin.users.index', compact('registeredUsers', 'guestBuyers', 'search'));
    }

    public function showUser($id)
    {
        // Support both registered (numeric id) and guest (email string)
        if (is_numeric($id)) {
            $user   = User::findOrFail($id);
            $orders = Order::where('user_id', $id)->with('items.product')->latest()->get();
            $isGuest = false;
        } else {
            $email  = urldecode($id);
            $user   = (object) [
                'name'       => Order::whereNull('user_id')->where('email', $email)->value('name'),
                'email'      => $email,
                'created_at' => null,
                'role'       => 'guest',
                'id'         => null,
            ];
            $orders  = Order::whereNull('user_id')->where('email', $email)->with('items.product')->latest()->get();
            $isGuest = true;
        }

        $totalSpent  = $orders->where('payment_status', 'paid')->sum('total');
        $orderCount  = $orders->count();
        $productIds  = $orders->flatMap(fn($o) => $o->items->pluck('product_id'))->unique();
        $boughtProducts = Product::whereIn('id', $productIds)->get();

        return view('admin.users.show', compact('user', 'orders', 'totalSpent', 'orderCount', 'boughtProducts', 'isGuest'));
    }

    // ─── HELPER: build recipient list ─────────────────────────────────────────

    private function buildRecipients(array $filters): \Illuminate\Support\Collection
    {
        $filterProductIds  = array_filter((array) ($filters['filter_product_ids']  ?? []));
        $filterCategoryIds = array_filter((array) ($filters['filter_category_ids'] ?? []));
        $minOrders         = (int) ($filters['min_orders'] ?? 0);
        $hasFilters        = !empty($filterProductIds) || !empty($filterCategoryIds);

        // target_registered / target_guests: checkbox sends "1" or nothing
        $targetRegistered = filter_var($filters['target_registered'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $targetGuests     = filter_var($filters['target_guests']     ?? false, FILTER_VALIDATE_BOOLEAN);

        $recipients = collect();

        // ── REGISTERED USERS ──────────────────────────────────────────────
        if ($targetRegistered) {
            $regQuery = User::where('role', '!=', 'admin');

            if ($hasFilters || $minOrders > 0) {
                // Only users who match the order filters
                $ordersQuery = Order::query();
                if (!empty($filterProductIds)) {
                    $ordersQuery->whereHas('items', fn($q) => $q->whereIn('product_id', $filterProductIds));
                }
                if (!empty($filterCategoryIds)) {
                    $ordersQuery->whereHas('items.product', fn($q) => $q->whereIn('category_id', $filterCategoryIds));
                }

                $qualifiedEmails = $ordersQuery
                    ->selectRaw('email, COUNT(*) as orders_count')
                    ->groupBy('email')
                    ->having('orders_count', '>=', max($minOrders, 1))
                    ->pluck('email');

                $regQuery->whereIn('email', $qualifiedEmails);
            }

            $regQuery->get()->each(function ($u) use (&$recipients) {
                $recipients->push(['email' => $u->email, 'name' => $u->name, 'type' => 'registered']);
            });
        }

        $regEmailList = $targetRegistered
            ? User::where('role', '!=', 'admin')->pluck('email')->toArray()
            : [];

        // ── GUEST BUYERS ──────────────────────────────────────────────────
        if ($targetGuests) {
            $guestQuery = Order::whereNull('user_id');

            if (!empty($filterProductIds)) {
                $guestQuery->whereHas('items', fn($q) => $q->whereIn('product_id', $filterProductIds));
            }
            if (!empty($filterCategoryIds)) {
                $guestQuery->whereHas('items.product', fn($q) => $q->whereIn('category_id', $filterCategoryIds));
            }

            $guestStats = $guestQuery
                ->selectRaw('email, MAX(name) as name, COUNT(*) as orders_count')
                ->groupBy('email')
                ->having('orders_count', '>=', max($minOrders, 1))
                ->get();

            foreach ($guestStats as $stat) {
                if (!in_array($stat->email, $regEmailList)) {
                    $recipients->push(['email' => $stat->email, 'name' => $stat->name, 'type' => 'guest']);
                }
            }
        }

        return $recipients->unique('email')->values();
    }

    // ─── CAMPAGNE EMAIL ────────────────────────────────────────────────────────

    public function campaigns()
    {
        $campaigns = EmailCampaign::latest()->paginate(15);
        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function createCampaign()
    {
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $campaign   = null;
        return view('admin.campaigns.form', compact('products', 'categories', 'campaign'));
    }

    public function storeCampaign(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:200',
            'subject'             => 'required|string|max:300',
            'body'                => 'required|string',
            'filter_product_ids'  => 'nullable|array',
            'filter_product_ids.*'=> 'integer|exists:products,id',
            'filter_category_ids' => 'nullable|array',
            'filter_category_ids.*'=> 'integer|exists:categories,id',
            'min_orders'          => 'integer|min:0',
            'target_registered'   => 'boolean',
            'target_guests'       => 'boolean',
        ]);

        $data['target_registered'] = $request->boolean('target_registered', false);
        $data['target_guests']     = $request->boolean('target_guests', false);
        $data['status']            = 'draft';

        $campaign = EmailCampaign::create($data);

        return redirect()->route('admin.campaigns')->with('success', 'Campagna creata.');
    }

    public function editCampaign(EmailCampaign $campaign)
    {
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.campaigns.form', compact('campaign', 'products', 'categories'));
    }

    public function updateCampaign(Request $request, EmailCampaign $campaign)
    {
        abort_if($campaign->status === 'sent', 403, 'Campagna già inviata.');

        $data = $request->validate([
            'name'                => 'required|string|max:200',
            'subject'             => 'required|string|max:300',
            'body'                => 'required|string',
            'filter_product_ids'  => 'nullable|array',
            'filter_product_ids.*'=> 'integer|exists:products,id',
            'filter_category_ids' => 'nullable|array',
            'filter_category_ids.*'=> 'integer|exists:categories,id',
            'min_orders'          => 'integer|min:0',
        ]);

        $data['target_registered'] = $request->boolean('target_registered', false);
        $data['target_guests']     = $request->boolean('target_guests', false);

        $campaign->update($data);

        return redirect()->route('admin.campaigns')->with('success', 'Campagna aggiornata.');
    }

    public function destroyCampaign(EmailCampaign $campaign)
    {
        abort_if($campaign->status === 'sending', 403, 'Invio in corso.');
        $campaign->delete();
        return back()->with('success', 'Campagna eliminata.');
    }

    public function previewRecipients(Request $request)
    {
        $recipients = $this->buildRecipients($request->all());
        return response()->json([
            'count'      => $recipients->count(),
            'registered' => $recipients->where('type', 'registered')->count(),
            'guests'     => $recipients->where('type', 'guest')->count(),
            'sample'     => $recipients->take(5)->map(fn($r) => $r['email'])->values(),
        ]);
    }

    public function sendCampaign(EmailCampaign $campaign)
    {
        abort_if($campaign->status === 'sent', 403, 'Campagna già inviata.');

        $recipients = $this->buildRecipients([
            'filter_product_ids'  => $campaign->filter_product_ids ?? [],
            'filter_category_ids' => $campaign->filter_category_ids ?? [],
            'min_orders'          => $campaign->min_orders,
            'target_registered'   => $campaign->target_registered,
            'target_guests'       => $campaign->target_guests,
        ]);

        $campaign->update(['status' => 'sending', 'total_recipients' => $recipients->count()]);

        $sentCount = 0;
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient['email'])->send(new CampaignMail($campaign, $recipient['name'] ?? ''));
                EmailCampaignLog::create([
                    'campaign_id' => $campaign->id,
                    'email'       => $recipient['email'],
                    'name'        => $recipient['name'] ?? null,
                    'status'      => 'sent',
                ]);
                $sentCount++;
            } catch (\Throwable $e) {
                EmailCampaignLog::create([
                    'campaign_id' => $campaign->id,
                    'email'       => $recipient['email'],
                    'name'        => $recipient['name'] ?? null,
                    'status'      => 'failed',
                    'error'       => substr($e->getMessage(), 0, 250),
                ]);
            }
        }

        $campaign->update([
            'status'     => 'sent',
            'sent_count' => $sentCount,
            'sent_at'    => now(),
        ]);

        $failedCount = $recipients->count() - $sentCount;
        $msg = "Campagna inviata a {$sentCount} destinatari.";
        if ($failedCount > 0) {
            $msg .= " {$failedCount} falliti — controlla il log della campagna.";
        }

        return redirect()->route('admin.campaigns.logs', $campaign)->with('success', $msg);
    }

    public function campaignLogs(EmailCampaign $campaign)
    {
        $logs = $campaign->logs()->latest()->paginate(50);
        return view('admin.campaigns.logs', compact('campaign', 'logs'));
    }
}
