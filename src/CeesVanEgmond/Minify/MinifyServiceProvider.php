<?php namespace CeesVanEgmond\Minify;

use Config;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MinifyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('ceesvanegmond/minify');

        AliasLoader::getInstance()->alias(
            'Minify',
            'CeesVanEgmond\Minify\Facades\Minify'
        );
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['Minify'] = $this->app->share(function($app)
        {
            return new Minify(
                array(
                    'css_build_path' => Config::get('minify::css_build_path'),
                    'js_build_path' => Config::get('minify::js_build_path'),
                    'ignore_environments' => Config::get('minify::ignore_environments'),
					'base_url' => Config::get('minify::base_url'),
                ),
                $app->environment()
            );
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('minify');
	}
}
