<?php

namespace Invoicebox\V3;

use Invoicebox\Exceptions\ApiUnauthorizedException;
use Invoicebox\V3\Api;

final class Authentication
{
    /**
     * @return bool
     * @throws \Invoicebox\Exceptions\ApiNotConfiguredException
     * @throws \Invoicebox\Exceptions\InvalidRequestException
     * @throws \Invoicebox\Exceptions\NotFoundException
     * @throws \Invoicebox\Exceptions\OperationErrorException
     */
    public static function authenticate()
    {
        try {
            $result = Api::get('/v3/security/api/auth/auth');
            if(is_array($result) && isset($result["data"]) && key_exists("userId", $result["data"])) return true;
        } catch (ApiUnauthorizedException $e) {
            return false;
        }

        return false;
    }
}
