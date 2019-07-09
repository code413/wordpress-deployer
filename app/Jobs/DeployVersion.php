<?php

namespace App\Jobs;

use App\Models\Version;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class DeployVersion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $version;
    protected $destination;
    protected $targetSymlink;

    public function __construct($version)
    {
        $this->version = $version;
        $version = Version::find($version);
        $this->destination = $version->profile->path_to;
        $this->targetSymlink = $version->profile->symlink;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->createSymlink("v$this->version");
        $this->renameDirectory($this->version);
        $this->activate($this->version);
    }

    protected function createSymlink($name)
    {
        if (is_dir("{$this->destination}{$name}x")) {
            $cmd = "mv {$this->destination}{$name}x {$this->destination}{$name}";
            $this->run($cmd);
        }

        $cmd = "ln -snf {$this->destination}{$name} {$this->targetSymlink}";
        $this->run($cmd);
    }

    protected function renameDirectory($id)
    {
        $version = Version::where('is_active', 1)->first();
        if (isset($version) && $version->id != $id) {
            $cmd = "mv {$this->destination}v{$version->id} {$this->destination}v{$version->id}x";
            $this->run($cmd);
        }
    }

    protected function activate($id)
    {
        $version = Version::find($id);
        Version::where('is_active', 1)->where('profile_id', $version->profile_id)->update(['is_active'=>0]);
        $version->update(['is_active'=>1]);
    }

    protected function run($cmd)
    {
        return (new Process($cmd))->run();
    }
}
