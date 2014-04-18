<?php namespace MinicStudio\Minify\Facades;

use Illuminate\Support\Facades\Facade;

class Minify extends Facade
{
    /**
     * Name of the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Minify';
    }
}
