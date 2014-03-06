<?php namespace CeesVanEgmond\Minify;

use CeesVanEgmond\Minify\Exceptions\FileNotFoundException;
use Config;
use CssMin;
use JSMin;

class Minify
{
    /**
     * $files
     *
     * @var array
     *
     * @access protected
     */
    protected $files;

    /**
     * $buildpath
     *
     * @var string
     *
     * @access protected
     */
    protected $buildpath;

    /**
     * $path
     *
     * @var string
     *
     * @access protected
     */
    protected $path;

    /**
     * styles
     *
     * @param mixed $files
     *
     * @access public
     * @return mixed Value.
     */
    public function styles(array $files)
    {
        $jsPath = Config::get('minify::css_path');
        $jsBuildPath = Config::get('minify::css_build_path');

        list($filehash, $output, $relative) = $this->before($files, $jsPath, $jsBuildPath, '.css');

        if(!file_exists($output))
        {
            $result = CssMin::minify($this->appendAllFiles());

            $this->deleteOldMinifiedFiles($filehash);

            file_put_contents($output, $result);
        }

        return $relative;
    }

    /**
     * javascript
     *
     * @param mixed $files Description.
     *
     * @access public
     * @return mixed Value.
     */
    public function javascript(array $files)
    {
        $jsPath = Config::get('minify::js_path');
        $jsBuildPath = Config::get('minify::js_build_path');

        list($filehash, $output, $relative) = $this->before($files, $jsPath, $jsBuildPath, '.js');

        if(!file_exists($output))
        {
            $result = JSMin::minify($this->appendAllFiles());

            $this->deleteOldMinifiedFiles($filehash);

            file_put_contents($output, $result);
        }

        return $relative;
    }


    /**
     * before
     *
     * @access protected
     * @param array $files
     * @param $path
     * @param $buildPath
     * @param $extension
     * @return mixed array.
     */
    protected function before(array $files, $path, $buildPath, $extension)
    {
        $this->files = $files;
        $this->path = public_path() . $path;
        $this->buildpath = $this->path . $buildPath;

        $this->createBuildPathIfNotExist();

        $filehash = md5(implode('-', $this->files));
        $filename = $filehash . $this->calculateModifiedTimes() . $extension;

        $output = $this->buildpath . $filename;
        $relative = $path . $buildPath . $filename;

        return array($filehash, $output, $relative);
    }

    /**
     * createBuildPathIfNotExist
     *
     * @access private
     */
    private function createBuildPathIfNotExist()
    {
        if(!is_dir($this->buildpath))
        {
            mkdir($this->buildpath);
        }
    }

    /**
     * appendAllFiles
     *
     * @access private
     * @return mixed Value.
     */
    private function appendAllFiles()
    {
        $all = '';
        foreach ($this->files as $file)
        {
            $all .= file_get_contents($this->path . $file);
        }

        return $all;
    }

    /**
     * calculateModifiedTimes
     *
     * @access private
     * @throws Exceptions\FileNotFoundException
     * @return mixed string.
     */
    private function calculateModifiedTimes()
    {
        $time = 0;
        foreach ($this->files as $file)
        {
            $filepath = $this->path . $file;
            if(!file_exists($filepath))
            {
                throw new FileNotFoundException("File {$file} doest not exists at {$filepath}");
            }

            $time += filemtime($filepath);
        }

        return $time;
    }

    /**
     * calculateModifiedTimes
     *
     * Deletes files based on the same files with an other
     * timestamp
     *
     * @access private
     * @param $filehash
     */
    private function deleteOldMinifiedFiles($filehash)
    {
        $pattern = $this->buildpath . $filehash . '*';

        foreach (glob($pattern) as $file)
        {
            @unlink($file);
        }
    }
}
