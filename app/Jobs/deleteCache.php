<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Process;

class deleteCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    public function __construct($path)
    {
        $this->path = $path;
    }


    public function handle()
    {
        $directories = ['cache/','w3tc-config/','advance-cache.php','db.php','object-cache.php'];

        foreach ($directories as $file)
        {
            $cmd = "rm -rf {$this->path}/wp-content/{$file}";
            (new Process($cmd))->run();
        }
    }
}
