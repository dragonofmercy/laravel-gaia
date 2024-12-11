<?php
namespace Gui\Datatable\Decorators\Default;

use Illuminate\Http\Request;

class PaginationComponent extends AbstractComponent
{
    /** CSS variables */
    protected string $classPaginationFirst = 'first';
    protected string $classPaginationPrevious = 'previous';
    protected string $classPaginationNext = 'next';
    protected string $classPaginationLast = 'last';
    protected string $classPaginationActive = 'active';
    protected string $classPaginationItem = 'page-item';
    protected string $classPaginationLink = 'page-link';

    /** Translation keys */
    protected string $stringPosition = 'gui::messages.datatable.page_of_page';
    protected string $stringRecords = 'gui::messages.datatable.records';

    /**
     * Pagination layout
     * @var string
     */
    protected string $layout = <<<EOF
<div class="datatable-bottom">
    <div class="datatable-stats">
        <div class="reccords">{count}</div>
        <div class="page">{position}</div>
    </div>
    <div class="datatable-paginator">
        <nav>
            <ul class="pagination">
                {pagination_first}
                {pagination_previous}
                {pagination_pages}
                {pagination_next}
                {pagination_last}
            </ul>
        </nav>
    </div>
    <div class="datatable-options"></div>
</div>
EOF;

    /**
     * Render paginate
     *
     * @return string
     */
    public function render(): string
    {
        $replacements = [
            '{count}' => $this->renderCount(),
            '{position}' => $this->renderPosition(),
            '{pagination_first}' => $this->renderPaginationFirst(),
            '{pagination_previous}' => $this->renderPaginationPrevious(),
            '{pagination_pages}' => $this->renderPaginationPages(),
            '{pagination_next}' => $this->renderPaginationNext(),
            '{pagination_last}' => $this->renderPaginationLast(),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->layout);
    }

    /**
     * Render element count text
     *
     * @return string
     */
    protected function renderCount() : string
    {
        $count = $this->getParent()->getEngine()->getPagination()->total();
        return sprintf('%d %s', $count, trans_choice($this->stringRecords, $count));
    }

    /**
     * Render position text
     *
     * @return string
     */
    protected function renderPosition() : string
    {
        $datatable = $this->getParent()->getEngine();
        return sprintf(trans($this->stringPosition), $datatable->getPagination()->currentPage(), $datatable->getPagination()->lastPage());
    }

    /**
     * Render paginate first link
     *
     * @return string
     */
    protected function renderPaginationFirst() : string
    {
        $pagination = $this->getParent()->getEngine()->getPagination();
        if($pagination->hasPages() && $pagination->currentPage() > 1 && $pagination->lastPage() > 5){
            return $this->renderPaginationLink("", $this->getParent()->url(1), ['class' => $this->classPaginationFirst]);
        }
        return "";
    }

    /**
     * Render paginate previous link
     *
     * @return string
     */
    protected function renderPaginationPrevious() : string
    {
        $pagination = $this->getParent()->getEngine()->getPagination();
        if($pagination->hasPages() && $pagination->currentPage() > 1){
            return $this->renderPaginationLink("", $this->getParent()->url($pagination->currentPage() - 1), ['class' => $this->classPaginationPrevious]);
        }
        return "";
    }

    /**
     * Render paginate page link
     *
     * @return string
     */
    protected function renderPaginationPages() : string
    {
        $output = "";
        $pagination = $this->getParent()->getEngine()->getPagination();
        if($pagination->hasPages()){
            foreach(gui_paginator_pages_range($pagination, 5) as $page){
                $output.= $this->renderPaginationLink($page, $this->getParent()->url($page), $page === $pagination->currentPage() ? ['class' => 'active'] : []);
            }
        }
        return $output;
    }

    /**
     * Render paginate next link
     *
     * @return string
     */
    protected function renderPaginationNext() : string
    {
        $pagination = $this->getParent()->getEngine()->getPagination();
        if($pagination->hasPages() && $pagination->currentPage() < $pagination->lastPage()){
            return $this->renderPaginationLink("", $this->getParent()->url($pagination->currentPage() + 1), ['class' => $this->classPaginationNext]);
        }
        return "";
    }

    /**
     * Render paginate last link
     *
     * @return string
     */
    protected function renderPaginationLast() : string
    {
        $pagination = $this->getParent()->getEngine()->getPagination();
        if($pagination->hasPages() && $pagination->currentPage() < $pagination->lastPage() && $pagination->lastPage() > 5){
            return $this->renderPaginationLink("", $this->getParent()->url($pagination->lastPage()), ['class' => $this->classPaginationLast]);
        }
        return "";
    }

    /**
     * Render pagination link
     *
     * @param string $label
     * @param string $url
     * @param array $attributes
     * @return string
     */
    protected function renderPaginationLink(string $label, string $url, array $attributes = []) : string
    {
        $engine = $this->getParent()->getEngine();
        if(isset($attributes['class'])){
            $attributes['class'].= " " . $this->classPaginationItem;
        } else {
            $attributes['class'] = $this->classPaginationItem;
        }
        return content_tag('li', lr($label, $url, $engine->getUid(), ['class' => $this->classPaginationLink], ['method' => Request::METHOD_POST]), $attributes);
    }
}