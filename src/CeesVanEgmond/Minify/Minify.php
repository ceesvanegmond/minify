<?php  namespace CeesVanEgmond\Minify; 

use CeesVanEgmond\Minify\Providers\JavaScript;
use CeesVanEgmond\Minify\Providers\StyleSheet;

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
     * @param $environment
     */
    public function __construct(array $config, $environment)
    {
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

        //Return when minified file already exists
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
        if (in_array($this->environment, $this->config['ignore_envionments']))
        {
            $this->provider->tags($this->attributes);
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
}
