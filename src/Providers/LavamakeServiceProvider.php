<?php

namespace Lavamake\Lavamake\Providers;

use Illuminate\Support\ServiceProvider;
use Lavamake\Lavamake\Console\LavamakeInstallCommand;
use Lavamake\Lavamake\Support\Manage\Article\ManageArticle;
use Lavamake\Lavamake\Support\Web\Navigation\NavService;
use Lavamake\Lavamake\Support\Web\Article\WebArticle;

class LavamakeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([$this->getConfigSource() => config_path('lavamake.php')], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     *
     * by Menlain
     * 2022/1/18 - 4:01 AM
     */
    public function register()
    {

        $this->mergeConfigFrom($this->getConfigSource(), 'lavamake');

        $this->registerLavamakeCommand();
        $this->commands('lavamake.lavamake.install');

        $this->registerAlias();

        $this->app->singleton('lavamake.lavamake.article.manage', function ($app) {
            return new ManageArticle($app['config'], $app['router']->getCurrentRequest());
        });

        $this->app->singleton('lavamake.lavamake.article.web', function ($app) {
            return new WebArticle($app['config'], $app['router']->getCurrentRequest(), $app['events']);
        });

        $this->app->singleton('lavamake.lavamake.nav.web', function ($app) {
            return new NavService($app['config'], $app['router']->getCurrentRequest());
        });
    }

    protected function registerAlias()
    {
        $this->app->alias('lavamake.lavamake.article.manage', ManageArticle::class);
        $this->app->alias('lavamake.lavamake.article.web', WebArticle::class);
        $this->app->alias('lavamake.lavamake.nav.web', NavService::class);
    }

    /**
     * register Lavamake Command
     *
     * @return void
     */
    protected function registerLavamakeCommand()
    {
        $this->app->singleton('lavamake.lavamake.install', function () {
            return new LavamakeInstallCommand();
        });
    }

    protected function getConfigSource()
    {
        return realpath(__DIR__.'/../../config/config.php');
    }
}
