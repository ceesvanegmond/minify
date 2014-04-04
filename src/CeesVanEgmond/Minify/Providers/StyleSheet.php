<?php namespace CeesVanEgmond\Minify\Providers;

use CeesVanEgmond\Minify\Contracts\MinifyInterface;
use CssMinifier;

class StyleSheet extends BaseProvider implements MinifyInterface
{
    /**
     *  The extension of the outputted file.
     */
    const EXTENSION = '.css';

    /**
     * @return string
     */
    public function minify()
    {
        $minified = new CssMinifier($this->appended);

        return $this->put($minified->getMinified());
    }

    /**
     * @param $file
     * @return string
     */
    public function tag($file)
    {
        return "<link href='{$file}' rel='stylesheet'>";
    }
}
