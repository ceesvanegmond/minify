<?php namespace CeesVanEgmond\Minify\Providers;

use CeesVanEgmond\Minify\Contracts\MinifyInterface;
use JSMin;

class JavaScript extends BaseProvider implements MinifyInterface
{
    /**
     *  The extension of the outputted file.
     */
    const EXTENSION = '.js';

    /**
     * @return string
     */
    public function minify()
    {
        $minified = JSMin::minify($this->appended);

        return $this->put($minified);
    }

    /**
     * @param $file
     * @param array $attributes
     * @return string
     */
    public function tag($file, array $attributes, $async, $defer)
    {
        $attributes['src'] = $file;
        $async = ($async) ? ' async' : '' ;
        $defer = ($defer) ? ' defer' : '' ;

        return "<script{$this->attributes($attributes)}{$async}{$defer}></script>" . PHP_EOL;
    }
}
