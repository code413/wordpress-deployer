<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class updateIndexing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $value;

    public function __construct($name,$value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->value == 'yes')
        {
            $this->updateDB("$this->name.wp_options", 'option_value', '1', 'option_name', 'blog_public');
        }
        else
        {
            $this->updateDB("$this->name.wp_options", 'option_value', '0', 'option_name', 'blog_public');
        }

    }

    protected function updateDB($tableName, $setColumn, $setColumnValue, $findColumn, $findColumnValue)
    {
        $query = "UPDATE $tableName";
        $query .= " SET $setColumn='{$setColumnValue}'";
        $query .= " WHERE $findColumn = '{$findColumnValue}'";

        DB::connection()->statement($query);
    }
}
