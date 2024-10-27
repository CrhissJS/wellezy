<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('parameterValidator', function ($app) {
            return new class {
                public function validateRequiredParameters(Request $request, array $requiredParams): array
                {
                    $missingParams = [];

                    foreach ($requiredParams as $param) {
                        if (is_null($request->$param)) {
                            $missingParams[] = $param;
                        }
                    }

                    return $missingParams;
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
