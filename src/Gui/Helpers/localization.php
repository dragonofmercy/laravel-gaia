<?php
use Carbon\Carbon;

if(!function_exists('format_date')){
    /**
     * Format date
     *
     * @param string|DateTimeInterface|null $time
     * @param string $format
     * @param string|null $locale
     * @return string
     */
    function format_date(string|DateTimeInterface|null $time = null, string $format = "Y-m-d", string|null $locale = null): string
    {
        if(null === $locale){
            $locale = app()->currentLocale();
        }

        return Carbon::parse($time)->locale($locale)->format($format);
    }
}

if(!function_exists('format_date_iso')){
    /**
     * Format date using iso format
     *
     * @see https://carbon.nesbot.com/docs/#api-localization
     *
     * @param string|DateTimeInterface|null $time
     * @param string $format
     * @param string|null $locale
     * @return string
     */
    function format_date_iso(string|DateTimeInterface|null $time = null, string $format = "Y-m-d", string|null $locale = null): string
    {
        if(null === $locale){
            $locale = app()->currentLocale();
        }

        return Carbon::parse($time)->locale($locale)->isoFormat($format);
    }
}