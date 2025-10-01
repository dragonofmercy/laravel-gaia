<?php
if(!function_exists('tabler_icon')){
    /**
     * Renders a Tabler icon component with the specified name and attributes.
     *
     * @param string $name The name of the Tabler icon to render.
     * @param string $class
     * @return string The rendered Tabler icon component as a string.
     */
    function tabler_icon(string $name, string $class = ""){
        return \Illuminate\Support\Facades\Blade::render('<x-gui::tabler-icon :name="$name" :class="$class" />', ['name' => $name, 'class' => $class]);
    }
}