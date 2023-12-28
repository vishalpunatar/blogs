<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
// use App\Models\Blog;
// use App\Models\User;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Route::model('blog', Blog::class);
        // Route::model('user', User::class);

        RateLimiter::for('api', function (Request $requestuest) {
            return Limit::perMinute(60)->by($requestuest->user()?->id ?: $requestuest->ip());
        });

        // Route::bind('blog', function ($value) {
        //     return Blog::where('id', $value)->firstOrFail();
        // });

        // Route::bind('user', function ($value) {
        //     return User::findOrFail($value);
        // });
        // $this->bindBlogModel();

        $this->routes(function () {
            Route::middleware(['auth:api'])->group(function() {
                require base_path('routes/admin.php');
                require base_path('routes/publisher.php');
                require base_path('routes/user.php');
            });
            
            Route::middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->prefix('web')
                ->group(base_path('routes/web.php'));
        });
    }

    // private function bindBlogModel()
    // {
    //     $this->bind('blog', function ($value) {
    //         return Blog::where('id', $value)->first() ?? abort(404);
    //     });
    // }
}
