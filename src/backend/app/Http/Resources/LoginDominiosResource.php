<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\RolesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginDominiosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dominio' => $this->dominio,
            'role' => RolesResource::make($this->whenLoaded('role')),
        ];
    }
}
