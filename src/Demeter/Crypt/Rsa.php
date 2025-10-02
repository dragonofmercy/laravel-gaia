<?php

namespace Demeter\Crypt;

use Demeter\Crypt\Rsa\Key;
use Demeter\Crypt\Rsa\KeyPrivate;
use Demeter\Crypt\Rsa\KeyPublic;
use RuntimeException;

class Rsa
{
    protected ?KeyPrivate $privateKey = null;
    protected ?KeyPublic $publicKey = null;
    protected ?string $pemString = null;
    protected ?string $passphrase = null;

    /**
     * Constructor method for initializing the instance with optional parameters.
     *
     * @param string|null $pemFile Path to the PEM file. If provided, its contents will be used as PEM string.
     * @param string|null $pemString PEM string data. If not provided and a PEM file is specified, the file contents will be used.
     * @param string|null $passphrase Passphrase for the PEM file or string.
     */
    public function __construct(?string $pemFile = null, ?string $pemString = null, ?string $passphrase = null)
    {
        $this->passphrase = $passphrase;

        if(null !== $pemFile){
            $pemString = file_get_contents($pemFile);
        }

        $this->setPemString($pemString);
    }

    /**
     * Encrypts the provided data using the specified encryption key.
     *
     * @param string $data The data to be encrypted.
     * @param Key $key The encryption key, which can be a private or public key.
     * @param bool $base64 Optional. If true, the encrypted data will be encoded in Base64. Defaults to false.
     *
     * @return string The encrypted data, optionally Base64 encoded if the $base64 parameter is set to true.
     */
    public function encrypt(string $data, Key $key, bool $base64 = false): string
    {
        $encrypted = "";

        if($key instanceof KeyPrivate){
            openssl_private_encrypt($data, $encrypted, $key->getKeyResource());
        } else {
            openssl_public_encrypt($data, $encrypted, $key->getKeyResource());
        }

        if($base64){
            $encrypted = base64_encode($encrypted);
        }

        return $encrypted;
    }

    /**
     * Decrypts the given encrypted data using the provided key.
     *
     * @param string $data The encrypted data to be decrypted.
     * @param Key $key The key used for decryption. Can be a private or public key.
     * @param bool $base64 Optional. Indicates if the data is base64 encoded. Defaults to false.
     * @return string The decrypted data.
     */
    public function decrypt(string $data, Key $key, $base64 = false): string
    {
        $decrypted = "";

        if($base64){
            $data = base64_decode($data);
        }

        if($key instanceof KeyPrivate){
            openssl_private_decrypt($data, $decrypted, $key->getKeyResource());
        } else {
            openssl_public_decrypt($data, $decrypted, $key->getKeyResource());
        }

        return $decrypted;
    }

    /**
     * Retrieves the private key.
     *
     * @return KeyPrivate|null The private key instance or null if not set.
     */
    public function getPrivateKey(): ?KeyPrivate
    {
        return $this->privateKey;
    }

    /**
     * Retrieves the public key associated with this instance.
     *
     * @return KeyPublic|null The public key if available, or null if not set.
     */
    public function getPublicKey(): ?KeyPublic
    {
        return $this->publicKey;
    }

    /**
     * Sets the PEM string and initializes the private and public key objects.
     *
     * @param string $pemString The PEM-encoded string to be used for key operations.
     * @return void
     */
    public function setPemString(string $pemString): void
    {
        $this->pemString = $pemString;

        try {
            $this->privateKey = new KeyPrivate($this->pemString, $this->passphrase);
            $this->publicKey = $this->privateKey->getPublicKey();
        } catch(RuntimeException) {
            $this->privateKey = null;
            $this->publicKey = new KeyPublic($this->pemString);
        }
    }
}