<?php
use Demeter\Support\Str;
use Illuminate\Support\Collection;

if(!function_exists('remote_function')){
    /**
     * Get remove function call in javascript ($.ajax)
     *
     * @param array|Collection $options
     * @return string
     */
    function remote_function(array|Collection $options): string
    {
        if(!$options instanceof Collection){
            $options = collect($options);
        }

        if(!$options->has('url')){
            throw new \InvalidArgumentException("required option [url] is missing");
        }

        if(!$options->has('update')){
            throw new \InvalidArgumentException("required option [update] is missing");
        }

        $updateMethod = $options->get('updateMethod', 'html');
        $target = str_starts_with($options->get('update'), "#") || str_starts_with($options->get('update'), ".") ? $options->get('update') : '#' . $options->get('update');
        $callbackSuccess = $options->get('success', "$('$target').$updateMethod(data)");

        if(app()->hasDebugModeEnabled()){
            $callbackError = $options->get('error', "gui.ignitionErrorFrame(xhr.responseText, $('$target')); gui.init($('$target'))");
        } else {
            $callbackError = $options->get('error', "$('$target').html(xhr.responseText);");
        }

        $xhr = [];
        $xhr['url'] = url($options->get('url'));
        $xhr['type'] = Str::upper($options->get('method', 'get'));
        $xhr['dataType'] = $options->get('dataType', 'text');
        $xhr['headers'] = '{"X-CSRF-TOKEN":$(\'meta[name="csrf-token"]\').attr(\'content\')}';

        if($options->has('form')){
            $xhr['data'] = "|$(this).serialize()";
        } elseif($options->has('data')) {
            $xhr['data'] = json_encode($options->get('data'));
        }

        $xhr['cache'] = $options->get('cache', false);
        $xhr['success'] = "function(data,status){ $callbackSuccess }";
        $xhr['error'] = "function(xhr,status,e){" . $callbackError . "}";

        if($options->has('loading')){
            $xhr['beforeSend'] = "function(xhr){" . $options->get('loading') . "}";
        }

        if($options->has('complete')){
            $xhr['complete'] = "function(xhr,status){" . $options->get('complete') . "}";
        }

        return '$.ajax(' . _javascript_php_to_object($xhr) . ');';
    }
}