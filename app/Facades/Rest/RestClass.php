<?php

namespace App\Facades\Rest;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class RestClass
{
    public function response(mixed $data = null, int $code = Response::HTTP_OK): object
    {
        if ($data instanceof JsonResource) {
            return $data->response()->setStatusCode($code);
        }

        return response()->json($data)->setStatusCode($code);
    }

    public function ok(mixed $data = null): object
    {
        return $this->response($data ?? ['message' => 'ok']);
    }

    public function accepted(mixed $data = null): object
    {
        return $this->response($data ?? ['message' => 'accepted'], Response::HTTP_ACCEPTED);
    }

    public function badRequest(mixed $data = null): object
    {
        return $this->response($data ?? ['message' => 'bad request'], Response::HTTP_BAD_REQUEST);
    }

    public function unauthorized(mixed $data = null): object
    {
        return $this->response($data ?? ['message' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

    public function forbidden(mixed $data = null): object
    {
        return $this->response($data ?? ['message' => 'forbidden'], Response::HTTP_FORBIDDEN);
    }

    public function notFound(mixed $data = null): object
    {
        return $this->response($data ?? ['message' => 'not found'], Response::HTTP_NOT_FOUND);
    }

    public function error(mixed $data = null): object
    {
        $payload = config('app.debug')
            ? ($data ?? ['message' => 'internal server error'])
            : ['message' => __('status.error')];

        return $this->response($payload, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function custom(mixed $data, int $statusCode): object
    {
        return $this->response($data, $statusCode);
    }
}
