<?php

if ( ! function_exists('stylesheet'))
{
    /**
     * stylesheet
     *
     * @param mixed $args Description.
     *
     * @param array $attributes
     * @access public
     * @return mixed Value.
     */
    function stylesheet($args, array $attributes = array())
    {
        $args = cast_to_array($args);
        if (!if_array(App::environment(), Config::get('minify::ignore_min'))) {
            $url = App::make('minify')->minifyCss($args);

            return \HTML::style($url, $attributes);
        }

        $path = Config::get('minify.css_path', Config::get('minify::css_path', '/css/'));

        $return = '';
        foreach ($args as $arg)
        {
            $return .= \HTML::style($path . $arg, $attributes);
        }

        return $return;
    }

}

if ( ! function_exists('javascript'))
{
    /**
     * javascript
     *
     * @param mixed $args Description.
     *
     * @param array $attributes
     * @access public
     * @return mixed Value.
     */
    function javascript($args, array $attributes = array())
    {
        $args = cast_to_array($args);
        if (!if_array(App::environment(), Config::get('minify::ignore_min'))) {
            $url = App::make('minify')->minifyJs($args);
            return \HTML::script($url, $attributes);
        }
        
        $path = Config::get('minify.js_path', Config::get('minify::js_path', '/js/'));
        
        $return = '';
        foreach ($args as $arg)
        {
            $return .= \HTML::script($path . $arg, $attributes);
        }

        return $return;
    }
}

if ( ! function_exists('cast_to_array'))
{
    /**
     * cast_to_array
     * 
     * @param mixed $args Description.
     *
     * @access public
     * @return mixed Value.
     */
    function cast_to_array($args)
    {
        if (!is_array($args))
            $args = array($args);

        return $args;    
    }
}
