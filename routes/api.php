<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AboutContentController;
use App\Http\Controllers\Api\ContactInfoController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ExpertiseItemController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\PortfolioProjectController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\Admin\AboutContentController as AdminAboutContentController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\ContactInfoController as AdminContactInfoController;
use App\Http\Controllers\Api\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Api\Admin\ConversationController as AdminConversationController;
use App\Http\Controllers\Api\Admin\ExpertiseItemController as AdminExpertiseItemController;
use App\Http\Controllers\Api\Admin\HeroSlideController as AdminHeroSlideController;
use App\Http\Controllers\Api\Admin\PortfolioProjectController as AdminPortfolioProjectController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\TeamMemberController as AdminTeamMemberController;
use App\Http\Controllers\Api\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hero-slides', [HeroSlideController::class, 'index']);
Route::get('/about', [AboutContentController::class, 'show']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::get('/portfolio-projects', [PortfolioProjectController::class, 'index']);
Route::get('/expertise-items', [ExpertiseItemController::class, 'index']);
Route::get('/team-members', [TeamMemberController::class, 'index']);
Route::get('/testimonials', [TestimonialController::class, 'index']);
Route::get('/contact-info', [ContactInfoController::class, 'show']);
Route::post('/contact-messages', [ContactMessageController::class, 'store'])->middleware(['auth:sanctum', 'contact.sender']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::get('/me', [LoginController::class, 'me']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('/contact-messages', [ContactMessageController::class, 'index']);
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
});

Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/me', [AdminAuthController::class, 'me']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);

        Route::apiResource('hero-slides', AdminHeroSlideController::class);
        Route::apiResource('services', AdminServiceController::class);
        Route::apiResource('portfolio-projects', AdminPortfolioProjectController::class);
        Route::apiResource('expertise-items', AdminExpertiseItemController::class);
        Route::apiResource('team-members', AdminTeamMemberController::class);
        Route::apiResource('testimonials', AdminTestimonialController::class);
        Route::apiResource('users', AdminUserController::class);

        Route::get('/about', [AdminAboutContentController::class, 'show']);
        Route::put('/about', [AdminAboutContentController::class, 'update']);
        Route::get('/contact-info', [AdminContactInfoController::class, 'show']);
        Route::put('/contact-info', [AdminContactInfoController::class, 'update']);

        Route::get('/contact-messages', [AdminContactMessageController::class, 'index']);
        Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show']);
        Route::post('/contact-messages/{contactMessage}/reply', [AdminContactMessageController::class, 'reply']);
        Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy']);

        Route::get('/conversations', [AdminConversationController::class, 'index']);
        Route::get('/conversations/{conversation}', [AdminConversationController::class, 'show']);
        Route::post('/conversations/{conversation}/messages', [AdminConversationController::class, 'sendMessage']);
    });
});
