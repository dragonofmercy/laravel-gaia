<?php
namespace Gui\Forms\Validators;

use Illuminate\Support\Collection;
use ValueError;

class Error extends ValueError
{
    /**
     * AbstractValidator instance
     *
     * @var AbstractValidator
     */
    protected AbstractValidator $validator;

    /**
     * Arguments
     * @var Collection<string, mixed>
     */
    protected Collection $arguments;

    /**
     * Constructor.
     *
     * @param AbstractValidator $validator
     * @param string $code
     * @param Collection<string, mixed>|array<string, mixed> $arguments
     */
    public function __construct(AbstractValidator $validator, string $code, Collection|array $arguments = [])
    {
        parent::__construct();

        $this->code = $code;
        $this->validator = $validator;
        $this->arguments = new Collection($arguments);

        $replacements = $this->getReplacements();

        $this->message = str_replace(array_keys($replacements), array_values($replacements), trans($this->validator->getMessage($code)));
    }

    /**
     * Get validator instance
     *
     * @return AbstractValidator
     */
    public function getValidator() : AbstractValidator
    {
        return $this->validator;
    }

    /**
     * Get arguments collection
     *
     * @return Collection
     */
    public function getArguments() : Collection
    {
        return $this->arguments;
    }

    /**
     * Get replacements
     *
     * @return array<string, string>
     */
    protected function getReplacements() : array
    {
        $replacements = [];

        foreach($this->arguments as $k => $v){
            $replacements[":" . $k] = $v;
        }

        return $replacements;
    }
}