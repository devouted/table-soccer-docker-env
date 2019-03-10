<?php

namespace App\Service;


class TokenGenerator
{
    public const DEFAULT_TOKEN_LENGTH = 32;

    public function generateToken(int $tokenLength = self::DEFAULT_TOKEN_LENGTH): string
    {
        try {
            return md5(random_bytes($tokenLength));
        } catch(\Exception $e) {
            return base64_encode(openssl_random_pseudo_bytes($tokenLength));
        }
    }
}