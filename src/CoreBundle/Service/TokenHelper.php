<?php

namespace CoreBundle\Service;

use CoreBundle\Exception\InvalidTokenException;
use Namshi\JOSE\JWS;

class TokenHelper
{
	const ALG = 'HS256';

	private $_key;

	public function __construct($key)
	{
		$this->_key = $key;
	}

	public function encode(array $payload, $ttl = 86400)
    {
        $payload['iat'] = time();
        $payload['exp'] = time() + $ttl;
        $jws = new JWS([
            'typ' => 'JWS',
            'alg' => self::ALG,
        ]);

        $jws->setPayload($payload);
        $jws->sign($this->_key);

        return $jws->getTokenString();
    }

    public function decode($token)
    {
        $jws = JWS::load($token);

        if (!$jws->verify($this->_key, self::ALG)) {
            throw new InvalidTokenException('Invalid Token');
        }

        if ($this->isExpired($payload = $jws->getPayload())) {
            throw new InvalidTokenException('Expired Token');
        }

        return $payload;
    }

    private function isExpired($payload)
    {
        if (isset($payload['exp']) && is_numeric($payload['exp'])) {
            return (time() - $payload['exp']) > 0;
        }
        return false;
    }
}