<?php
namespace Demeter\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Hashing\AbstractHasher;

class Openssl extends AbstractHasher implements HasherContract
{
    /**
     * @inheritDoc
     */
    public function make($value, array $options = []): string
    {
        return base64_encode(openssl_encrypt($value, 'AES-256-CBC', $this->getPassphrase(), 1, $this->getIv()));
    }

    /**
     * @inheritDoc
     */
    public function check($value, $hashedValue, array $options = []): bool
    {
        if (is_null($hashedValue) || strlen($hashedValue) === 0) {
            return false;
        }

        return $value === $this->decode($hashedValue);
    }

    /**
     * @inheritDoc
     */
    public function needsRehash($hashedValue, array $options = []): bool
    {
        return false;
    }

    /**
     * Decode hashed value
     *
     * @param string|null $hashedValue
     * @return string
     */
    public function decode(string|null $hashedValue): string
    {
        if(null === $hashedValue){
            return "";
        }

        return rtrim(openssl_decrypt(base64_decode($hashedValue), 'AES-256-CBC', $this->getPassphrase(), 1, $this->getIv()));
    }

    /**
     * Return openssl IV
     *
     * @return string
     */
    protected function getIv(): string
    {
        return substr($this->getPassphrase(), -16);
    }

    /**
     * Get passphrase from config
     *
     * @return string
     */
    protected function getPassphrase(): string
    {
        return config('demeter.openssl_passphrase');
    }
}