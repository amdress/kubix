<?php

namespace App\Http\Controllers\Auth;

use App\Kubix\Features\Branch\Dashboard\DashboardController;
use App\Models\Affiliation;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelData\Resource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id'          => $this['user']['id'],
                'name'        => $this['user']['name'],
                'role'        => $this['user']['role'],
                'avatar'      => $this['user']['avatar'],
                'permissions' => $this['user']['permissions'],
            ],

            /**
             * Context unificado.
             * El frontend espera TODO aquí:
             * label, branding, countryAccess, accessMap, activeBranch
             */
            'context' => [
                'label'         => $this['context_label'],
                'branding'      => $this['branding'],
                'countryAccess' => [], // TODO: resolver por rol cuando tengamos territorios
                'accessMap'     => $this['access_map'],
                'activeBranch'  => $this['access_map'][0] ?? null,
            ],

            'token' => $this['token'],
        ];
    }
}




