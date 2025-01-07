<?php
namespace Gui\Contracts;

interface Datatable
{
    /**
     * Renders and returns a string representation of the datatable.
     *
     * @return string The rendered output.
     */
    public function render(): string;
}