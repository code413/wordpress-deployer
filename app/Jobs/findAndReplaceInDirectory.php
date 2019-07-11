<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class findAndReplaceInDirectory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;
    protected $name;
    protected $destination;

    public function __construct(Profile $profile, $name)
    {
        $this->profile = $profile;
        $this->name = $name;
    }

    public function handle()
    {
        foreach ($this->profile->replacements()->where('type', 'File')->get() as $replacement) {
            $this->replaceText($replacement->from, $replacement->to, $this->profile->path_to.$this->name.'/'.$replacement->path, $replacement->pattern ?? null);
        }
    }

    protected function replaceText($from, $to, $path, $pattern = null)
    {
        $cmd = "find $path -type f";
        $cmd .= isset($pattern) ? " -name \"$pattern\"" : '';
        $cmd .= " -exec sed -i 's#$from#$to#g' {} +";
        $this->run($cmd);
    }
}
