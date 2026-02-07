<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\Schema\ProductSchemaService;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Auto inject schema into your product details page
        View::composer('front.pages.product-details', function ($view) {

            if (!isset($view->product)) {
                return; 
            }

            $product = $view->product;

            $schema = app(ProductSchemaService::class)->generate($product);

            $view->with('schema', $schema);
        });
    }
}
