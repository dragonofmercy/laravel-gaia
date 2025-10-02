<?php

namespace Demeter\Crypt\Rsa;

use Countable;
use OpenSSLAsymmetricKey;

abstract class Key implements Countable
{
    protected string $pemString = "";
    protected array|false $details = false;
    protected OpenSSLAsymmetricKey $keyResource;

    /**
     * Constructor method.
     *
     * @param string $pemString PEM formatted string to be parsed.
     * @return void
     */
    public function __construct(string $pemString)
    {
        $this->pemString = $pemString;
        $this->parse();
    }

    /**
     * Retrieves the OpenSSL asymmetric key resource.
     *
     * @return OpenSSLAsymmetricKey The OpenSSL asymmetric key resource.
     */
    public function getKeyResource(): OpenSSLAsymmetricKey
    {
        return $this->keyResource;
    }

    /**
     * Counts and returns a specific value based on the details property.
     *
     * @return int Returns 0 if details are false; otherwise, returns the 'bits' value from the details array.
     */
    public function count(): int
    {
        if(false === $this->details)
            return 0;

        return $this->details['bits'];
    }

    /**
     * Retrieves the type from the details property.
     *
     * @return string Returns the type if available, or an empty string if details are not set or invalid.
     */
    public function getType(): string
    {
        if(false === $this->details)
            return '';

        return $this->details['type'];
    }

    /**
     * Parses the provided PEM formatted string.
     *
     * @return void
     */
    abstract protected function parse(): void;
}