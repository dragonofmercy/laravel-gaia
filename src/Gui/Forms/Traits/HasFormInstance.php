<?php
namespace Gui\Forms\Traits;

use Gui\Forms\Form as BaseForm;
use Gui\Forms\FormEloquent as EloquentForm;

trait HasFormInstance
{
    /**
     * Form instance
     *
     * @var BaseForm
     */
    protected BaseForm $form;

    /**
     * Set form instance
     *
     * @param BaseForm $form
     * @return void
     */
    public function setFormInstance(BaseForm $form): void
    {
        $this->form = $form;
    }

    /**
     * Get form instance
     *
     * @return BaseForm|EloquentForm
     */
    public function getFormInstance(): BaseForm|EloquentForm
    {
        return $this->form;
    }
}