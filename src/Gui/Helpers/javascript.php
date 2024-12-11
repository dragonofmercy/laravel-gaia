<?php
use Illuminate\Support\Facades\Request as RequestFacade;

if(!function_exists('javascript_tag')){
    /**
     * Create a javascript tag
     *
     * @param  string $content
     * @return string
     */
    function javascript_tag(string $content): string
    {
        return content_tag('script', $content, ['type' => 'text/javascript']);
    }
}

if(!function_exists('javascript_tag_deferred')){
    /**
     * Create a deferred javascript tag
     *
     * @param  string $content
     * @return string
     */
    function javascript_tag_deferred(string $content): string
    {
        return RequestFacade::isXmlHttpRequest() ? javascript_tag($content): javascript_tag('window.addEventListener("DOMContentLoaded",function(){' . $content . '});');
    }
}