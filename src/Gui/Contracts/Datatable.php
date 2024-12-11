<?php
namespace Gui\Contracts;

interface Datatable
{
    /**
     * Render datatable
     *
     * @return string
     */
    public function render(): string;
}