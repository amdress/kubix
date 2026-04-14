<?php

use Illuminate\Support\Facades\Broadcast;
use App\Kubix\Core\WebSocket\Channels\TerritoryChannel;
use Illuminate\Support\Facades\Log;

/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Realtime Territory Authorization (Basado en Path)
 * ════════════════════════════════════════════════════════════════
 */

/**
 * CANAL DE TERRITORIO (Universal)
 * Sustituye a branch.{id}, territory.{id} y cualquier otra pendejada de IDs.
 * * El parámetro {path} llegará desde el frontend como "1-5-10" 
 * y nuestro TerritoryChannel lo convertirá en "/1/5/10/".
 */
Broadcast::channel('territory.{path}', TerritoryChannel::class);

/**
 * NOTA DE SEGURIDAD:
 * Ya no usamos funciones anónimas aquí para no ensuciar las rutas.
 * Toda la lógica de "puedo entrar o no" está dentro de:
 * App\Kubix\Core\WebSocket\Channels\TerritoryChannel
 */

Log::debug("KUBIX Channels: Rutas de transmisión cargadas bajo el radar de Territorios.");