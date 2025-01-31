<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;

class TextareaList extends Textarea
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addOption('max', 0);
        $this->addOption('autosize', false);
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        if(is_array($value)){
            $nbLines = count($value);
            $value = implode("\n", $value);
        } else {
            $nbLines = count(preg_split('/\r?\n/', (string) $value));
        }

        $opt = [];
        $opt['max'] = $this->getOption('max');
        $opt['autosize'] = $this->getOption('autosize');

        $js = '$("#' . $this->generateId($name) . '").GUIControlLines(' . _javascript_php_to_object($opt) . ')';
        return content_tag('div', content_tag('div', content_tag('div', $nbLines, ['class' => 'whitespace']), ['class' => 'input-group-text']) . parent::render($name, $value, $error), ['class' => 'input-group gui-control-lines']) . javascript_tag_deferred($js);
    }
}