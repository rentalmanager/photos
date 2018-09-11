<?php
namespace RentalManager\Photos;


use Illuminate\Support\ServiceProvider;

/**
 * Created by PhpStorm.
 * Date: 7/4/18
 * Time: 12:13 PM
 * ImagesServiceProvider.php
 * @author Goran Krgovic <goran@dashlocal.com>
 */

class PhotosServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'MakePhoto' => 'command.photos.photo',
        'Setup' => 'command.photos.setup',
        'AddPhotoableTrait' => 'command.photos.add-photoable-trait'
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Merge config file for the current app
        $this->mergeConfigFrom(__DIR__.'/../config/photos.php', 'photos');

        // Publish the config files
        $this->publishes([
            __DIR__.'/../config/photos.php' => config_path('photos.php')
        ], 'photos');

        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }


    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Register the app
        $this->registerApp();

        // Register Commands
        $this->registerCommands();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerApp()
    {
        $this->app->bind('photos', function ($app) {
            return new Photos($app);
        });

        $this->app->alias('photos', 'RentalManager\Photos');
    }

    /**
     * Register the given commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";
            call_user_func_array([$this, $method], []);
        }
        $this->commands(array_values($this->commands));
    }

    protected function registerSetupCommand()
    {
        $this->app->singleton('command.photos.setup', function () {
            return new \RentalManager\Photos\Commands\SetupCommand();
        });
    }


    protected function registerMakePhotoCommand()
    {
        $this->app->singleton('command.photos.photo', function ($app) {
            return new \RentalManager\Photos\Commands\MakePhotoCommand($app['files']);
        });
    }

    protected function registerAddPhotoableTraitCommand()
    {
        $this->app->singleton('command.photos.add-photoable-trait', function () {
            return new \RentalManager\Photos\Commands\AddPhotoableTraitCommand();
        });
    }


    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return array_values($this->commands);
    }

}
