<?php

namespace App\Http\Responses;

use App\Support\FrontendReturnRoutes;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class FrontendLogoutResponse implements LogoutResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        return redirect()->route(FrontendReturnRoutes::resolve($request->input('return')) ?? 'home');
    }
}
