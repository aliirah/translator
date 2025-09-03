<?php

namespace App\Http\Integrations\OpenAI;

use Saloon\Http\Connector;

class OpenAIConnector extends Connector
{

    public function resolveBaseUrl(): string
    {
        return 'https://api.openai.com/v1';
    }

    public function defaultHeaders(): array
    {
        return [
          'Authorization' => 'Bearer ' . config('services.openai.key'),
        ];
    }
}
