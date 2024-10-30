<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('comments', function () {
    // Verificar si el usuario es administrador
    return true;
});
