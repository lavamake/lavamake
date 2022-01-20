<?php

namespace Lavamake\Lavamake\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LavamakeInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lavamake:install {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install lavamake service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function welcome()
    {
        $this->info("========================================================");
        $this->info('+     __                                      __       +');
        $this->info('+    / /   ____ ___  ______ _____ ___  ____ _/ /_____  +');
        $this->info('+   / /   / __ `/ | / / __ `/ __ `__ \/ __ `/ / _/ _ \ +');
        $this->info('+  / /___/ /_/ /| |/ / /_/ / / / / / / /_/ / ,< /  __/ +');
        $this->info('+ /_____/\__,_/ |___/\__,_/_/ /_/ /_/\__,_/_/|_|\___/  +');
        $this->info("========================================================");
        $this->line('');
        $this->info('Welcome to use Lavamake!');
        $this->info('More info and documents in https://lavamake.com!');
    }

    private function buildForWho()
    {
        return $this->choice('Who is the website build for?',[
            'single',
            'platform'
        ],'single');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->welcome();

        /**
         *  who is the website build for?
         */
        $build_for_who = $this->buildForWho();

        $foreign_key = $this->ask('Please enter the foreign key for the administrator table','user_id');

        $this->exportArticleMigration($foreign_key);
        $this->exportNavMigration($foreign_key);
        $this->copyModels($foreign_key);
        $this->writeEnv($build_for_who, $foreign_key);
        $this->copyConfig();
    }

    /**
     * copyModels
     *
     * @param $foreign_key
     *
     * @return void
     */
    protected function copyModels($foreign_key)
    {
        $article_model = app_path('Models/Article.php');
        if (file_exists($article_model) && !$this->option('force')) {
            if ($this->confirm("The [Article.php] file already exists. Do you want to replace it?")) {
                file_put_contents($article_model, $this->compileArticleModelStub($foreign_key));
            }
        } else {
            file_put_contents($article_model, $this->compileArticleModelStub($foreign_key));
        }

        $navigation_model = app_path('Models/Navigation.php');
        if (file_exists($navigation_model) && !$this->option('force')) {
            if ($this->confirm("The [Navigation.php] file already exists. Do you want to replace it?")) {
                file_put_contents($navigation_model, $this->compileNavigationModelStub($foreign_key));
            }
        } else {
            file_put_contents($navigation_model, $this->compileNavigationModelStub($foreign_key));
        }

    }

    /**
     * compileArticleModelStub
     *
     * @param $foreign_key
     *
     * @return array|false|string|string[]
     */
    protected function compileArticleModelStub($foreign_key)
    {
        if($foreign_key){
            $content = file_get_contents(__DIR__.'/../../stubs/models/Article.stub');
            return str_replace('{{foreign_key}}', $foreign_key, $content);
        }else{
            return file_get_contents(__DIR__.'/../../stubs/models/Article_single.stub');
        }

    }

    /**
     * compileNavigationModelStub
     *
     * @param $foreign_key
     *
     * @return array|false|string|string[]
     */
    protected function compileNavigationModelStub($foreign_key)
    {
        if($foreign_key){
            $content = file_get_contents(__DIR__.'/../../stubs/models/Navigation.stub');
            return str_replace('{{foreign_key}}', $foreign_key, $content);
        }else{
            return file_get_contents(__DIR__.'/../../stubs/models/Navigation_single.stub');
        }
    }

    /**
     * copyConfig
     *
     * @return void
     */
    protected function copyConfig()
    {
        $lavamake_path  = config_path('lavamake.php');
        if (file_exists($lavamake_path)  && ! $this->option('force') ){
            if ($this->confirm("The [config/lavamake.php] file already exists. Do you want to replace it?")) {
                file_put_contents($lavamake_path, file_get_contents($this->getConfigSource()));
            }
        }else{
            file_put_contents($lavamake_path,file_get_contents($this->getConfigSource()));
        }
    }

    /**
     * getConfigSource
     *
     * @return false|string
     */
    protected function getConfigSource()
    {
        return realpath(__DIR__.'/../../config/config.php');
    }

