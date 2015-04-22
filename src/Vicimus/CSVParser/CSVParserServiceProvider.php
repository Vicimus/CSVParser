<?php namespace Vicimus\CSVParser;

use Illuminate\Support\ServiceProvider;

class CSVParserServiceProvider extends ServiceProvider {

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
		$this->package('vicimus/c-sv-parser');
		include(__DIR__.'/../../routes.php');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\View::addNamespace('CSVParser', __DIR__.'/../../views/');
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
