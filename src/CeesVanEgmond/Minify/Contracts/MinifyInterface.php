<?php namespace CeesVanEgmond\Minify\Contracts;

interface MinifyInterface {

    /**
     * @return mixed
     */
    public function minify();

    /**
     * @param $file
     * @return mixed
     */
    public function tag($file);
}
