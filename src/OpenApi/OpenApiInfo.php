<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Lift User Records API',
    description: 'REST API for managing user records'
)]
#[OA\Server(url: 'http://localhost:8080', description: 'Local server')]
class OpenApiInfo
{
}