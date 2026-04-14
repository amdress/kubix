<?php

namespace App\Http\Controllers\Territory\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TerritorySuggestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'display_name'   => $this['display_name'] ?? null,
            'territory_type' => $this['territory_type'] ?? null,
            'latitude'       => $this['latitude'] ?? null,
            'longitude'      => $this['longitude'] ?? null,
            'country'        => $this['country'] ?? null,
            'state'          => $this['state'] ?? null,
            'city'           => $this['city'] ?? null,
            'neighborhood'   => $this['neighborhood'] ?? null,
        ];
    }
}