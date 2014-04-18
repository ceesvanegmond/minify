<?php namespace MinicStudio\Minify\Providers;

use MinicStudio\Minify\Contracts\MinifyInterface;
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
    public function tag($file, array $attributes)
    {
        $attributes['src'] = $file;

        return "<script{$this->attributes($attributes)}></script>" . PHP_EOL;
    }
}
