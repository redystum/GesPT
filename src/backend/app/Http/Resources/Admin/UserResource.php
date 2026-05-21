<?php

namespace app\Http\Resources\Admin;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'blocked' => $this->blocked,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'custom_permissions' => $this->relationLoaded('permissoes') ? $this->permissoes->count() : 0
        ];
    }
}
