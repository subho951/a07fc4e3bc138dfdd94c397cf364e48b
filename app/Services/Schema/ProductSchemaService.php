<?php

namespace App\Services\Schema;

class ProductSchemaService
{
    public function generate($product)
    {
        
        $path = url('public/uploads/product/' . $product->cover_image);
        $productImage[] = str_replace('public/', '', $path);

        $schema = [
            "@context"      => "https://schema.org/",
            "@type"         => "Product",
            "name"          => $product->name,
            "image"         => $productImage,
            "description"   => strip_tags($product->short_description),
            "sku"           => $product->product_sku,
            // "brand" => [
            //     "@type" => "Brand",
            //     "name"  => $product->brand->name ?? null,
            // ],
            "offers" => [
                "@type"         => "Offer",
                "url"           => url('/product/'.$product->slug . '/' .urlencode(base64_encode($product->id))),
                "priceCurrency" => "INR",
                "price"         => $product->discounted_price ?? $product->discounted_price,
                "itemCondition" => "https://schema.org/NewCondition",
                "availability"  => $product->stock > 0 
                                   ? "https://schema.org/InStock" 
                                   : "https://schema.org/OutOfStock",
            ]
        ];

        // Include rating if available
        if ($product->rating_avg) {
            $schema["aggregateRating"] = [
                "@type"       => "AggregateRating",
                "ratingValue" => $product->rating_avg,
                "reviewCount" => $product->rating_count,
            ];
        }

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}