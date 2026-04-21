<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Cell;
use App\Models\Game;
use App\Models\GameQuest;
use App\Models\IssuedReward;
use App\Models\PackagingSize;
use App\Models\PackagingType;
use App\Models\Play;
use App\Models\PlayResponse;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductDisplay;
use App\Models\Quest;
use App\Models\Reward;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\SiteSetting;
use App\Models\SpinSegment;
use App\Models\User;
use App\Observers\AuditableObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('access-dashboard-api', function (User $user) {
            return (bool) $user->is_active;
        });

        foreach ($this->auditedModels() as $modelClass) {
            $modelClass::observe(AuditableObserver::class);
        }
    }

    private function auditedModels(): array
    {
        return [
            User::class,
            Role::class,
            Brand::class,
            Category::class,
            PackagingType::class,
            PackagingSize::class,
            Product::class,
            Price::class,
            Cell::class,
            ProductDisplay::class,
            Ad::class,
            Sale::class,
            SaleLine::class,
            Game::class,
            Quest::class,
            GameQuest::class,
            Reward::class,
            SpinSegment::class,
            Play::class,
            PlayResponse::class,
            IssuedReward::class,
            SiteSetting::class,
        ];
    }
}
