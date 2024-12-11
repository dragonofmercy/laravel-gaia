<?php
namespace Demeter\Pdf;

use Closure;
use Demeter\Support\Str;
use Illuminate\Support\Collection;

class Tcpdf extends \TCPDF
{
    const ORIENTATION_PORTRAIT = 'P';
    const ORIENTATION_LANDSCAPE = 'L';
    const DISPLAY_FULLPAGE = 'fullpage';
    const DISPLAY_FULLWIDTH = 'fullwidth';
    const DISPLAY_REAL = 'real';
    const DISPLAY_DEFAULT = 'default';

    /**
     * Header closure
     *
     * @var Closure|null
     */
    protected ?Closure $headerClosure = null;

    /**
     * Footer closure
     *
     * @var Closure|null
     */
    protected ?Closure $footerClosure = null;

    /**
     * Current page format
     *
     * @var string|array
     */
    protected string|array $currentFormat = "A4";

    /**
     * Constructor.
     *
     * @inheritDoc
     */
    public function __construct($orientation = self::ORIENTATION_PORTRAIT, $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = true)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

        $this->currentFormat = $format;
        $this->setDocumentMargins(0, 0, 0, 0);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->setJPEGQuality(100);
        $this->setImageScale(4);
        $this->setLanguageArray(array(
            'a_meta_charset' => $encoding,
            'a_meta_dir' => $this->getRTL() ? 'rtl' : 'ltr',
            'a_meta_language' => app()->currentLocale(),
            'w_page' => 'page'
        ));
    }

    /**
     * Set document margins
     *
     * @param float $top
     * @param float $left
     * @param float $right
     * @param float $bottom
     * @return void
     */
    public function setDocumentMargins(float $top, float $left, float $right, float $bottom): void
    {
        $this->setMargins($left, $top, $right, true);
        $this->setAutoPageBreak(true, $bottom);
        $this->setFooterMargin($bottom);
    }

    /**
     * New Page
     *
     * @param string $orientation
     * @param string|array $format
     * @return void
     */
    public function newPage(string $orientation = self::ORIENTATION_PORTRAIT, string|array $format = "A4") : void
    {
        $this->currentFormat = $format;
        $this->AddPage($orientation, $format);
    }

    /**
     * Get current page orientation
     *
     * @return string
     */
    public function getCurrentPageOrientation() : string
    {
        return $this->CurOrientation;
    }

    /**
     * Get current page format
     *
     * @return string
     */
    public function getCurrentPageFormat() : string
    {
        return $this->currentFormat;
    }

    /**
     * Get content width
     * @return float
     */
    public function getContentWidth() : float
    {
        return floatval($this->getPageWidth() - $this->lMargin - $this->rMargin);
    }

    /**
     * Get content height
     *
     * @return float
     */
    public function getContentHeight() : float
    {
        return floatval($this->getPageHeight() - $this->tMargin - $this->bMargin);
    }

    /**
     * Get the top of the footer
     *
     * @return float
     */
    public function getFooterTop() : float
    {
        return floatval($this->getPageHeight() - $this->getFooterMargin());
    }

    /**
     * Set text color
     *
     * @param string|array $color
     * @return void
     */
    public function textColor(string|array $color) : void
    {
        if(!is_array($color))
        {
            $color = $this->hexToRgb($color);
        }

        $this->setTextColorArray($color);
    }

    /**
     * Set fill color
     *
     * @param string|array $color
     * @return void
     */
    public function fillColor(string|array $color) : void
    {
        if(!is_array($color))
        {
            $color = $this->hexToRgb($color);
        }

        $this->setFillColorArray($color);
    }

    /**
     * Set text bold
     *
     * @param bool $v
     * @return void
     */
    public function textBold(bool $v) : void
    {
        $this->setFont($this->getFontFamily(), $v ? 'B' : '');
    }

    /**
     * Create a textbox
     *
     * @param array $args
     * @return void
     */
    public function textBox(array $args = []) : void
    {
        $defaults = array(
            'w' => 0,
            'h' => 0,
            'txt' => '',
            'border' => 0,
            'align' => 'L',
            'fill' => false,
            'ln' => 0,
            'x' => '',
            'y' => '',
            'reseth' => true,
            'stretch' => 0,
            'ishtml' => false,
            'autopadding' => true,
            'maxh' => 0,
            'valign' => null,
            'fitcell' => false
        );

        $options = new Collection();

        foreach($defaults as $k => $v)
        {
            $options[$k] = array_key_exists($k, $args) ? $args[$k] : $v;
        }

        if($options->get('maxh') == 0 && null !== $options->get('h')){
            $options['maxh'] = $options->get('h');
        }

        call_user_func_array([$this, 'MultiCell'], $options->toArray());
    }

    /**
     * Create a picture
     *
     * @param string $src
     * @param array $args
     * @return void
     */
    public function picture(string $src, array $args = [])
    {
        $defaults = [
            'file' => $src,
            'x' => '',
            'y' => '',
            'w' => 0,
            'h' => 0,
            'type' => 'JPEG',
            'link' => '',
            'align' => '',
            'resize' => false,
            'dpi' => 300,
            'palign' => '',
            'ismask' => false,
            'imgmask' => false,
            'border' => 0,
            'fitbox' => false,
            'hidden' => false,
            'fitonpage' => false,
            'alt' => false,
            'altimgs' => array()
        ];

        $options = new Collection();

        foreach($defaults as $k => $v)
        {
            $options[$k] = array_key_exists($k, $args) ? $args[$k] : $v;
        }

        if(Str::startsWith($src, 'data:')){
            list(, $content) = explode(';base64,', $src);
            $options['file'] = '@' . base64_decode($content);
        } elseif(mb_detect_encoding($src) === false) {
            $options['file'] = '@' . $src;
        }

        call_user_func_array([$this, 'Image'], $options->toArray());
    }

    /**
     * Set the current abscissa down
     *
     * @param float|null $v
     * @return float|Tcpdf
     */
    public function top(float $v = null) : float|self
    {
        if(null === $v){
            return $this->GetY();
        }

        $this->setY($v, false);
        return $this;
    }

    /**
     * Set the current abscissa left
     *
     * @param float|null $v
     * @return float|Tcpdf
     */
    public function left(float $v = null) : float|self
    {
        if(null === $v){
            return $this->GetX();
        }

        $this->setX($v);
        return $this;
    }

    /**
     * Set X and Y
     *
     * @param float $x
     * @param float $y
     * @return void
     */
    public function pos(float $x, float $y)
    {
        $this->setXY($x, $y);
    }

    /**
     * Moves the current abscissa down
     *
     * @param float $v
     * @return void
     */
    public function moveDown(float $v) : void
    {
        $this->setY($this->GetY() + $v, false);
    }

    /**
     * Moves the current abscissa left
     *
     * @param float $v
     * @return void
     */
    public function moveLeft(float $v) : void
    {
        $this->setX($this->GetX() + $v);
    }

    /**
     * Convert hex color to RGB
     *
     * @param string $color
     * @return array
     */
    public function hexToRgb(string $color) : array
    {
        $hex = str_replace("#", "", $color);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }

        return array($r, $g, $b);
    }

    /**
     * Get fill color
     *
     * @return int[]
     */
    public function getFillColor() : array
    {
        return $this->bgcolor;
    }

    /**
     * Set header closure
     *
     * @param callable $callable
     * @return void
     */
    public function setHeaderCallable(callable $callable) : void
    {
        $this->headerClosure = $callable;
    }

    /**
     * Set footer closure
     *
     * @param callable $callable
     * @return void
     */
    public function setFooterCallable(callable $callable) : void
    {
        $this->footerClosure = $callable;
    }

    /**
     * Get header closure
     *
     * @return callable|null
     */
    public function getHeaderCallable() : callable|null
    {
        return $this->headerClosure;
    }

    /**
     * Get footer closure
     *
     * @return callable|null
     */
    public function getFooterCallable() : callable|null
    {
        return $this->footerClosure;
    }

    /**
     * Render header
     *
     * @inheritDoc
     */
    public function Header() : void
    {
        if($this->print_header && null !== $this->getHeaderCallable()){
            call_user_func($this->getHeaderCallable());
        }
    }

    /**
     * Render footer
     *
     * @inheritDoc
     */
    public function Footer() : void
    {
        if($this->print_footer && null !== $this->getFooterCallable()){
            call_user_func($this->getFooterCallable());
        }
    }

    /**
     * Render Pdf to browser
     *
     * @param string $mode
     * @param string $filename
     * @return never
     */
    public function renderPdf(string $mode = self::DISPLAY_FULLPAGE, string $filename = '') : never
    {
        $filename = $this->buildFilename($filename);

        $this->setDisplayMode($mode);
        $this->Close();

        if(headers_sent()){
            throw new \RuntimeException('The PDF file cannot be sent because some data has already been output to the browser.');
        }

        $rawpdf = $this->getBuffer();

        header('Content-Type: application/pdf');
        header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
        header('Pragma: public');
        header('Expires: Sat, 01 Jan 2000 01:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header(
            'Content-Disposition: inline; filename="' . $filename . '"; filename*=UTF-8\'\''
            . $filename
        );

        if(empty($_SERVER['HTTP_ACCEPT_ENCODING'])){
            header('Content-Length: ' . strlen($rawpdf));
        }

        echo $rawpdf;
        exit(1);
    }

    /**
     * Force the download of the PDF in the browser
     *
     * @param string $filename
     * @return never
     */
    public function downloadPdf(string $filename = "") : never
    {
        $filename = $this->buildFilename($filename);

        $this->Close();

        if(ob_get_contents()){
            throw new \RuntimeException('The PDF file cannot be sent, some data has already been output to the browser.');
        }

        if(headers_sent()){
            throw new \RuntimeException('The PDF file cannot be sent because some data has already been output to the browser.');
        }

        $rawpdf = $this->getBuffer();

        header('Content-Description: File Transfer');
        header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
        header('Pragma: public');
        header('Expires: Sat, 01 Jan 2000 01:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Type: application/pdf');

        if(!str_contains(PHP_SAPI, 'cgi')){
            header('Content-Type: application/force-download', false);
            header('Content-Type: application/octet-stream', false);
            header('Content-Type: application/download', false);
        }

        header(
            'Content-Disposition: attachment; filename="' . $filename . '";'
            . " filename*=UTF-8''" . $filename
        );

        header('Content-Transfer-Encoding: binary');

        if(empty($_SERVER['HTTP_ACCEPT_ENCODING'])){
            header('Content-Length: ' . strlen($rawpdf));
        }

        echo $rawpdf;
        exit(1);
    }

    /**
     * Build filename
     *
     * @param string $filename
     * @return string
     */
    protected function buildFilename(string $filename = "") : string
    {
        if($filename === '')
        {
            $filename = uniqid('PDF_');
        }

        if(!Str::endsWith($filename, '.pdf'))
        {
            $filename.= '.pdf';
        }

        return $filename;
    }
}