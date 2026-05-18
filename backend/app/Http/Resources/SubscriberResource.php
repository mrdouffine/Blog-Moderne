<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int                  $id
 * @property string               $email
 * @property string               $token
 * @property \Carbon\Carbon       $subscribed_at
 * @property \Carbon\Carbon|null  $unsubscribed_at
 */
class SubscriberResource extends JsonResource
{
    /**
     * Le token de désinscription n'est jamais exposé dans l'API publique.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'email'           => $this->email,
            'is_active'       => $this->unsubscribed_at === null,
            'subscribed_at'   => $this->subscribed_at?->toIso8601String(),
            'unsubscribed_at' => $this->unsubscribed_at?->toIso8601String(),
        ];
    }
}
