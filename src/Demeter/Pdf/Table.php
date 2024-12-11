<?php
namespace Demeter\Pdf;

use Demeter\Support\Iterator;
use Demeter\Support\Str;
use Illuminate\Support\Collection;

class Table
{
    const ALIGN_LEFT = 'L';
    const ALIGN_CENTER = 'C';
    const ALIGN_RIGHT = 'R';

    const VALIGN_TOP = 'T';
    const VALIGN_MIDDLE = 'M';
    const VALIGN_BOTTOM = 'B';

    /**
     * TcPDF handle
     * @var Tcpdf
     */
    protected Tcpdf $handle;

    /**
     * Table width
     * @var float
     */
    protected float $tableWidth;

    /**
     * Indicate if headers must be draw after break page
     * @var bool
     */
    protected bool $repeatHeadingAfterBreak = true;

    /**
     * Indicate if table is stripped with $stripColor
     * @var bool
     */
    protected bool $stripped = true;

    /**
     * Line width
     * @var float
     */
    protected float $lineWidth = 0.1;

    /**
     * Fill color
     * @var int[]
     */
    protected array $fillColor = [204, 255, 255];

    /**
     * Fill color
     * @var int[]
     */
    protected array $stripColor = [250, 250, 250];

    /**
     * Border color
     * @var int[]
     */
    protected array $drawColor = [230, 230, 230];

    /**
     * Cells paddings
     * @var float[]
     */
    protected array $cellPadding = [2, 2, 2, 2];

    /**
     * Columns default definitions
     * @var array<string, mixed>
     */
    protected array $columnsDefaultDefinitions = [
        'w' => 0,
        'h' => 0,
        'maxh' => 0,
        'align' => self::ALIGN_LEFT,
        'valign' => self::VALIGN_MIDDLE,
        'reseth' => true,
        'autopadding' => true,
        'border' => 1,
        'ishtml' => false,
        'fill' => false,
        'txt' => '',
        'font-size' => null,
        'font-style' => '',
        'font-family' => null
    ];

    /**
     * Table row values
     * @var array<string, string>
     */
    protected array $rows = [];

    /**
     * Columns definitions
     * @var array<string, array<string, mixed>>
     */
    protected array $columns = [];

    /**
     * Heading definitions
     * @var array<string, array<string, mixed>>
     */
    protected array $heading = [];

    /**
     * Constructor.
     *
     * @param Tcpdf $handle
     * @param float|null $tableWidth
     */
    public function __construct(Tcpdf $handle, ?float $tableWidth = null)
    {
        $this->handle = $handle;
        $this->tableWidth = null === $tableWidth ? $this->handle->getContentWidth() : $tableWidth;
    }

    /**
     * Set heading definitions
     *
     * @param array|Collection $columns
     * @return void
     */
    public function setHeading(array|Collection $columns) : void
    {
        foreach($columns as $column => $definition){
            $def = [];
            foreach($this->columnsDefaultDefinitions as $k => $v){
                $def[$k] = array_key_exists($k, $definition) ? $definition[$k] : $v;
            }

            $this->heading[$column] = $def;
        }
    }

    /**
     * Set columns definitions
     *
     * @param array|Collection $columns
     * @return void
     */
    public function setColumns(array|Collection $columns) : void
    {
        foreach($columns as $column => $definition){
            $def = [];
            foreach($this->columnsDefaultDefinitions as $k => $v){
                $def[$k] = array_key_exists($k, $definition) ? $definition[$k] : $v;
            }

            $this->columns[$column] = $def;
        }
    }

    /**
     * Add row
     *
     * @param array|Collection $row
     * @return void
     */
    public function addRow(array|Collection $row) : void
    {
        $line = [];

        foreach(array_keys($this->columns) as $column){
            $line[$column] = array_key_exists($column, $row) ? (string) $row[$column] : "";
        }

        $this->rows[] = $line;
    }

    /**
     * Set line width
     *
     * @param float $v
     * @return void
     */
    public function setLineWidth(float $v) : void
    {
        $this->lineWidth = $v;
    }

    /**
     * Set fill color, can be hex or RGB
     *
     * @param string|array $color
     * @return void
     */
    public function setFillColor(string|array $color) : void
    {
        if(!is_array($color)){
            $color = $this->handle->hexToRgb($color);
        }

        $this->fillColor = $color;
    }

    /**
     * Set strip background color, can be hex or RGB
     *
     * @param string|array $color
     * @return void
     */
    public function setStripColor(string|array $color) : void
    {
        if(!is_array($color)){
            $color = $this->handle->hexToRgb($color);
        }

        $this->stripColor = $color;
    }

    /**
     * Set draw color, can be hex or RGB
     *
     * @param string|array $color
     * @return void
     */
    public function setDrawColor(string|array $color) : void
    {
        if(!is_array($color)){
            $color = $this->handle->hexToRgb($color);
        }

        $this->drawColor = $color;
    }

    /**
     * Set cell padding
     *
     * @param float $left
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @return void
     */
    public function setCellPadding(float $left, float $top, float $right, float $bottom) : void
    {
        $this->cellPadding = [$left, $top, $right, $bottom];
    }

    /**
     * Set if header must be draw after break page
     *
     * @param bool $v
     * @return void
     */
    public function setRepeatHeadingAfterBreak(bool $v) : void
    {
        $this->repeatHeadingAfterBreak = $v;
    }

    /**
     * Set if table has strip color
     *
     * @param bool $v
     * @return void
     */
    public function setStripped(bool $v) : void
    {
        $this->stripped = $v;
    }

