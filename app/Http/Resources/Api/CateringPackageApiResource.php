<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CateringPackageApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'is_popular' => $this->is_popular,
            'thumbnail'  => $this->thumbnail,
            'about'      => $this->about,
            // View only 1 Data (belong to)
            'city'     => new CityApiResource($this->whenLoaded('city')),
            'category' => new CategoryApiResource($this->whenLoaded('category')),
            'kitchen'  => new KitchenApiResource($this->whenLoaded('kitchen')),
            // View data more than 1 data (has many)
            'photos'       => CateringPhotoApiResource::collection($this->whenLoaded('photos')),
            'bonuses'      => CateringBonusApiResource::collection($this->whenLoaded('bonuses')),
            'testimonials' => CateringTestimonialApiResource::collection($this->whenLoaded('testimonials')),
            'tiers'        => CateringTierApiResource::collection($this->whenLoaded('tiers')),
        ];
    }
}
