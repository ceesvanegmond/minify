<?php  namespace MinicStudio\Minify; 

use MinicStudio\Minify\Exceptions\InvalidArgumentException;
use MinicStudio\Minify\Providers\JavaScript;
use MinicStudio\Minify\Providers\StyleSheet;

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
     * @param $file
     */
    private function process($file)
    {
        $this->provider->add($file);

        if($this->provider->make($this->buildPath))
        {
            $this->provider->minify();
        }
    }

    /**
     * @return mixed
     */
    public function render()
    {
        if (in_array($this->environment, $this->config['ignore_environments']))
        {
            return $this->provider->tags($this->attributes);
        }

        return $this->provider->tag($this->buildPath . $this->provider->getFilename(), $this->attributes);
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
}