    /**
     * Draw the table to PDF
     *
     * @return void
     */
    public function make() : void
    {
        $this->updateWidths();

        call_user_func_array([$this->handle, 'setCellPaddings'], $this->cellPadding);
        call_user_func_array([$this->handle, 'setFillColor'], $this->fillColor);
        call_user_func_array([$this->handle, 'setDrawColor'], $this->drawColor);
        call_user_func_array([$this->handle, 'setLineWidth'], [$this->lineWidth]);

        if($this->hasHeading()){
            $this->makeHeadings();
        }

        $it = new Iterator($this->rows);
        $it->rewind();

        $stripState = false;

        while($it->valid()){
            $columns = $it->current();
            $rowHeight = $this->calculateRowHeight($columns);

            if($it->hasNext()){
                $y = $this->handle->top();
                $bottomMargin = $this->handle->getFooterMargin();

                $it->next();
                $height = $this->calculateRowHeight($it->current());
                $it->prev();

                if($y + $height + $bottomMargin > $this->handle->getPageHeight()){
                    $this->handle->newPage($this->handle->getCurrentPageOrientation(), $this->handle->getCurrentPageFormat());

                    if($this->repeatHeadingAfterBreak && $this->hasHeading()){
                        $this->makeHeadings();
                    }

                    $stripState = false;
                }
            }

            foreach($columns as $column => $txt){
                $definition = $this->columns[$column];
                $definition['h'] = $rowHeight;
                $definition['txt'] = $txt;

                $this->makeColumn($definition, $stripState);
            }

            $this->handle->Ln();
            $stripState = !$stripState;
            $it->next();
        }
    }

    /**
     * Check if has headings
     *
     * @return bool
     */
    protected function hasHeading() : bool
    {
        return count($this->heading) > 0;
    }

    /**
     * Calculate row height
     *
     * @param array $columns
     * @param bool $heading
     * @return float
     */
    protected function calculateRowHeight(array $columns, bool $heading = false) : float
    {
        $height = 0;

        $definitions = $heading ? $this->heading : $this->columns;

        foreach($definitions as $column => $definition){
            if($definition['h'] > $height){
                $height = $definition['h'];
            }

            $originalFontSize = $this->handle->getFontSizePt();

            if(null !== $definition['font-size']){
                $this->handle->setFontSize($definition['font-size']);
            }

            $txt = strip_tags(str_replace(['<br>', '<br/>', '<br />'], PHP_EOL, $columns[$column] ?? PHP_EOL));

            if(Str::length($txt) === 0){
                $txt = PHP_EOL;
            }

            $stringHeight = $this->handle->getStringHeight($definition['w'], $txt, $definition['reseth'], $definition['autopadding'], '', $definition['border']);
            $this->handle->setFontSize($originalFontSize);

            if($stringHeight > $height){
                $height = $stringHeight;
            }
        }

        return $height;
    }

    /**
     * Update columns widths
     *
     * @retrun void
     */
    protected function updateWidths() : void
    {
        $columnsDefinitions = [];
        $autoColumns = [];
        $currentSize = 0;

        foreach($this->columns as $column => $definition)
        {
            if(isset($definition['w']) && !is_string($definition['w']) && $definition['w'] > 0)
            {
                $currentSize+= $definition['w'];
                $columnsDefinitions[$column] = $definition;
            }
            else
            {
                $autoColumns[$column] = $definition;
            }
        }

        $remainingWidth = $this->tableWidth - $currentSize;

        if($remainingWidth < 0)
        {
            throw new \InvalidArgumentException("Sum of column widths is greater than table width (cols: " . $currentSize . ", table: " . $this->tableWidth . ")");
        }

        if(count($autoColumns))
        {
            $rep = $remainingWidth / count($autoColumns);

            foreach($autoColumns as $column => $definition)
            {
                $definition['w'] = $rep;
                $columnsDefinitions[$column] = $definition;
            }

            ksort($columnsDefinitions);
            $this->columns = $columnsDefinitions;
        }

        if($this->hasHeading())
        {
            foreach($this->columns as $k => $v)
            {
                $this->heading[$k]['w'] = $v['w'];
            }
        }
    }

    /**
     * Make column
     *
     * @param array $definition
     * @param bool $stripState
     * @return void
     */
    protected function makeColumn(array $definition, bool $stripState) : void
    {
        $originalFillColor = $this->handle->getFillColor();
        $originalFontSize = $this->handle->getFontSizePt();
        $originalFontStyle = $this->handle->getFontStyle();
        $originalFontFamily = $this->handle->getFontFamily();

        if(null !== $definition['font-family']){
            $this->handle->setFont($definition['font-family']);
        }

        if(null !== $definition['font-size']){
            $this->handle->setFontSize($definition['font-size']);
        }

        if(null !== $definition['font-style']){
            $this->handle->setFont($this->handle->getFontFamily(), $definition['font-style']);
        }

        if($this->stripped && $stripState && (bool) $definition['fill'] === false){
            $this->handle->fillColor($this->stripColor);
            $definition['fill'] = true;
        }

        $this->handle->textBox($definition);

        $this->handle->setFontSize($originalFontSize);
        $this->handle->setFont($originalFontFamily, $originalFontStyle);
        $this->handle->fillColor($originalFillColor);
    }

    /**
     * Make heading line
     *
     * @return void
     */
    protected function makeHeadings() : void
    {
        $headingLine = [];

        foreach($this->heading as $column => $definition){
            $headingLine[$column] = array_key_exists('txt', $definition) ? (string) $definition['txt'] : PHP_EOL;
        }

        $this->calculateRowHeight($headingLine, true);

        foreach($this->heading as $definition){
            $definition['fill'] = true;
            $this->makeColumn($definition, false);
        }

        $this->handle->Ln();
    }
}