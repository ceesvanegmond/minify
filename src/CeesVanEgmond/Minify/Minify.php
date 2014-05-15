<?php  namespace CeesVanEgmond\Minify; 

use CeesVanEgmond\Minify\Exceptions\InvalidArgumentException;
use CeesVanEgmond\Minify\Providers\JavaScript;
use CeesVanEgmond\Minify\Providers\StyleSheet;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Minify
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var string
     */
    private $environment;

    /**
     * @var
     */
    private $provider;

    /**
     * @var
     */
    private $buildPath;

    /**
     * @var bool
     */
    private $fullUrl = false;

    /**
     * @param array $config
     * @param string $environment
     */
    public function __construct(array $config, $environment)
    {
        $this->checkConfiguration($config);

        $this->config = $config;
        $this->environment = $environment;
    }

    /**
     * @param $file
     * @param array $attributes
     * @return string
     */
    public function javascript($file, $attributes = array())
    {
        $this->provider = new JavaScript(public_path());
        $this->buildPath = $this->config['js_build_path'];
        $this->attributes = $attributes;

        $this->process($file);

        return $this;
    }

    /**
     * @param $file
     * @param array $attributes
     * @return string
     */
    public function stylesheet($file, $attributes = array())
    {
        $this->provider = new StyleSheet(public_path());
        $this->buildPath = $this->config['css_build_path'];
        $this->attributes = $attributes;

        $this->process($file);

        return $this;
    }

    /**
     * @param $dir
     * @return string
     */
    public function stylesheetDir($dir)
    {
	$this->provider = new StyleSheet(public_path());
	$this->buildPath = $this->config['css_build_path'];
	
	return $this->assetDirHelper('css', $dir);
    }
 	
    /**
     * @param $dir
     * @return string
     */	
    public function javascriptDir($dir)
    {
	$this->provider = new JavaScript(public_path());
	$this->buildPath = $this->config['js_build_path'];
	
	return $this->assetDirHelper('js', $dir);
    }
	
    /**
     * @param $ext
     * @param $dir
     * @return string
     */	
    private function assetDirHelper($ext, $dir)
    {
	$files = array();
	
	$dir_obj = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(public_path().$dir));
	foreach ($dir_obj as $fileinfo) 
	{
		if (!$fileinfo->isDir() && ($filename = $fileinfo->getFilename()) && (pathinfo($filename, PATHINFO_EXTENSION) == $ext) && (strlen($fileinfo->getFilename()) < 30)) 
		{
			$files[] = str_replace(public_path(), '', $fileinfo);
		}
	}
	 
	if (count($files) > 0)
	{
		$this->process($files);
	}
	
	return $this;
    }
	
    /**
     * @param $file
     */
    private function process($file)
    {
        $this->provider->add($file);

        if($this->minifyForCurrentEnvironment() && $this->provider->make($this->buildPath))
        {
            $this->provider->minify();
        }

        $this->fullUrl = false;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $baseUrl = $this->fullUrl ? $this->getBaseUrl() : '';
        if (!$this->minifyForCurrentEnvironment())
        {
            return $this->provider->tags($this->attributes, $baseUrl);
        }

        $filename = $baseUrl . $this->buildPath . $this->provider->getFilename();

        return $this->provider->tag($filename, $this->attributes);
    }

	/**
	 * @return bool
	 */
	protected function minifyForCurrentEnvironment()
	{
		return !in_array($this->environment, $this->config['ignore_environments']);
	}

    /**
     * @return string
     */
    public function withFullUrl()
    {
        $this->fullUrl = true;

        return $this->render();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param array $config
     * @throws Exceptions\InvalidArgumentException
     * @return array
     */
    private function checkConfiguration(array $config)
    {
        if(!isset($config['css_build_path']) || !is_string($config['css_build_path']))
            throw new InvalidArgumentException("Missing css_build_path field");
        if(!isset($config['js_build_path']) || !is_string($config['js_build_path']))
            throw new InvalidArgumentException("Missing js_build_path field");
        if(!isset($config['ignore_environments']) || !is_array($config['ignore_environments']))
            throw new InvalidArgumentException("Missing ignore_environments field");
    }

    /**
     * @return string
     */
    private function getBaseUrl()
    {
        return sprintf("%s://%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['HTTP_HOST']
        );
    }
}
