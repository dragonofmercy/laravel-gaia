<?php
namespace Gui\Forms\Elements;

use Demeter\Support\Str;
use Gui\Forms\Validators\Error;
use Illuminate\Support\Collection;

class MultiFields extends AbstractElement
{
    /**
     * Default overridable template
     * @var string
     */
    public static string $template = <<<EOF
<div class="mf-content">
    <table class="mf-table">
        <thead>{heading}</thead>
        <tbody>{rows}</tbody>
    </table>
</div>
{invalid_feedback}
<a class="btn btn-default btn-icon inline" data-trigger="add">
    <i class="far fa-circle-plus"></i>
    {label}
</a>
EOF;

    /**
     * @inheritDoc
     */
    protected function initialize() : void
    {
        parent::initialize();

        $this->addRequiredOption('columns');
        $this->addRequiredOption('fields');

        $this->addOption('max');
        $this->addOption('sortable', true);
        $this->addOption('newLineOnTab', true);
        $this->addOption('columnsAttributes', []);
        $this->addOption('newLinelabel', 'gui::messages.component.multifields.add');
        $this->addOption('template', static::$template);
        $this->addOption('trashIcon', 'fa-solid fa-trash-can');
        $this->addOption('moveIcon', 'fa-solid fa-grip');
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender() : void
    {
        parent::beforeRender();

        $this->appendAttribute('class', 'gui-control-multi');

        if(!is_array($this->getOption('columns')) && !($this->getOption('columns') instanceof Collection)){
            throw new \InvalidArgumentException("Option [columns] must be type of array or Collection");
        }

        if(!is_array($this->getOption('fields')) && !($this->getOption('fields') instanceof Collection)){
            throw new \InvalidArgumentException("Option [fields] must be type of array or Collection");
        }

        if(!is_array($this->getOption('columnsAttributes')) && !($this->getOption('columnsAttributes') instanceof Collection)){
            throw new \InvalidArgumentException("Option [columnsAttributes] must be type of array or Collection");
        }

        $columns = $this->getOption('columns');
        $columns['gui-control'] = 'gui::messages.generic.empty';

        $columnsAttributes = $this->getOption('columnsAttributes');
        $columnsAttributes['gui-control'] = ['class' => 'control'];

        $this->setOption('columns', new Collection($columns));
        $this->setOption('fields', new Collection($this->getOption('fields')));
        $this->setOption('columnsAttributes', new Collection($columnsAttributes));
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null) : string
    {
        $this->setAttribute('id', $this->generateId($name));
        $this->setAttribute('name', $name);

        $error?->getValidator()->setFlag('hidden', true);

        $opt = [
            'max' => $this->getOption('max'),
            'sortable' => $this->getOption('sortable'),
            'newLineOnTab' => $this->getOption('newLineOnTab')
        ];

        return $this->renderContentTag('div', Str::strtr($this->getOption('template'), [
            '{heading}' => $this->getHeading(),
            '{rows}' => $this->getRows($this->formatValues($value), $error),
            '{invalid_feedback}' => $this->getInvalidFeedback($error),
            '{label}' => trans($this->getOption('newLinelabel'))
        ]), $this->attributes) . javascript_tag_deferred("$('#" . $this->getAttribute('id') . "').GUIControlMulti(" . _javascript_php_to_object($opt) . ")");
    }

    /**
     * Get heading
     *
     * @return string
     */
    protected function getHeading() : string
    {
        /** @var Collection $columnsAttributes */
        $columnsAttributes = $this->getOption('columnsAttributes');
        $output = "";

        $this->getOption('columns')->map(function(string|null $label, string $name) use ($columnsAttributes, &$output){
            $output.= content_tag('th', trans($label), $columnsAttributes->get($name, []));
        });

        return $output;
    }

    /**
     * Get rows
     *
     * @param Collection $rows
     * @param Error|null $error
     * @return string
     */
    protected function getRows(Collection $rows, ?Error $error) : string
    {
        if($rows->count() < 1){
            $rows->add($this->getOption('fields')->keys()->flip()->transform(function(){
                return null;
            }));
        }

        $output = "";
        $index = 0;
        $invalid = [];

        if(null !== $error){
            if($error->getCode() == 'invalid'){
                $invalid = json_decode($error->getArguments()->get('errors'), true);
            }
        }

        $rows->map(function(mixed $values) use (&$output, &$index, $invalid){
            $this->getOption('columns')->map(function(string|null $label, string $name) use ($values, &$row, $invalid, $index){
                if($name !== 'gui-control'){
                    $element = $this->getOption('fields')->get($name);

                    if(null !== $element){
                        $cell = "";

                        if(!$element instanceof AbstractElement){
                            $element = new $element();
                        }

                        $element->setFormInstance($this->getFormInstance());
                        $element->setFieldName($this->getFieldName());

                        if(isset($invalid[$index][$name])){
                            $cell = Str::replace('{error}', $invalid[$index][$name], $this->getFormInstance()->getDecorator()->getErrorFormat());
                            $element->appendAttribute('class', 'is-invalid');
                        }

                        $cell = $element->render($this->getAttribute('name') . '[' . $name . '][]', $values[$name] ?? null) . $cell;
                    } else {
                        $cell = trans('gui::messages.generic.empty');
                    }
                } else {
                    $cell = content_tag('a', gui_icon($this->getOption('trashIcon')), ['data-trigger' => 'remove', 'class' => 'btn btn-link']);

                    if($this->getOption('sortable')){
                        $cell.= content_tag('a', gui_icon($this->getOption('moveIcon')), ['data-trigger' => 'move', 'class' => 'btn btn-link']);
                    }
                }

                $row.= content_tag('td', $cell, $this->getOption('columnsAttributes')->get($name, []));
            });

            $output.= content_tag('tr', $row);
            $index++;
        });

        return $output;
    }

    /**
     * Get invalid feedback
     *
     * @param Error|null $error
     * @return string
     */
    protected function getInvalidFeedback(?Error $error = null) : string
    {
        if(null !== $error && $error->getCode() != 'invalid'){
            return Str::replace('{error}', $error->getMessage(), $this->getFormInstance()->getDecorator()->getErrorFormat());
        }

        return "";
    }

    /**
     * Format raw values to collection of lines
     *
     * @param mixed $v
     * @return Collection<int, mixed>
     */
    protected function formatValues(mixed $v) : Collection
    {
        if(is_array($v))
        {
            return collect($v);
        }

        return new Collection();
    }
}