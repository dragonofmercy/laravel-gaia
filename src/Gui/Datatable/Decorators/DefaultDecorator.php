<?php
namespace Gui\Datatable\Decorators;

class DefaultDecorator extends AbstractDecorator
{
    /**
     * @inheritDoc
     */
    protected string $layout = <<<EOF
<div class="datatable table-adapt">
    {styles}
    {flash}
    {search}
    <table class="table">
        <thead>{headers}</thead>
        <tbody>{rows}</tbody>
    </table>
    {pagination}
    {javascript}
</div>
EOF;

    /**
     * @inheritDoc
     */
    protected function getComponents(): array
    {
        return [
            '{styles}' => Default\StylesComponent::class,
            '{flash}' => Default\FlashComponent::class,
            '{search}' => Default\SearchComponent::class,
            '{headers}' => Default\HeadersComponent::class,
            '{rows}' => Default\RowsComponent::class,
            '{pagination}' => Default\PaginationComponent::class,
            '{javascript}' => Default\JavascriptComponent::class
        ];
    }
}