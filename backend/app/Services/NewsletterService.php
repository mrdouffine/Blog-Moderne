<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\SendWelcomeNewsletterEmail;
use App\Models\Subscriber;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class NewsletterService
{
    /**
     * Abonne un email à la newsletter.
     *
     * Si l'email existe déjà mais s'était désabonné, il est réabonné.
     * Un job d'envoi d'email de bienvenue est dispatché de manière asynchrone.
     *
     * @param string $email
     * @return Subscriber
     */
    public function subscribe(string $email): Subscriber
    {
        // Vérifier si un abonné avec cet email existe déjà
        $subscriber = Subscriber::where('email', $email)->first();

        if ($subscriber) {
            // Cas de réabonnement : l'abonné s'était précédemment désabonné
            if ($subscriber->unsubscribed_at !== null) {
                $subscriber->update([
                    'unsubscribed_at' => null,
                    'subscribed_at'   => now(),
                    // Génération d'un nouveau token pour le lien de désinscription
                    'token'           => Str::uuid()->toString(),
                ]);
            }

            // Si déjà abonné et actif, on retourne simplement l'abonné existant
            return $subscriber->fresh();
        }

        // Création d'un nouvel abonné
        $subscriber = Subscriber::create([
            'email'         => $email,
            'token'         => Str::uuid()->toString(),
            'subscribed_at' => now(),
        ]);

        // Dispatch asynchrone du job d'email de bienvenue
        SendWelcomeNewsletterEmail::dispatch($subscriber);

        return $subscriber;
    }

    /**
     * Désabonne un email de la newsletter en enregistrant la date de désinscription.
     *
     * @param string $email
     * @return Subscriber|null Retourne null si l'email n'est pas trouvé
     */
    public function unsubscribe(string $email): ?Subscriber
    {
        $subscriber = Subscriber::where('email', $email)
            ->whereNull('unsubscribed_at')
            ->first();

        if (!$subscriber) {
            return null;
        }

        $subscriber->update([
            'unsubscribed_at' => now(),
        ]);

        return $subscriber->fresh();
    }

    /**
     * Récupère la liste paginée des abonnés avec filtre optionnel.
     *
     * @param array $filters Filtres disponibles : filter (active|unsubscribed), per_page
     * @return LengthAwarePaginator
     */
    public function getAllSubscribers(array $filters): LengthAwarePaginator
    {
        $query = Subscriber::query()->orderBy('subscribed_at', 'desc');

        // Filtre : abonnés actifs uniquement
        if (isset($filters['filter']) && $filters['filter'] === 'active') {
            $query->whereNull('unsubscribed_at');
        }

        // Filtre : désabonnés uniquement
        if (isset($filters['filter']) && $filters['filter'] === 'unsubscribed') {
            $query->whereNotNull('unsubscribed_at');
        }

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 20;

        return $query->paginate($perPage);
    }
}
