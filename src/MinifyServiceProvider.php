<?php namespace CeesVanEgmond\Minify;

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
    $this->publishConfig();
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {

    $this->registerServices();

  }

  /**
   * Register the package services.
   *
   * @return void
   */
  protected function registerServices() {
    $this->app->bindShared('minify', function ($app) {
      return new Minify(
        array(
          'css_build_path' => config('minify.config.css_build_path'),
          'js_build_path' => config('minify.config.js_build_path'),
          'ignore_environments' => config('minify.config.ignore_environments'),
          'base_url' => config('minify.config.base_url'),
        ),
        $app->environment()
      );
    });
  }

  /**
   * Publish the package configuration
   */
  protected function publishConfig() {
    $this->publishes([
       __DIR__ . '/config/config.php' => config_path('minify.config.php'),
    ]);
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
