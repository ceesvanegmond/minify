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
     * @var string
     */
    private $environment;

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
     * @return string
     */
    public function javascript($file)
    {
        $provider = new JavaScript(public_path());
        $buildPath = $this->config['js_build_path'];

        return $this->process($file, $provider, $buildPath);
    }

    /**
     * @param $file
     * @return string
     */
    public function stylesheet($file)
    {
        $provider = new StyleSheet(public_path());
        $buildPath = $this->config['css_build_path'];

        return $this->process($file, $provider, $buildPath);
    }

    /**
     * @param $file
     * @param $provider
     * @param $buildPath
     * @return string
     */
    private function process($file, $provider, $buildPath)
    {
        $provider->add($file);

        if (in_array($this->environment, $this->config['ignore_envionments']))
        {
            return $provider->tags();
        }

        //Return when minified file already exists
        if(!$provider->make($buildPath))
        {
            return $provider->tag($buildPath . $provider->getFilename());
        }

        $provider->minify();

        return $provider->tag($buildPath . $provider->getFilename());
    }
} 
