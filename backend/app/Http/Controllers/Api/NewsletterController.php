<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\SubscribeRequest;
use App\Http\Requests\Newsletter\UnsubscribeRequest;
use App\Http\Resources\SubscriberResource;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(
        private readonly NewsletterService $newsletterService
    ) {}

    /**
     * Abonne un email à la newsletter.
     * Gère également le cas du réabonnement d'un email précédemment désabonné.
     *
     * @param SubscribeRequest $request
     * @return JsonResponse
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $subscriber = $this->newsletterService->subscribe($request->validated('email'));

        return response()->json([
            'success' => true,
            'data'    => new SubscriberResource($subscriber),
            'message' => 'Inscription à la newsletter réussie.',
        ], 201);
    }

    /**
     * Désabonne un email de la newsletter.
     *
     * @param UnsubscribeRequest $request
     * @return JsonResponse
     */
    public function unsubscribe(UnsubscribeRequest $request): JsonResponse
    {
        $subscriber = $this->newsletterService->unsubscribe($request->validated('email'));

        if (!$subscriber) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Cet email n\'est pas abonné à la newsletter.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new SubscriberResource($subscriber),
            'message' => 'Désinscription de la newsletter réussie.',
        ], 200);
    }

    /**
     * Retourne la liste paginée des abonnés (réservé aux administrateurs).
     *
     * Query params : per_page, filter (active|unsubscribed)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function subscribers(Request $request): JsonResponse
    {
        $filters = $request->only(['per_page', 'filter']);

        $paginator = $this->newsletterService->getAllSubscribers($filters);

        return response()->json([
            'success' => true,
            'data'    => SubscriberResource::collection($paginator->items()),
            'message' => 'Abonnés récupérés.',
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ], 200);
    }
}
