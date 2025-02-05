<?php
namespace Gui\Forms\Validators;

use Gui\Dto\ImageDimensions;
use Illuminate\Http\UploadedFile;

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
     * Retrieves the width and height dimensions of the given uploaded image file.
     *
     * @param UploadedFile $file The uploaded file whose image dimensions need to be extracted.
     * @return ImageDimensions An object containing the width and height of the image. If the dimensions
     *                         cannot be determined, the object will contain default values (e.g., 0).
     */
    protected function getImageDimensions(UploadedFile $file): ImageDimensions
    {
        $imageDimensions = new ImageDimensions();

        if(extension_loaded('imagick')){
            try {
                $image = new \Imagick($file->getRealPath());
                $imageDimensions->width = $image->getImageWidth();
                $imageDimensions->height = $image->getImageHeight();
                return $imageDimensions;
            } catch(\ImagickException $e){
                report($e);
            }
        }

        $imageSize = @getimagesize($file->getRealPath());

        if(false === $imageSize){
            return $imageDimensions;
        }

        $imageDimensions->width = $imageSize[0];
        $imageDimensions->height = $imageSize[1];

        return $imageDimensions;
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