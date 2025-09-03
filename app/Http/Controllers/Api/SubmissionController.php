<?php

namespace App\Http\Controllers\Api;

use App\Facades\Rest\Rest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\StoreRequest;
use App\Http\Resources\SubmissionResource;
use App\Jobs\TranslateSubmissionJob;
use App\Models\Submission;

class SubmissionController extends Controller
{
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $submission = Submission::query()->create($validated);
        $submission->refresh();

        TranslateSubmissionJob::dispatch($submission->id);
        /* we can have TranslateSubmissionJob::dispatch($submission->id)->onQueue('translations');
        but for keep it simple i'm not using named queues */

        // TODO - use telescope for monitoring the queues
        // TODO - install laravel pulse for finding bottlenecks

        return Rest::accepted(new SubmissionResource($submission));
    }

    public function show(Submission $submission)
    {
        return Rest::ok(new SubmissionResource($submission));
    }
}
