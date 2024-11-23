<?php

use Odin\Database\MyAdmin;
use Illuminate\Support\Facades\Config;

if(!function_exists('check_db_migrate')){
    function check_db_migrate(){
        // return app(MyAdmin::class, [Config::get("")]);
        $myConfig = Config::get('database.myadmin', []);
        $myAdmin = new MyAdmin($myConfig['host'], $myConfig['username'], $myConfig['password']);
        $myAdmin->connect();
        return $myAdmin->tableExists(Config::get('database.connections.mysql.database'), 'users');
    }
}