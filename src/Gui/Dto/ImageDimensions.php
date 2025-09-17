<?php
namespace Gui\Dto;

class ImageDimensions
{
    /**
     * @param int $width
     * @param int $height
     */
    public function __construct(int $width = 0, int $height = 0)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Represents the width of an element, initialized to zero.
     */
    public int $width = 0;

    /**
     * Represents the height of an element, initialized to zero.
     */
    public int $height = 0;
}