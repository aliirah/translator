<?php

use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('return submission resource', function () {
    $submission = Submission::factory()->completed()->create();

    $res = $this->getJson("/api/submissions/{$submission->id}");
    $res->assertOk()
        ->assertJsonFragment(['id' => $submission->id])
        ->assertJsonStructure(['data' => ['id','status','base_lang','target_lang','original','translated','error','created_at']]);
});
