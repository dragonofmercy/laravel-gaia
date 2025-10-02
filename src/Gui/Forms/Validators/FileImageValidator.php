<?php

namespace Gui\Forms\Validators;

use Illuminate\Http\UploadedFile;
use Gui\Dto\ImageDimensions;

class FileImageValidator extends FileValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('maxHeight');
        $this->addOption('minHeight');
        $this->addOption('maxWidth');
        $this->addOption('minWidth');

        $this->setMessage('maxHeight', 'gui::validation.file.max_height');
        $this->setMessage('minHeight', 'gui::validation.file.min_height');
        $this->setMessage('maxWidth', 'gui::validation.file.max_width');
        $this->setMessage('minWidth', 'gui::validation.file.min_width');
    }

    /**
     * @inheritDoc
     */
    protected function validateFile(UploadedFile $file): UploadedFile
    {
        $file = parent::validateFile($file);

        if(!in_array($file->getMimeType(), $this->imageMimeTypes())){
            return $file;
        }

        $dimensions = $this->getImageDimensions($file);

        if($this->hasOption('maxWidth') && $dimensions->width > $this->getOption('maxWidth')){
            throw new Error($this, 'maxWidth', ['name' => $file->getClientOriginalName(), 'width' => $this->getOption('maxWidth') . 'px']);
        }

        if($this->hasOption('minWidth') && $dimensions->width < $this->getOption('minWidth')){
            throw new Error($this, 'minWidth', ['name' => $file->getClientOriginalName(), 'width' => $this->getOption('minWidth') . 'px']);
        }

        if($this->hasOption('maxHeight') && $dimensions->height > $this->getOption('maxHeight')){
            throw new Error($this, 'maxHeight', ['name' => $file->getClientOriginalName(), 'height' => $this->getOption('maxHeight') . 'px']);
        }

        if($this->hasOption('minHeight') && $dimensions->height < $this->getOption('minHeight')){
            throw new Error($this, 'minHeight', ['name' => $file->getClientOriginalName(), 'height' => $this->getOption('minHeight') . 'px']);
        }

        return $file;
    }

    /**
     * Retrieves the dimensions of an uploaded image file.
     *
     * @param UploadedFile $file The uploaded file whose dimensions need to be determined.
     * @return ImageDimensions The dimensions of the image as an ImageDimensions object.
     */
    protected function getImageDimensions(UploadedFile $file): ImageDimensions
    {
        $filePath = $file->getRealPath();

        return match (true) {
            extension_loaded('imagick') => $this->tryWithImagick($filePath),
            default => $this->tryWithGd($filePath)
        } ?? new ImageDimensions();
    }

    /**
     * Attempts to retrieve image dimensions using the Imagick library.
     *
     * @param string $filePath The file path of the image to analyze.
     * @return ImageDimensions|null An instance of ImageDimensions containing the width and height of the image,
     *                              or null if the operation fails.
     */
    protected function tryWithImagick(string $filePath): ImageDimensions|null
    {
        try {
            $image = new \Imagick($filePath);
            return new ImageDimensions($image->getImageWidth(), $image->getImageHeight());
        } catch (\ImagickException $e) {
            report($e);
            return null;
        }
    }

    /**
     * Attempts to retrieve the dimensions of an image using the GD library.
     *
     * @param string $filePath The path to the image file.
     * @return ImageDimensions|null An instance of ImageDimensions containing the width and height of the image,
     *                              or null if the dimensions could not be retrieved.
     */
    protected function tryWithGd(string $filePath): ImageDimensions|null
    {
        $imageSize = @getimagesize($filePath);
        return $imageSize ? new ImageDimensions($imageSize[0], $imageSize[1]) : null;
    }

    /**
     * Retrieves a list of supported image MIME types.
     *
     * @return array The supported image MIME types.
     */
    protected function imageMimeTypes(): array
    {
        return ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'];
    }
}