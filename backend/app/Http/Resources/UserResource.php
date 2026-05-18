<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $role
 * @property string|null $avatar
 * @property string|null $bio
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserResource extends JsonResource
{
    /**
     * Transforme le modèle User en tableau JSON.
     * Le mot de passe et les champs sensibles sont exclus.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role,
            'avatar'     => $this->avatar,
            'bio'        => $this->bio,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
