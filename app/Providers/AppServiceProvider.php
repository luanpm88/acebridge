<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->wordpressDBConnect();
    }

    public function wordpressDBConnect()
    {
        if (!defined('DB_HOST')) {
            return false;
        }

        // =================== WORDPRESS CONNECTION =============================
        global $table_prefix;            
        // WordPress database connection
        if (strpos(DB_HOST, ':') !== false) {
            $dbHost = explode(':', DB_HOST)[0];
            $dbPort = explode(':', DB_HOST)[1];
        } else {
            $dbHost = DB_HOST;
            $dbPort = '3306';
        }
        config([
            'database.connections.mysql.host' => $dbHost,
            'database.connections.mysql.port' => $dbPort,
            'database.connections.mysql.database' => DB_NAME,
            'database.connections.mysql.username' => DB_USER,
            'database.connections.mysql.password' => DB_PASSWORD,
            'database.connections.mysql.prefix' => $table_prefix . 'beemail_',
            'database.connections.mysql_wp.host' => $dbHost,
            'database.connections.mysql_wp.port' => $dbPort,
            'database.connections.mysql_wp.database' => DB_NAME,
            'database.connections.mysql_wp.username' => DB_USER,
            'database.connections.mysql_wp.password' => DB_PASSWORD,
            'database.connections.mysql_wp.prefix' => $table_prefix,
        ]);
        // =================== END WORDPRESS CONNECTION ===========================
    }
}
