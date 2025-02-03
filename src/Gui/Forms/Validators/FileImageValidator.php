<?php
namespace Gui\Forms\Validators;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Intervention\Image\ImageManager;
use Throwable;
use RuntimeException;

class FileImageValidator extends FileValidator
{
    /**
     * @inheritDoc
     */
    public function __construct(array|Collection $options = [], array|Collection $messages = [], array|Collection $flags = [])
    {
        if(!class_exists('Intervention\Image\ImageManager')){
            throw new RuntimeException('The Intervention Image library is not installed. Please run "composer require intervention/image" to install it.');
        }

        parent::__construct($options, $messages, $flags);
    }

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

        if($this->hasOption('maxWidth') && $dimensions[0] > $this->getOption('maxWidth')){
            throw new Error($this, 'maxWidth', ['name' => $file->getClientOriginalName(), 'width' => $this->getOption('maxWidth') . 'px']);
        }

        if($this->hasOption('minWidth') && $dimensions[0] < $this->getOption('minWidth')){
            throw new Error($this, 'minWidth', ['name' => $file->getClientOriginalName(), 'width' => $this->getOption('minWidth') . 'px']);
        }

        if($this->hasOption('maxHeight') && $dimensions[1] > $this->getOption('maxHeight')){
            throw new Error($this, 'maxHeight', ['name' => $file->getClientOriginalName(), 'height' => $this->getOption('maxHeight') . 'px']);
        }

        if($this->hasOption('minHeight') && $dimensions[1] < $this->getOption('minHeight')){
            throw new Error($this, 'minHeight', ['name' => $file->getClientOriginalName(), 'height' => $this->getOption('minHeight') . 'px']);
        }

        return $file;
    }

    /**
     * Retrieves the dimensions of the given image file.
     *
     * @param UploadedFile $file The uploaded image file for which dimensions will be determined.
     * @return array<int, int> An array containing the width and height of the image, respectively.
     */
    protected function getImageDimensions(UploadedFile $file): array
    {
        try {
            $image = (extension_loaded('imagick') ? ImageManager::imagick() : ImageManager::gd())->read($file->getRealPath());
            return [$image->width(), $image->height()];
        } catch(Throwable){
            return [0, 0];
        }
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