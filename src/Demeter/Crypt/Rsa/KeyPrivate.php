<?php

namespace Demeter\Crypt\Rsa;

use RuntimeException;

class KeyPrivate extends Key
{
    protected ?string $passphrase = null;
    protected ?KeyPublic $publicKey = null;

    /**
     * Constructor method to initialize the object with a PEM string and an optional passphrase.
     *
     * @param string|null $passphrase An optional passphrase for the provided PEM string. Defaults to null.
     *
     * @return void
     */
    public function __construct(string $pemString, ?string $passphrase = null)
    {
        $this->passphrase = $passphrase;
        parent::__construct($pemString);
    }

    /**
     * Retrieves the public key associated with this instance. If the public key has not already been instantiated, it initializes it using the provided key details.
     *
     * @return KeyPublic The public key object associated with this instance.
     */
    public function getPublicKey(): KeyPublic
    {
        if(null === $this->publicKey){
            $this->publicKey = new KeyPublic($this->details['key']);
        }

        return $this->publicKey;
    }

    /**
     * Parses a PEM formatted private key string and initializes the key resource and details.
     *
     * @throws RuntimeException If parsing the private key fails.
     */
    protected function parse(): void
    {
        $result = openssl_pkey_get_private($this->pemString, $this->passphrase);

        if(!$result)
        {
            throw new RuntimeException("Unable to parse private key");
        }

        $this->keyResource = $result;
        $this->details = openssl_pkey_get_details($this->keyResource);
    }
}