    /**
     * exportArticleMigration
     *
     * @param $foreign_key
     *
     * @return void
     */
    protected function exportArticleMigration($foreign_key)
    {
        $article_migration = database_path('migrations/2022_01_14_111432_create_articles_table.php');
        if (file_exists($article_migration) && ! $this->option('force')) {
            if ($this->confirm("The [2022_01_14_111432_create_articles_table.php] file already exists. Do you want to replace it?")) {
                file_put_contents($article_migration, $this->compileArticleMigrationStub($foreign_key));
            }
        }else{
            file_put_contents($article_migration, $this->compileArticleMigrationStub($foreign_key));
        }
    }

    /**
     * exportNavMigration
     *
     * @param $foreign_key
     *
     * @return void
     */
    protected function exportNavMigration($foreign_key)
    {
        $nav_migration = database_path('migrations/2022_01_14_112230_create_navigations_table.php');
        if (file_exists($nav_migration) && ! $this->option('force')) {
            if ($this->confirm("The [2022_01_14_112230_create_navigations_table.php] file already exists. Do you want to replace it?")) {
                file_put_contents($nav_migration, $this->compileNavigationMigrationStub($foreign_key));
            }
        }else{
            file_put_contents($nav_migration, $this->compileNavigationMigrationStub($foreign_key));
        }

    }

    protected function envPath()
    {
        if (method_exists($this->laravel, 'environmentFilePath')) {
            return $this->laravel->environmentFilePath();
        }

        return $this->laravel->basePath('.env');
    }

    /**
     * writeEnv
     *
     * @param $build_for_who
     * @param $foreign_key
     *
     * @return void
     */
    protected function writeEnv($build_for_who, $foreign_key = null)
    {
        // LAVAMAKE_BUILD_FOR
        if (file_exists($env_path = $this->envPath()) === false) {
            return;
        }

        if (Str::contains(file_get_contents($env_path), 'LAVAMAKE_BUILD_FOR') === false) {
            // create new entry
            file_put_contents($env_path, PHP_EOL."LAVAMAKE_BUILD_FOR=$build_for_who".PHP_EOL, FILE_APPEND);
        } else {
            if($this->laravel['config']['lavamake.build_for'] === $build_for_who) {
                return;
            }
            // update existing entry
            file_put_contents($env_path, str_replace(
                'LAVAMAKE_FOREIGN_KEY='.$this->laravel['config']['lavamake.build_for'],
                'LAVAMAKE_FOREIGN_KEY='.$build_for_who, file_get_contents($env_path)
            ));
        }

        if (Str::contains(file_get_contents($env_path), 'LAVAMAKE_FOREIGN_KEY') === false) {
            // create new entry
            file_put_contents($env_path, PHP_EOL."LAVAMAKE_FOREIGN_KEY=$foreign_key".PHP_EOL, FILE_APPEND);
        } else {
            if($this->laravel['config']['lavamake.foreign_key'] === $foreign_key) {
                return;
            }
            // update existing entry
            file_put_contents($env_path, str_replace(
                'LAVAMAKE_FOREIGN_KEY='.$this->laravel['config']['lavamake.foreign_key'],
                'LAVAMAKE_FOREIGN_KEY='.$foreign_key, file_get_contents($env_path)
            ));
        }
    }

    /**
     * compileArticleMigrationStub
     *
     * @param $foreign_key
     *
     * @return array|false|string|string[]
     */
    protected function compileArticleMigrationStub($foreign_key)
    {
        return str_replace(
            '{{user_id}}',
            $foreign_key,
            file_get_contents(__DIR__ . '/../../stubs/migrations/2022_01_14_111432_create_articles_table.stub')
        );
    }

    /**
     * compileNavigationMigrationStub
     *
     * @param $foreign_key
     *
     * @return array|false|string|string[]
     */
    protected function compileNavigationMigrationStub($foreign_key)
    {
        return str_replace(
            '{{user_id}}',
            $foreign_key,
            file_get_contents(__DIR__.'/../../stubs/migrations/2022_01_14_112230_create_navigations_table.stub')
        );
    }
}
