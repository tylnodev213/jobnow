<?php

namespace App\Providers;

use Core\Providers\Facades\Schema\CustomBlueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // custom schema
        $this->customSchema();

        // ignore sanctum migration
        Sanctum::ignoreMigrations();

        // only HTTPs
        $this->ensureHttps();

        // custom log
        $this->app->bind('channellog', 'Core\Providers\Facades\Log\ChannelWriter');

        // custom storage
        $this->app->bind('basestorage', 'Core\Providers\Facades\Storages\NewStorage');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // log SQL
        $this->logSql();
    }

    /**
     * custom schema
     */
    protected function customSchema()
    {
        $this->app->bind('db.custom.schema', function ($app) {
            $schema = $app['db']->connection()->getSchemaBuilder();
            $schema->blueprintResolver(function ($table, $callback) {
                return new CustomBlueprint($table, $callback);
            });
            return $schema;
        });
    }

    /**
     * set Https only
     */
    protected function ensureHttps()
    {
        if (config('app.https')) {
            url()->forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * log SQL
     */
    protected function logSql()
    {
        if (!isEnableLogSql()) {
            return;
        }

        try {
            DB::listen(function ($sql) {
                $isJobs = str_contains($sql->sql, 'jobs') || str_contains($sql->sql, 'failed_jobs');
                if (App::runningInConsole() && $isJobs) {
                    return;
                }

                foreach ($sql->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $sql->bindings[$i] = "'$binding'";
                        }
                    }
                }
                // Insert bindings into query
                $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
                $query = vsprintf($query, $sql->bindings);
                $area = strtoupper(getArea());
                $log = "[{$area}] Time: {$sql->time} - SQL: {$query}";

                logDebug($log, [], 'NASUCTRH', getConfig('logs.sql_log_filename'));
            });
        } catch (\Exception $exception) {
            // write log errors
        }
    }
}
