<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Process;

class deleteDirectory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $version;
    protected $destination;
    protected $temporaryPath;

    public function __construct($version)
    {
        $this->version = $version;
    }


    public function handle()
    {
        $name = "v$this->version";
        if (is_dir("{$this->destination}{$name}x"))
        {
            $cmd = "rm -rf {$this->destination}{$name}x";
            $this->run($cmd);
        }
        else{
            $cmd = "rm -rf {$this->destination}{$name}";
            $this->run($cmd);
        }
    }

    protected function run($cmd)
    {
        return (new Process($cmd))->run();
    }
}