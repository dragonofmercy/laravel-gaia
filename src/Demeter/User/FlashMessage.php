<?php
namespace Demeter\User;

class FlashMessage
{
    /**
     * Flash message
     * @var string
     */
    public string $message = "";

    /**
     * Flash flag
     *
     * @var FlashMessageType
     */
    public FlashMessageType $flag = FlashMessageType::SUCCESS;

    /**
     * Instantiate class from array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $flash = new self();
        $flash->message = $data['message'];
        $flash->flag = FlashMessageType::from($data['flag']);

        return $flash;
    }
}