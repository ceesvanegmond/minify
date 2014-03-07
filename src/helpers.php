<?php

if(!function_exists('stylesheet'))
{
    /**
     * stylesheet
     *
     * @param $files
     * @param array $attributes
     * @internal param mixed $args Description.
     *
     * @access public
     * @return mixed Value.
     */
    function stylesheet($files, array $attributes = array())
    {
        $files = (array)$files;
        if(!in_array(App::environment(), Config::get('minify::ignore_envionments')))
        {
            $url = App::make('minify')->styles($files);

            return HTML::style($url, $attributes);
        }

        $path = Config::get('minify.css_path', Config::get('minify::css_path'));

        $return = null;
        foreach ($files as $file)
        {
            $return .= HTML::style($path . $file, $attributes);
        }

        return $return;
    }

}

if(!function_exists('javascript'))
{
    /**
     * javascript
     *
     * @param mixed $files Description.
     *
     * @param array $attributes
     * @access public
     * @return mixed Value.
     */
    function javascript($files, array $attributes = array())
    {
        $files = (array)$files;
        if(!in_array(App::environment(), Config::get('minify::ignore_envionments')))
        {
            $url = App::make('minify')->javascript($files);

            return HTML::script($url, $attributes);
        }

        $path = Config::get('minify.js_path', Config::get('minify::js_path', '/js/'));

        $return = null;
        foreach ($files as $file)
        {
            $return .= HTML::script($path . $file, $attributes);
        }

        return $return;
    }
}
