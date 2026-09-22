<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\CartWebController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\AdminWebController;

Route::get("/", [HomeController::class, "index"])->name("home");

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'it'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get("/login", [AuthWebController::class, "showLogin"])->name("login");
Route::post("/login", [AuthWebController::class, "login"])->name("login.submit");
Route::get("/register", [AuthWebController::class, "showRegister"])->name("register");
Route::post("/register", [AuthWebController::class, "register"])->name("register.submit");
Route::post("/logout", [AuthWebController::class, "logout"])->name("logout");

Route::get("/collection", [ShopController::class, "collection"])->name("collection");
Route::get("/collection/{slug}", [ShopController::class, "category"])->name("category.show");
Route::get("/product/{slug}", [ShopController::class, "product"])->name("product.show");
Route::get("/best-sellers", [ShopController::class, "bestSellers"])->name("best-sellers");
Route::get("/photos", [ShopController::class, "photos"])->name("photos");
Route::get("/about", [ShopController::class, "about"])->name("about");

Route::post("/cart/add", [CartWebController::class, "add"])->name("cart.add");
Route::post("/cart/update", [CartWebController::class, "update"])->name("cart.update");
Route::post("/cart/remove", [CartWebController::class, "remove"])->name("cart.remove");
Route::post("/cart/discount", [CartWebController::class, "applyDiscount"])->name("cart.discount");

Route::get("/checkout", [CartWebController::class, "checkout"])->name("checkout");
Route::post("/checkout", [CartWebController::class, "placeOrder"])->name("checkout.submit");
Route::get("/checkout/payment/{order}", [CartWebController::class, "payment"])->name("checkout.payment");
Route::post("/checkout/payment/{order}", [CartWebController::class, "processPayment"])->name("checkout.pay");
Route::get("/order/confirmation/{order}", [CartWebController::class, "confirmation"])->name("order.confirmation");

Route::middleware("auth")->group(function () {
    Route::get("/account", [HomeController::class, "account"])->name("account");
    Route::get("/account/orders", [HomeController::class, "orderHistory"])->name("account.orders");
});

Route::prefix("admin")->name("admin.")->group(function () {
    Route::get("/", [AdminWebController::class, "dashboard"])->name("dashboard");
    Route::get("/products", [AdminWebController::class, "products"])->name("products");
    Route::get("/products/create", [AdminWebController::class, "createProduct"])->name("products.create");
    Route::post("/products", [AdminWebController::class, "storeProduct"])->name("products.store");
    Route::get("/products/{product}/edit", [AdminWebController::class, "editProduct"])->name("products.edit");
    Route::put("/products/{product}", [AdminWebController::class, "updateProduct"])->name("products.update");
    Route::delete("/products/{product}", [AdminWebController::class, "destroyProduct"])->name("products.destroy");
    Route::get("/categories", [AdminWebController::class, "categories"])->name("categories");
    Route::post("/categories", [AdminWebController::class, "storeCategory"])->name("categories.store");
    Route::put("/categories/{category}", [AdminWebController::class, "updateCategory"])->name("categories.update");
    Route::delete("/categories/{category}", [AdminWebController::class, "destroyCategory"])->name("categories.delete");
    Route::get("/collection", [AdminWebController::class, "collectionImages"])->name("collection");
    Route::post("/collection/{category}/image", [AdminWebController::class, "updateCollectionCategoryImage"])->name("collection.image");
    Route::delete("/collection/{category}/image", [AdminWebController::class, "destroyCollectionCategoryImage"])->name("collection.image.destroy");
    Route::get("/orders", [AdminWebController::class, "orders"])->name("orders");
    Route::get("/orders/{order}", [AdminWebController::class, "showOrder"])->name("orders.show");
    Route::post("/orders/{order}/status", [AdminWebController::class, "updateOrderStatus"])->name("orders.status");
    Route::patch("/orders/{order}/status", [AdminWebController::class, "updateOrderStatus"]);
    Route::get("/discounts", [AdminWebController::class, "discounts"])->name("discounts");
    Route::post("/discounts", [AdminWebController::class, "storeDiscount"])->name("discounts.store");
    Route::delete("/discounts/{discount}", [AdminWebController::class, "destroyDiscount"])->name("discounts.delete");
    Route::get("/reports", [AdminWebController::class, "reports"])->name("reports");
    Route::get("/gallery", [AdminWebController::class, "gallery"])->name("gallery");
    Route::post("/gallery", [AdminWebController::class, "storeGalleryPhoto"])->name("gallery.store");
    Route::delete("/gallery/{photo}", [AdminWebController::class, "destroyGalleryPhoto"])->name("gallery.destroy");
    Route::post("/gallery/reorder", [AdminWebController::class, "reorderGallery"])->name("gallery.reorder");
    Route::get("/cookie-consents", [AdminWebController::class, "cookieConsents"])->name("cookie-consents");
    Route::get("/analytics", [AdminWebController::class, "analytics"])->name("analytics");

    // Utenti
    Route::get("/users", [AdminWebController::class, "users"])->name("users");
    Route::get("/users/{id}", [AdminWebController::class, "showUser"])->name("users.show");

    // Campagne email
    Route::get("/campaigns", [AdminWebController::class, "campaigns"])->name("campaigns");
    Route::get("/campaigns/create", [AdminWebController::class, "createCampaign"])->name("campaigns.create");
    Route::post("/campaigns", [AdminWebController::class, "storeCampaign"])->name("campaigns.store");
    Route::get("/campaigns/{campaign}/edit", [AdminWebController::class, "editCampaign"])->name("campaigns.edit");
    Route::put("/campaigns/{campaign}", [AdminWebController::class, "updateCampaign"])->name("campaigns.update");
    Route::delete("/campaigns/{campaign}", [AdminWebController::class, "destroyCampaign"])->name("campaigns.destroy");
    Route::post("/campaigns/{campaign}/send", [AdminWebController::class, "sendCampaign"])->name("campaigns.send");
    Route::get("/campaigns/{campaign}/logs", [AdminWebController::class, "campaignLogs"])->name("campaigns.logs");
    Route::post("/campaigns/preview-recipients", [AdminWebController::class, "previewRecipients"])->name("campaigns.preview-recipients");
});

Route::get("/sitemap.xml", [HomeController::class, "sitemap"])->name("sitemap");
Route::post("/cookie-consent", [HomeController::class, "cookieConsent"])->name("cookie.consent");
