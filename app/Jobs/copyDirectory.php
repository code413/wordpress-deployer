<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class copyDirectory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $profile;

    public function __construct($name, Profile $profile)
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
        if (!is_dir("{$this->profile->path_to}$this->name")) {
            mkdir("{$this->profile->path_to}$this->name");
        }
        $cmd = "cp -a {$this->profile->path_from}* {$this->profile->path_to}$this->name";
        (new Process($cmd))->setTimeout(120)->run();
    }
}
