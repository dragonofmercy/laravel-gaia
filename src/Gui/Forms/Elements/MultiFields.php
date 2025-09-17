<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

class MultiFields extends AbstractElement
{
    public static string $stringAddLine = "gui::messages.component.multifields.add";

    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('columns');
        $this->addRequiredOption('fields');

        $this->addOption('max');
        $this->addOption('sortable', true);
        $this->addOption('newLineOnTab', true);
        $this->addOption('columnsAttributes', []);
    }

    /**
     * @inheritDoc
     */
    protected function getView(): string
    {
        return 'gui::forms.elements.multi-fields';
    }

    /**
     * @inheritDoc
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->appendAttribute('class', 'gui-control-mf');

        $this->setViewVar('stringAddLine', trans(static::$stringAddLine));
        $this->setViewVar('columns', $this->getColumns());
        $this->setViewVar('columnsAttributes', $this->getColumnsAttributes());
        $this->setViewVar('sortable', $this->getOption('sortable'));

        $this->setViewVar('componentConfig', json_encode([
            'max' => $this->getOption('max'),
            'sortable' => $this->getOption('sortable'),
            'newLineOnTab' => $this->getOption('newLineOnTab')
        ]));
    }

    /**
     * @inheritDoc
     */
    protected function render(string $name, mixed $value = null, ?Error $error = null): string|View
    {
        $this->setViewVar('rows', $this->getRows($name, $value, $error));
        $error?->getValidator()->setFlag('hidden', true);

        return parent::render($name, $value, $error);
    }

    /**
     * Retrieves the collection of columns, modifying it to include a default GUI control column.
     *
     * @return Collection The collection of columns with added GUI control.
     */
    protected function getColumns(): Collection
    {
        $columns = $this->getOption('columns');
        $columns['gui-control'] = new HtmlString(trans('gui::messages.generic.empty'));

        return collect($columns);
    }

    /**
     * Retrieves the collection of fields based on the 'fields' option.
     *
     * @return Collection The collection of fields configured through options.
     */
    protected function getFields(): Collection
    {
        return collect($this->getOption('fields'));
    }

    /**
     * Retrieves the attributes for the columns, processes them, and returns them as a collection of ComponentAttributeBag objects.
     *
     * @return Collection A collection where each item is a ComponentAttributeBag object representing the attributes of a column.
     */
    protected function getColumnsAttributes(): Collection
    {
        $columnsAttributes = $this->getOption('columnsAttributes');
        $columnsAttributes['gui-control'] = ['class' => 'control'];

        return collect($columnsAttributes)->map(function($attributes){
            return new ComponentAttributeBag($attributes);
        });
    }

    /**
     * Processes rows of data and transforms them into a collection of rendered elements.
     *
     * @param string $name The name or key of the form or element to associate the rows with.
     * @param mixed $value The data to be processed, expected to be iterable or null.
     * @return Collection A collection containing processed and rendered rows.
     */
    protected function getRows(string $name, mixed $value, ?Error $error = null): Collection
    {
        $fields = $this->getFields();
        $rows = is_iterable($value) ? collect($value) : new Collection();
        $output = new Collection();
        $errors = new Collection();
        $invalid = [];
        $index = 0;

        if(null !== $error){
            if($error->getCode() == 'invalid'){
                $invalid = json_decode($error->getArguments()->get('errors'), true);
            } else {
                $this->setViewVar('invalidFeedback', $error->getMessage());
            }
        }

        if($rows->count() < 1){
            $rows->add($this->getFields()->keys()->flip()->transform(fn() => null));
        }

        $rows->map(function(mixed $values) use ($fields, $output, $errors, $invalid, $name, &$index){
            $line = new Collection();
            $lineErrors = new Collection();

            $this->getColumns()->map(function(string|null $columnLabel, string $columnName) use ($values, $fields, $line, $name, $lineErrors, $invalid, $index){
                if($columnName === 'gui-control'){
                    $line->put($columnName, null);
                    return;
                }

                $element = $fields->get($columnName);

                if(null === $element){
                    $line->put($columnName, trans('gui::messages.generic.empty'));
                    return;
                }

                if(!$element instanceof AbstractElement){
                    $element = new $element();
                }

                $element->setFormInstance($this->getFormInstance());
                $element->setFieldName($this->getFieldName());

                if(isset($invalid[$index][$columnName])){
                    $element->appendAttribute('class', 'is-invalid');
                    $lineErrors->put($columnName, $invalid[$index][$columnName]);
                }

                $line->put($columnName, $element->toHtml($name . '[' . $columnName . '][]', $values[$columnName] ?? null, autoId: false));
            });
            $output->add($line);
            $errors->add($lineErrors);
            $index++;
        });

        $this->setViewVar('errors', $errors);

        return $output;
    }
}