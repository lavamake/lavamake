<?php

namespace Lavamake\Lavamake\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Lavamake\Lavamake\Article\ArticleForManage;
use Lavamake\Lavamake\Article\ArticleForWeb;
use Lavamake\Lavamake\Config\Config;
use Lavamake\Lavamake\Config\ConfigInterface;
use Lavamake\Lavamake\Console\LavamakeInstallCommand;
use Lavamake\Lavamake\Navigation\NavigationForManage;
use Lavamake\Lavamake\Navigation\NavigationForWeb;
use Lavamake\Lavamake\Policies\ArticlePolicy;
use Lavamake\Lavamake\Policies\NavigationPolicy;
use Lavamake\Lavamake\Utils\Consts;

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
     * register
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigSource(), Consts::CONFIG_LAVAMAKE);
        $this->registerAlias();
        $this->registerConfig();
        $this->registerArticle();
        $this->registerNavigation();
        $this->registerPolicies();

        $this->registerLavamakeCommand();
        $this->commands('lavamake.lavamake.install');
    }

    protected function registerPolicies()
    {
        foreach ($this->policies() as $key => $policy) {
            Gate::policy($key, $policy);
        }
    }

    protected function policies()
    {
        $articleModel = config("lavamake.models.article");
        $navigationModel = config("lavamake.models.navigation");

        return [
            $articleModel => ArticlePolicy::class,
            $navigationModel => NavigationPolicy::class
        ];
    }

    protected function registerAlias()
    {
        $this->app->alias('lavamake.lavamake.config', ConfigInterface::class);
        $this->app->alias('lavamake.lavamake.article.manage', ArticleForManage::class);
        $this->app->alias('lavamake.lavamake.article.web', ArticleForWeb::class);
    }

    protected function registerArticle()
    {
        $this->app->singleton('lavamake.lavamake.models.article', function ($app) {
            return $this->getConfigInstance('models.article');
        });
        $this->app->singleton('lavamake.lavamake.article.manage', function ($app) {
            return new ArticleForManage(
                $app['lavamake.lavamake.models.article'],
                $app['lavamake.lavamake.config'],
                $app['router']->getCurrentRequest()
            );
        });
        $this->app->singleton('lavamake.lavamake.article.web', function ($app) {
            return new ArticleForWeb(
                $app['lavamake.lavamake.models.article'],
                $app['lavamake.lavamake.config'],
                $app['router']->getCurrentRequest()
            );
        });
    }

    protected function registerNavigation()
    {
        $this->app->singleton('lavamake.lavamake.models.navigation', function ($app) {
            return $this->getConfigInstance('models.navigation');
        });
        $this->app->singleton('lavamake.lavamake.nav.web', function ($app) {
            return new NavigationForWeb(
                $app['lavamake.lavamake.models.navigation'],
                $app['lavamake.lavamake.models.article'],
                $app['lavamake.lavamake.config'],
                $app['router']->getCurrentRequest()
            );
        });
        $this->app->singleton('lavamake.lavamake.nav.manage', function ($app) {
            return new NavigationForManage(
                $app['lavamake.lavamake.models.navigation'],
                $app['lavamake.lavamake.models.article'],
                $app['lavamake.lavamake.config'],
                $app['router']->getCurrentRequest()
            );
        });
    }

    public function registerConfig()
    {
        $this->app->singleton('lavamake.lavamake.config', function ($app) {
            return new Config($app['config']);
        });
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

    /**
     * Helper to get the config values.
     *
     * @param  string  $key
     * @param  string  $default
     *
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return config("lavamake.$key", $default);
    }

    /**
     * Get an instantiable configuration instance.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    protected function getConfigInstance($key)
    {
        $instance = $this->config($key);

        if (is_string($instance)) {
            return $this->app->make($instance);
        }

        return $instance;
    }
}
