<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'base_lang'    => $this->base_lang,
            'target_lang'  => $this->target_lang,
            'original'     => [
                'name'        => $this->name,
                'title'       => $this->title,
                'description' => $this->description,
            ],
            'translated'   => $this->translated,
            'error'        => $this->error,
            'created_at'   => $this->created_at,
        ];
    }
}
