<?php

use App\Jobs\TranslateSubmissionJob;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('validate payload', function () {
    $res = $this->postJson('/api/submissions');
    $res->assertStatus(422)->assertJsonValidationErrors(['name', 'title', 'description']);
});

it('create submission', function () {
    Bus::fake();

    $payload = [
        'name' => 'Ali Rahgoshay',
        'title' => 'testing api',
        'description' => 'testing api description',
        'target_lang' => 'fr',
    ];
    $res = $this->postJson('/api/submissions', $payload);
    $res->assertAccepted();

    $submissionId = Submission::query()->latest()->value('id');
    expect($submissionId)->not()->toBeNull();

    Bus::assertDispatched(
        TranslateSubmissionJob::class, fn ($job) => $job->submissionId === 1
    );
});
