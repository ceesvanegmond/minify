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
     * @return string
     */
    public function tag($file)
    {
        return "<script src='{$file}'></script>";
    }
}
