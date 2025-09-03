<?php

namespace App\Http\Integrations\OpenAI;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class TranslateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $model,
        protected string $sourceText,
        protected string $sourceLang,
        protected string $targetLang
    ) {}

    public function resolveEndpoint(): string
    {
        return '/responses';
    }

    protected function prompt(): string
    {
        return "Translate the following JSON fields from {$this->sourceLang} to {$this->targetLang}. Return only JSON with the same keys: name, title, description.";
    }

    protected function defaultBody(): array
    {
        return [
            'model' => $this->model,
            'input' => [[
                'role' => 'user',
                'content' => [[
                    'type' => 'input_text',
                    'text' => $this->prompt()."\n\n".$this->sourceText,
                ]],
            ]],
            'text' => [
                'format' => [
                    'type' => 'json_schema',
                    'name' => 'translation',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'title' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                        ],
                        'required' => ['name', 'title', 'description'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        ];
    }
}
