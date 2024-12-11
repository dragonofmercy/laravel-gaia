<?php
namespace Gui\Forms\Validators;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Number;

class FileValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addOption('max');
        $this->addOption('min');
        $this->addOption('maxSize');
        $this->addOption('mimeTypes');
        $this->addOption('multiple', false);

        $this->setMessage('min', 'gui::validation.file.min');
        $this->setMessage('max', 'gui::validation.file.max');
        $this->setMessage('maxSize', 'gui::validation.file.max_size');
        $this->setMessage('mimeTypes', 'gui::validation.file.mime_types');
    }

    /**
     * @inheritDoc
     */
    protected function getEmptyValue() : ?array
    {
        return $this->getOption('multiple') ? [] : null;
    }

    /**
     * @inheritDoc
     * @return UploadedFile|array<int,UploadedFile>
     */
    protected function validate(mixed $v) : UploadedFile|array|null
    {
        if(null === $v || is_string($v)){
            return $this->getEmptyValue();
        }

        if(is_array($v)){
            if($this->hasOption('min') && count($v) > $this->getOption('min')){
                throw new Error($this, 'min', ['count' => count($v), 'min' => $this->getOption('min')]);
            }

            if($this->hasOption('max') && count($v) > $this->getOption('max')){
                throw new Error($this, 'max', ['count' => count($v), 'max' => $this->getOption('max')]);
            }

            $collection = collect($v)->filter(function(mixed $file){
                return $file instanceof UploadedFile;
            });

            return $collection->transform(function(UploadedFile $file){
                return $this->validateFile($file);
            })->toArray();
        }

        return $this->validateFile($v);
    }

    /**
     * Validate uploaded file
     *
     * @param UploadedFile $file
     * @return UploadedFile
     */
    protected function validateFile(UploadedFile $file) : UploadedFile
    {
        if($this->hasOption('maxSize') && $file->getSize() > $this->getOption('maxSize')){
            throw new Error($this, 'maxSize', ['name' => $file->getClientOriginalName(), 'size' => Number::fileSize($this->getOption('maxSize'))]);
        }

        if($this->hasOption('mimeTypes')){
            $mimes = $this->getOption('mimeTypes');
            if(!is_array($mimes)){
                $mimes = [$mimes];
            }

            if(!in_array($file->getMimeType(), $mimes)){
                throw new Error($this, 'mimeTypes', ['name' => $file->getClientOriginalName(), 'mime' => $file->getMimeType()]);
            }
        }

        return $file;
    }
}