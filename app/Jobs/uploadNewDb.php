<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class uploadNewDb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $dbCredentials;
    protected $temporaryPath;

    public function __construct($name, $dbCredentials, $temporaryPath)
    {
        $this->name = $name;
        $this->dbCredentials = $dbCredentials;
        $this->temporaryPath = $temporaryPath;
    }

    public function handle()
    {
        $cmd = 'mysql';
        $cmd .= $this->dbCredentials;
        $cmd .= "  $this->name";
        $cmd .= "  < {$this->temporaryPath}/$this->name.sql";

        (new Process($cmd))->setTimeout(120)->run();
    }
}
