<?php namespace Lifeentity\Images;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Image;
use Lifeentity\Images\Cryption\ProcessMCryptifier;

class ImagesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->package('lifeentity/images');

        $app = $this->app;

        // Get required configurations
        $appKey = $app['config']->get('app.key');
        $config = $app['config']->get('images::config');

        // Add the filter class as a singleton
        $app->singleton('Lifeentity\Images\ImageFilter');

        // Add process cryptifier as a singleton
        $app->singleton('Lifeentity\Images\Cryption\ProcessCryptifierInterface', function() use($appKey)
        {
            return new ProcessMCryptifier($appKey);
        });

        $app->bind('Lifeentity\Images\ImageProcess', function($app, $operations)
        {
            return new ImageProcess($app->make('Lifeentity\Images\ImageFilter'), $operations);
        });

        $app['router']->get($config['images_dir'].'/{path}/'.$config['cache_dir'] . '/{cached_image}', array(

            'as' => 'cached.image',
            'uses' => 'Lifeentity\Images\CachedImageController@display'

        ))->where('path', '.*')->where('cached_image', '.*');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
