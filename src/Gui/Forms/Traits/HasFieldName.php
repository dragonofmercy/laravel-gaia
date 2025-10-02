<?php

namespace Gui\Forms\Traits;

trait HasFieldName
{
    /**
     * Field name
     *
     * @var ?string
     */
    protected ?string $fieldName = null;

    /**
     * Set form instance
     *
     * @param ?string $name
     * @return void
     */
    public function setFieldName(?string $name): void
    {
        $this->fieldName = $name;
    }

    /**
     * Get field name
     *
     * @return ?string
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }
}