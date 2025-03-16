<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        \Illuminate\Database\MySqlConnection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            $connection = new \Illuminate\Database\MySqlConnection($connection, $database, $prefix, $config);
            $connection->setSchemaGrammar(new \Illuminate\Database\Schema\Grammars\MySqlGrammar);
            $connection->setQueryGrammar(new \Illuminate\Database\Query\Grammars\MySqlGrammar);
            $connection->setPostProcessor(new \Illuminate\Database\Query\Processors\MySqlProcessor);
            $connection->statement('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');
            return $connection;
        });

        Response::macro('jsonUnescaped', function ($data, $status = 200, array $headers = [], $options = 0) {
            $options = JSON_UNESCAPED_SLASHES | $options;
            return Response::json($data, $status, $headers, $options);
        });
        
        JsonResource::withoutWrapping();
    }
}
