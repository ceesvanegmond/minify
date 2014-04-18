<?php namespace MinicStudio\Minify\Contracts;

interface MinifyInterface {

    /**
     * @return mixed
     */
    public function minify();

    /**
     * @param $file
     * @param $attributes
     * @return mixed
     */
    public function tag($file, array $attributes);
}
