<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class dumpSourceDb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;

    protected $db;

    protected $dbCredentials;

    public function __construct($path, $db, $dbCredentials)
    {
        $this->path = $path;
        $this->db = $db;
        $this->dbCredentials = $dbCredentials;
    }

    public function handle()
    {
        $cmd = 'mysqldump ';
        $cmd .= $this->dbCredentials;
        $cmd .= "  {$this->db['name']}";
        $cmd .= "  > $this->path";

        (new Process($cmd))->setTimeout(120)->run();
    }
}
