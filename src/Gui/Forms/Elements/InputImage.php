<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;

class InputImage extends InputText
{
    protected string $layout = <<<EOF
<div class="gui-control-image">
    {placeholder_field}
    <div class="background">
        <div class="thumbnail empty" style="width:{w}px;height:{h}px">
            <div class="control">
                <a href data-trigger="upload" data-bs-toggle="tooltip" title="{string.upload}"><i class="fa-solid fa-upload"></i></a>
                <a href data-trigger="clear" data-bs-toggle="tooltip" title="{string.trash}"><i class="fa-solid fa-trash-can"></i></a>
            </div>
        </div>
    </div>
    {placeholder_size}
    {placeholder_javascript}
</div>
EOF;

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('size', [256, 256]);
        $this->addOption('viewMode', 1);
        $this->addOption('displaySize', true);
        $this->addOption('antialiasing', 0.8);
        $this->addOption('accepts', '.png,.jpg,.jpeg');
        $this->addOption('fileTypes', ['image/png', 'image/jpeg']);
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->setAttribute('type', 'hidden');

        if(!is_array($this->getOption('size')) || count($this->getOption('size')) !== 2){
            throw new \InvalidArgumentException("Option [size] must be an array with two elements [width, height].");
        }
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        list($w, $h) = $this->getOption('size');

        $opt = [
            'width' => $w,
            'height' => $h,
            'viewMode' => $this->getOption('viewMode'),
            'antialiasing' => $this->getOption('antialiasing'),
            'accepts' => $this->getOption('accepts'),
            'fileTypes' => $this->getOption('fileTypes'),
            'strings' => [
                'btnClose' => trans("gui::messages.component.image.close"),
                'btnCrop' => trans("gui::messages.component.image.crop"),
                'btnReset' => trans("gui::messages.component.image.reset"),
                'loading' => trans("gui::messages.component.image.loading")
            ]
        ];

        return Str::strtr($this->layout, [
            '{w}' => $w,
            '{h}' => $h,
            '{placeholder_field}' => parent::render($name, $value, $error),
            '{placeholder_size}' => $this->getOption('displaySize') ? content_tag('div', "{$w}x$h", ['class' => 'size-display']) : "",
            '{placeholder_javascript}' => javascript_tag_deferred('$("#' . $this->generateId($name) . '").GUIControlImage(' . _javascript_php_to_object($opt) . ')'),
            '{string.upload}' => trans("gui::messages.component.image.upload"),
            '{string.trash}' => trans("gui::messages.component.image.trash")
        ]);
    }
}