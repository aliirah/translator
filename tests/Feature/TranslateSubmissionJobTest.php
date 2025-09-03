<?php

use App\Enums\SubmissionStatus;
use App\Jobs\TranslateSubmissionJob;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses(RefreshDatabase::class);

beforeEach(function () {
    MockClient::destroyGlobal();
});

it('marks submission completed on successful OpenAI response', function () {
    $submission = Submission::factory()->create([
        'name'        => 'Ali Rahgoshay',
        'title'       => 'Testing an API',
        'description' => 'Using Postman to verify JSON in responses.',
        'base_lang'   => 'en',
        'target_lang' => 'es',
        'status'      => SubmissionStatus::Pending->value,
    ]);

    $openAiPayload = [
        'object' => 'response',
        'status' => 'completed',
        'output' => [
            ['type' => 'reasoning', 'summary' => []],
            [
                'type'    => 'message',
                'status'  => 'completed',
                'role'    => 'assistant',
                'content' => [[
                    'type' => 'output_text',
                    'text' => json_encode([
                        'name'        => 'Ali Rahgoshay',
                        'title'       => 'es title',
                        'description' => 'es description',
                    ], JSON_UNESCAPED_UNICODE),
                ]],
            ],
        ],
    ];

    MockClient::global([
        'https://api.openai.com/v1/responses' => MockResponse::make($openAiPayload, 200),
    ]);

    (new TranslateSubmissionJob($submission->id))->handle();

    $submission->refresh();

    expect($submission->status->value)->toBe(SubmissionStatus::Completed->value)
        ->and($submission->translated)->toMatchArray([
            'name' => 'Ali Rahgoshay',
            'title' => 'es title',
            'description' => 'es description',
        ])
        ->and($submission->error)->toBeNull();
});


it('marks submission failed on non-200 from OpenAI', function () {
    $submission = Submission::factory()->create([
        'name'        => 'Ali Rahgoshay',
        'title'       => 'Testing an API',
        'description' => 'Using Postman to verify JSON in responses.',
        'base_lang'   => 'en',
        'target_lang' => 'es',
        'status'      => SubmissionStatus::Pending->value,
    ]);

    $errorBody = [
        'error' => [
            'message' => "invalid response",
        ],
    ];

    MockClient::global([
        'https://api.openai.com/v1/responses' => MockResponse::make($errorBody, 400),
    ]);

    try {
        (new TranslateSubmissionJob($submission->id))->handle();
        $this->fail('Job should throw and mark as failed');
    } catch (Throwable $e) {

    }

    $submission->refresh();

    expect($submission->status->value)->toBe(SubmissionStatus::Failed->value)
        ->and($submission->error)->not->toBeNull();
});
