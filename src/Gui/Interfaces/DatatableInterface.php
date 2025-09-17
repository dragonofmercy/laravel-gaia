<?php
namespace Gui\Interfaces;

use Illuminate\View\View;

interface DatatableInterface
{
    /**
     * Renders the content and returns the output.
     *
     * @return View|string The rendered output, which can be a View object or a string.
     */
    public function render(): View|string;
}