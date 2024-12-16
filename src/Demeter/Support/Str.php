<?php
namespace Demeter\Support;

use Illuminate\Support\Collection;

class Str extends \Illuminate\Support\Str
{
    /**
     * Join string with other string with separator
     *
     * @param string $haystack
     * @param array|Collection|string $needles
     * @param string $separator
     * @return string
     */
    public static function join(string $haystack, array|Collection|string $needles = '', string $separator = ' '): string
    {
        if($needles instanceof Collection){
            $needles = $needles->toArray();
        }

        if(!is_array($needles)){
            $needles = [$needles];
        }

        array_unshift($needles, $haystack);

        $needles = array_filter($needles, function(string $value){
            return !empty($value);
        });

        return implode($separator, $needles);
    }

    /**
     * Splits a given string into a collection based on a specified separator.
     *
     * @param string|null $string The input string to be split, or null to return an empty collection.
     * @param string $separator The string delimiter to use for splitting the input string.
     * @param int $limit The maximum number of elements in the resulting collection. Defaults to PHP_INT_MAX.
     * @return Collection A collection of the split string fragments, or an empty collection if the input string is null.
     */
    public static function explode(string|null $string, string $separator, int $limit = PHP_INT_MAX) : Collection
    {
        if(null === $string){
            return new Collection();
        }

        return new Collection(explode($separator, $string, $limit));
    }

    /**
     * Strtr speedy method
     *
     * @param string $haystack
     * @param array|Collection $needles
     * @return string
     */
    public static function strtr(string $haystack, array|Collection $needles = []) : string
    {
        return str_replace(array_keys($needles), array_values($needles), $haystack);
    }

    /**
     * Clean up string
     *
     * @param string $string
     * @param bool $trim
     * @param bool|array|Collection $stripTags Boolean or array of allowed tags
     * @param bool $stripWordChars
     * @return string
     */
    public static function cleanup(string $string, bool $trim = true, bool|array|Collection $stripTags = true, bool $stripWordChars = true) : string
    {
        $string = str_replace("\xE2\x80\x8B\xA0", '', $string);

        if($trim){
            $string = trim($string, " \t\n\r\0\x0B\xC2");
        }

        if($stripWordChars){
            $needles = [
                "\xC2\xAB",         // « (U+00AB)
                "\xC2\xBB",         // » (U+00BB)
                "\xE2\x80\x98",     // ‘ (U+2018)
                "\xE2\x80\x99",     // ’ (U+2019)
                "\xE2\x80\x9A",     // ‚ (U+201A)
                "\xE2\x80\x9B",     // ‛ (U+201B)
                "\xE2\x80\x9C",     // “ (U+201C)
                "\xE2\x80\x9D",     // ” (U+201D)
                "\xE2\x80\x9E",     // „ (U+201E)
                "\xE2\x80\x9F",     // ‟ (U+201F)
                "\xE2\x80\xB9",     // ‹ (U+2039)
                "\xE2\x80\xBA",     // › (U+203A)
                "\xE2\x80\x93",     // – (U+2013)
                "\xE2\x80\x94",     // — (U+2014)
                "\xE2\x80\xA6",     // … (U+2026)
                "\xC2\xA0",         // NO BRAKE SPACE (U+C2A0)
                "\xE2\x80\x8B",     // ZERO WIDTH SPACE (U+200B)
            ];

            $replacements = [
                "<<", ">>", "'", "'", "'", "'", '"', '"', '"',
                '"', "<", ">", "-", "-", "...", " ", ""
            ];

            $string = str_replace($needles, $replacements, $string);
        }

        if(is_array($stripTags) || $stripTags){
            $string = strip_tags($string, is_array($stripTags) ? $stripTags : null);
        }

        return $string;
    }
}