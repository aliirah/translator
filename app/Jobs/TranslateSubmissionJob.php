<?php

namespace App\Jobs;

use App\Enums\SubmissionStatus;
use App\Http\Integrations\OpenAI\OpenAIConnector;
use App\Http\Integrations\OpenAI\TranslateRequest;
use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use JsonException;
use RuntimeException;
use Throwable;

class TranslateSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function __construct(public int $submissionId) {}

    public function handle(): void
    {
        $submission = Submission::query()->findOrFail($this->submissionId);

        Log::info('TranslateSubmissionJob started', ['submission_id' => $submission->id]);

        $submission->update([
            'status' => SubmissionStatus::Processing,
            'error' => null,
        ]);

        $sourceText = json_encode([
            'name' => $submission->name,
            'title' => $submission->title,
            'description' => $submission->description,
        ], JSON_UNESCAPED_UNICODE);

        $connector = new OpenAIConnector;
        $request = new TranslateRequest(
            model: config('services.openai.model', 'gpt-5-mini'),
            sourceText: $sourceText,
            sourceLang: $submission->base_lang ?? 'en',
            targetLang: $submission->target_lang ?? 'es'
        );

        try {
            $payload = $connector->send($request)->throw()->json();

            $text = $this->extractOutputText($payload);
            $translated = $this->decodeJson($text);

            if (! $translated) {
                throw new RuntimeException('Unexpected translation format.');
            }

            $submission->update([
                'translated' => $translated,
                'status' => SubmissionStatus::Completed,
            ]);

            Log::info('TranslateSubmissionJob completed', ['submission_id' => $submission->id]);
        } catch (Throwable $e) {
            $submission->update([
                'status' => SubmissionStatus::Failed,
                'error' => $e->getMessage(),
            ]);

            Log::error('TranslateSubmissionJob failed', [
                'submission_id' => $submission->id,
                'message' => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    private function extractOutputText(array $payload): ?string
    {
        $direct = $payload['output_text'] ?? null;
        if (is_string($direct) && $direct !== '') {
            return $direct;
        }

        $output = $payload['output'] ?? [];
        if (is_array($output)) {
            foreach ($output as $item) {
                $contents = $item['content'] ?? [];
                if (is_array($contents)) {
                    foreach ($contents as $c) {
                        if (($c['type'] ?? null) === 'output_text' && isset($c['text']) && is_string($c['text'])) {
                            return $c['text'];
                        }
                    }
                }
            }
        }

        $chat = $payload['choices'][0]['message']['content'] ?? null;

        return is_string($chat) && $chat !== '' ? $chat : null;
    }

    private function decodeJson(?string $text): ?array
    {
        if (! is_string($text) || $text === '') {
            return null;
        }

        try {
            return json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
    }
}
