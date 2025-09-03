<?php

namespace App\Facades\Rest;

use Illuminate\Support\Facades\Facade;

/**
 * @method static ok(mixed $data = null)
 * @method static accepted(mixed $data)
 * @method static badRequest(mixed $data)
 * @method static unauthorized(mixed $data)
 * @method static forbidden(mixed $data)
 * @method static notFound(mixed $data = null)
 * @method static error(mixed $data)
 * @method static custom(mixed $data , int $statusCode)
 */
class Rest extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'restClass';
    }
}
