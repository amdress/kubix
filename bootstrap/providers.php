<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

   // Kubix 
    App\Integrations\Locations\Nominatim\NominatimServiceProvider::class,
];
