<?php
namespace Demeter\Crypt\Rsa;

use RuntimeException;

class KeyPublic extends Key
{
    /**
     * Parses a PEM formatted public key string and retrieves its details.
     *
     * @throws RuntimeException If the public key cannot be parsed.
     */
    protected function parse(): void
    {
        $result = openssl_get_publickey($this->pemString);

        if(!$result){
            throw new RuntimeException('Unable to parse public key');
        }

        $this->keyResource = $result;
        $this->details = openssl_pkey_get_details($this->keyResource);
    }
}