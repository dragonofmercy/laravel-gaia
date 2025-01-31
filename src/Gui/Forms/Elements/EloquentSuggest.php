<?php
namespace Gui\Forms\Elements;

use Gui\Forms\Validators\Error;
use Illuminate\Database\Eloquent\Builder;

class EloquentSuggest extends InputAutocomplete
{
    /**
     * @inheritDoc
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->addRequiredOption('model');
        $this->addOption('method', '__toString');
        $this->addOption('externalField');
        $this->addOption('column');
    }

    /**
     * @inheritDoc
     */
    public function render(string $name, mixed $value = null, ?Error $error = null): string
    {
        $displayField = new InputText(attributes: $this->getAttributes());

        if($this->hasOption('externalField')){
            $hiddenFieldId = $this->generateId(preg_replace('/\[([a-zA-Z0-9_-]*)]$/', '[' . $this->getOption('externalField') . ']', $name));
            $displayFieldId = $this->generateId($name);

            $output = $displayField->render($name, $value);
        } else {
            $hiddenFieldId = $this->generateId($name);
            $displayFieldId = $this->generateId($name) . '_display';

            $hiddenField = new InputText(attributes: ['type' => 'hidden']);

            $output = $hiddenField->render($name, $value) . $displayField->render(preg_replace('/]$/', '_display]', $name), $this->getValue($value));
        }

        return $output . javascript_tag_deferred($this->getJavascript($displayFieldId, $hiddenFieldId));
    }

    /**
     * @inheritDoc
     */
    protected function getJavascript(string $id, string $hiddenId = ""): string
    {
        return parent::getJavascript($id) . '$("#' . $id . '").on("gui.validate",function(_,v,t){$("#' . $hiddenId . '").val(v).trigger("change")}).on("keyup",function(){if($(this).val().length<1){$("#' . $hiddenId . '").val("").trigger("change")}})';
    }

    /**
     * Get value for display field
     *
     * @param mixed $v
     * @return mixed
     */
    protected function getValue(mixed $v): mixed
    {
        if(!empty($v)){
            /** @var Builder $query */
            $query = $this->getOption('model')::query();
            $model = $query->where($this->getColumn(), '=', $v)->first();
            if($model){
                $method = $this->getOption('method');
                if(method_exists($model, $method)){
                    return $model->{$method}();
                } else {
                    throw new \InvalidArgumentException("The method [$method] doesn't exists for [" . get_class($model) . "]");
                }
            }
        }

        return $v;
    }

    /**
     * Get column name
     *
     * @return string
     */
    protected function getColumn(): string
    {
        if($this->hasOption('column')){
            return $this->getOption('column');
        } else {
            $modelClass = $this->getOption('model');
            return (new $modelClass)->getKeyName();
        }
    }
}