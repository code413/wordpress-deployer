<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class updateConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $profile;


    public function __construct($name,Profile $profile)
    {
        $this->name = $name;
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = file_get_contents("{$this->profile->path_to}$this->name/wp-config.php");
        $file = str_replace("define( 'DB_NAME', '$this->profile->db_name' )", "define( 'DB_NAME', '$this->name' )", $file);
        file_put_contents("{$this->profile->path_to}$this->name/wp-config.php", $file);
    }
}
