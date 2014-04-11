<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | App environments to not minify
    |--------------------------------------------------------------------------
    |
    | These environments will not be minified and all individual files are
    | returned
    |
    */

    'ignore_environments' => array(
	    'local',
    ),

    /*
    |--------------------------------------------------------------------------
    | CSS build path
    |--------------------------------------------------------------------------
    |
    | Minify is an extention that can minify your css files into one build file.
    | The css_builds_path property is the location where the builded files are
    | stored. This is relative to your public path. Notice the trailing slash.
    | Note that this directory must be writable.
    |
    */

    'css_build_path' => '/css/builds/',

    /*
    |--------------------------------------------------------------------------
    | JS build path
    |--------------------------------------------------------------------------
    |
    | Minify is an extention that can minify your js files into one build file.
    | The js_build_path property is the location where the builded files are
    | stored. This is relative to your public path. Notice the trailing slash.
    | Note that this directory must be writable.
    |
    */

    'js_build_path' => '/js/builds/',

);
