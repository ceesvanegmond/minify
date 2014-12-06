<?php  namespace CeesVanEgmond\Minify\Providers;

use CeesVanEgmond\Minify\Exceptions\CannotRemoveFileException;
use CeesVanEgmond\Minify\Exceptions\CannotSaveFileException;
use CeesVanEgmond\Minify\Exceptions\DirNotExistException;
use CeesVanEgmond\Minify\Exceptions\DirNotWritableException;
use CeesVanEgmond\Minify\Exceptions\FileNotExistException;
use Countable;

abstract class BaseProvider implements Countable
{
    /**
     * @var string
     */
    protected $outputDir;

    /**
     * @var string
     */
    protected $appended = '';

    /**
     * @var string
     */
    protected $filename = '';

    /**
     * @var array
     */
    protected $files = array();

    /**
     * @var string
     */
    private $publicPath;

    /**
     * @param null $publicPath
     */
    public function __construct($publicPath = null)
    {
        $this->publicPath = $publicPath ?: $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * @param $outputDir
     * @return bool
     */
    public function make($outputDir)
    {
        $this->outputDir = $this->publicPath . $outputDir;

        $this->checkDirectory();

        if ($this->checkExistingFiles())
        {
            return false;
        }

        $this->removeOldFiles();
        $this->appendFiles();

        return true;
    }

    /**
     * @param  $file
     * @return void
     * @throws \CeesVanEgmond\Minify\Exceptions\FileNotExistException
     */
    public function add($file)
    {
        if (is_array($file))
        {
            array_map(array($this, 'add'), $file);
        }
        else
        {
            $file = $this->publicPath . $file;
            if (!file_exists($file))
            {
                throw new FileNotExistException("File '{$file}' does not exist");
            }

            $this->files[] = $file;
        }
    }

    /**
     * @param      $baseUrl
     * @param $attributes
     *
     * @return string
     */
    public function tags($baseUrl, $attributes)
    {
        $html = '';
        foreach($this->files as $file)
        {
            $file = $baseUrl . str_replace($this->publicPath, '', $file);
            $html .= $this->tag($file, $attributes);
        }

        return $html;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->files);
    }

    /**
     *
     */
    protected function appendFiles()
    {
        foreach ($this->files as $file) {
            $this->appended .= file_get_contents($file);
        }
    }

    /**
     * @return bool
     */
    protected function checkExistingFiles()
    {
        $this->buildMinifiedFilename();

        return file_exists($this->outputDir . $this->filename);
    }

    /**
     * @throws \CeesVanEgmond\Minify\Exceptions\DirNotWritableException
     * @throws \CeesVanEgmond\Minify\Exceptions\DirNotExistException
     */
    protected function checkDirectory()
    {
        if (!file_exists($this->outputDir))
        {
            throw new DirNotExistException("Buildpath '{$this->outputDir}' does not exist");
        }

        if (!is_writable($this->outputDir))
        {
            throw new DirNotWritableException("Buildpath '{$this->outputDir}' is not writable");
        }
    }

    /**
     * @return string
     */
    protected function buildMinifiedFilename()
    {
        $this->filename = $this->getHashedFilename() . $this->countModificationTime() . static::EXTENSION;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     * @return string
     */
    protected function attributes($attributes)
    {
        $html = array();
        foreach ((array) $attributes as $key => $value)
        {
            $element = $this->attributeElement($key, $value);

            if ( ! is_null($element)) $html[] = $element;
        }

        $output = count($html) > 0 ? ' '.implode(' ', $html) : '';

        return trim($output);
    }

    /**
     * Build a single attribute element.
     *
     * @param  string|integer $key
     * @param  string|boolean $value
     * @return string|null
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) $key = $value;

        if(is_bool($value))
            return $key;

        if ( ! is_null($value))
            return $key.'="'.htmlentities($value, ENT_QUOTES, 'UTF-8', false).'"';

        return null;
    }

    /**
     * @return string
     */
    protected function getHashedFilename()
    {
        return md5(implode('-', $this->files));
    }

    /**
     * @return int
     */
    protected function countModificationTime()
    {
        $time = 0;

        foreach ($this->files as $file)
        {
            $time += filemtime($file);
        }

        return $time;
    }

    /**
     * @throws \CeesVanEgmond\Minify\Exceptions\CannotRemoveFileException
     */
    protected function removeOldFiles()
    {
        $pattern = $this->outputDir . $this->getHashedFilename() . '*';
        $find = glob($pattern);

        if( is_array($find) && count($find) )
        {
            foreach ($find as $file)
            {
                if ( ! unlink($file) ) {
                    throw new CannotRemoveFileException("File '{$file}' cannot be removed");
                }
            }
        }
    }

    /**
     * @param $minified
     * @return string
     * @throws \CeesVanEgmond\Minify\Exceptions\CannotSaveFileException
     */
    protected function put($minified)
    {
        if(!file_put_contents($this->outputDir . $this->filename, $minified))
        {
            throw new CannotSaveFileException("File '{$this->outputDir}{$this->filename}' cannot be saved");
        }

        return $this->filename;
    }

    /**
     * @return string
     */
    public function getAppended()
    {
        return $this->appended;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
