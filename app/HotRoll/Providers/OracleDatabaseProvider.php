<?php

namespace App\HotRoll\Providers;

use Illuminate\Support\ServiceProvider;

use catfan\medoo;

class OracleDatabaseProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHelper();
        $this->app->alias('medoo', 'catfan\medoo');

    }

    protected function registerHelper()
    {
        $this->app->singleton('dbqms', function ($app) {

            $dbConnection = "database.connections.qms.";
            return $this->getMedooInstance($dbConnection);
        });

        $this->app->singleton('dbmes', function ($app) {

            $dbConnection = "database.connections.mes.";
            return $this->getMedooInstance($dbConnection);
        });
    }

    /**
     * Undocumented function
     *
     * @param [type] $dbConnection
     * @return localMedooInstance
     */
    public function getMedooInstance($dbConnection)
    {
        $driver = config($dbConnection . "driver");

        if ($driver !== 'sqlite') {
            $options = [
                'database_type' => $driver,
                'database_name' => config($dbConnection . "database"),
                'server' => config($dbConnection . "host"),
                'username' => config($dbConnection . "username"),
                'password' => config($dbConnection . "password"),
                'charset' => config($dbConnection . "charset"),
            ];

            $port = config($dbConnection . "port");

            if (!empty($port)) {
                $options['port'] = $port;
            }
        } elseif ($driver == 'sqlite') {
            $options = [
                'database_type' => $driver,
                'database_file' => config($dbConnection . "database"),
            ];
        }

        $prefix = config($dbConnection . "prefix");

        if (!empty($prefix)) {
            $options['prefix'] = $prefix;
        }

        return new \catfan\medoo($options);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['dbqms', 'dbmes'];
    }
}
