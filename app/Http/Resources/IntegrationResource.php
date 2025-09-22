<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'platform' => $this->platform,
            'api_key' => $this->api_key,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
