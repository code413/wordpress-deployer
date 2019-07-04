<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class updateGTM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    public function __construct($name)
    {
        $this->name = $name;
    }


    public function handle()
    {
        $value = $this->selectDB("$this->name.wp_options",'option_name','gtm4wp-options');
        $value= unserialize($value[0]->option_value);
        $value['gtm-env-gtm-auth'] = "";
        $value['gtm-env-gtm-preview'] = "";
        $value = serialize($value);
        $this->updateDB("$this->name.wp_options","option_value",$value,"option_name",'gtm4wp-options');
    }

    protected function selectDB($tableName,$columnName,$columnValue)
    {
        $query = "select * FROM {$tableName}";
        $query .= " WHERE $columnName ='$columnValue'";

        return DB::connection()->select($query);
    }

    protected function updateDB($tableName,$setColumn,$setColumnValue,$findColumn,$findColumnValue)
    {
        $query = "UPDATE $tableName";
        $query .= " SET $setColumn='{$setColumnValue}'";
        $query .= " WHERE $findColumn = '{$findColumnValue}'";

        DB::connection()->statement($query);
    }
}