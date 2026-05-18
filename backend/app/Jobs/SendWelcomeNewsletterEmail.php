<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeNewsletterEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives maximum en cas d'échec.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * Délai en secondes entre chaque tentative (backoff exponentiel).
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * Timeout maximum pour l'exécution du job (en secondes).
     *
     * @var int
     */
    public int $timeout = 60;

    /**
     * @param Subscriber $subscriber L'abonné qui vient de s'inscrire
     */
    public function __construct(
        public readonly Subscriber $subscriber
    ) {}

    /**
     * Envoie l'email de bienvenue à l'abonné.
     *
     * En production, remplacer le Log::info par un vrai Mailable :
     *   Mail::to($this->subscriber->email)->send(new WelcomeNewsletterMail($this->subscriber));
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Envoi de l\'email de bienvenue newsletter', [
            'email' => $this->subscriber->email,
            'token' => $this->subscriber->token,
        ]);

        /*
        |--------------------------------------------------------------------------
        | TODO : Remplacer par un vrai Mailable Laravel quand le mail est configuré
        |--------------------------------------------------------------------------
        |
        | Exemple d'utilisation avec un Mailable :
        |
        |   Mail::to($this->subscriber->email)
        |       ->send(new \App\Mail\WelcomeNewsletterMail($this->subscriber));
        |
        */

        // Simulation d'un envoi d'email avec Mail::raw pour les environnements de dev
        if (config('app.env') !== 'testing') {
            try {
                Mail::raw(
                    $this->buildEmailBody(),
                    function ($message) {
                        $message->to($this->subscriber->email)
                                ->subject('Bienvenue sur notre newsletter !');
                    }
                );

                Log::info('Email de bienvenue envoyé avec succès', [
                    'email' => $this->subscriber->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Échec de l\'envoi de l\'email de bienvenue', [
                    'email' => $this->subscriber->email,
                    'error' => $e->getMessage(),
                ]);

                // Relancer l'exception pour que le job soit marqué comme échoué
                throw $e;
            }
        }
    }

    /**
     * Callback appelé quand toutes les tentatives ont échoué.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job SendWelcomeNewsletterEmail définitivement échoué', [
            'email'     => $this->subscriber->email,
            'exception' => $exception->getMessage(),
        ]);
    }

    /**
     * Construit le corps de l'email de bienvenue.
     *
     * @return string
     */
    private function buildEmailBody(): string
    {
        $unsubscribeUrl = config('app.frontend_url')
            . '/newsletter/unsubscribe?token='
            . $this->subscriber->token;

        return sprintf(
            "Bonjour,\n\nMerci de vous être inscrit(e) à notre newsletter !\n\n" .
            "Vous recevrez désormais nos derniers articles et actualités directement dans votre boîte mail.\n\n" .
            "Pour vous désabonner à tout moment, cliquez sur ce lien :\n%s\n\n" .
            "À bientôt,\nL'équipe du blog",
            $unsubscribeUrl
        );
    }
}
