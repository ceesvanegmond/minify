<?php

if ( ! function_exists('stylesheet'))
{
    /**
     * stylesheet
     * 
     * @param mixed $args Description.
     *
     * @access public
     * @return mixed Value.
     */
    function stylesheet($args)
    {
        $args = cast_to_array($args);
        if (App::environment() !== 'local')
            return \HTML::style(App::make('minify')->minifyCss($args));

        $path = Config::get('minify::css_path', '/css/');
        $return = '';
        foreach ($args as $arg)
        {
            $return .= \HTML::style($path . $arg);
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
     * @access public
     * @return mixed Value.
     */
    function javascript($args)
    {
        $args = cast_to_array($args);
        if (App::environment() !== 'local')
            return \HTML::script(App::make('minify')->minifyJs($args));
        
        $path = Config::get('minify::js_path', '/js/');
        $return = '';
        foreach ($args as $arg)
        {
            $return .= \HTML::script($path . $arg);
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