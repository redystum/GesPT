<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserShowResource extends JsonResource
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
            'recovery_email' => $this->recovery_email,
            'type' => $this->type,
            'roles' => RolesShowResource::collection($this->whenLoaded('roles')),
            'custom_permissions' => PermissionsResource::collection($this->whenLoaded('permissoes'))
        ];
    }
}
