<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class updateGTM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $value;
    protected $profile;

    public function __construct($name,$profile)
    {
        $this->name = $name;
        $this->profile = $profile;
        $this->value = json_decode($profile->options)->enable_gtm;
    }

    public function handle()
    {
        $value = $this->selectDB("$this->name.wp_options", 'option_name', 'gtm4wp-options');

        if (isset($value[0]->option_value) && $this->value == 'yes') {
            $value = unserialize($value[0]->option_value);
            $value['gtm-env-gtm-auth'] =  '';
            $value['gtm-env-gtm-preview'] = '';
            $value['gtm-code'] = isset($this->profile->gtm_id) ? $this->profile->gtm_id : '';
            $value = serialize($value);
            $this->updateDB("$this->name.wp_options", 'option_value', $value, 'option_name', 'gtm4wp-options');
        }

        if (isset($value[0]->option_value) && $this->value == 'no') {
            $value = unserialize($value[0]->option_value);
            $value['gtm-code'] = isset($this->profile->gtm_id) ? $this->profile->gtm_id : '';
            $value['gtm-env-gtm-auth'] = isset($this->profile->gtm_auth) ? $this->profile->gtm_auth : '';
            $value['gtm-env-gtm-preview'] = isset($this->profile->gtm_preview) ? $this->profile->gtm_preview : '';
            $value = serialize($value);
            $this->updateDB("$this->name.wp_options", 'option_value', $value, 'option_name', 'gtm4wp-options');
        }
    }

    protected function selectDB($tableName, $columnName, $columnValue)
    {
        $query = "select * FROM {$tableName}";
        $query .= " WHERE $columnName ='$columnValue'";

        return DB::connection()->select($query);
    }

    protected function updateDB($tableName, $setColumn, $setColumnValue, $findColumn, $findColumnValue)
    {
        $query = "UPDATE $tableName";
        $query .= " SET $setColumn='{$setColumnValue}'";
        $query .= " WHERE $findColumn = '{$findColumnValue}'";

        DB::connection()->statement($query);
    }
}
