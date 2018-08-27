<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'name' => $this->name
        ];

        /* $response = $next($request);
        $response->setEncodingOptions(JSON_PRETTY_PRINT);
        return parent::toArray($request); */
    }
}
