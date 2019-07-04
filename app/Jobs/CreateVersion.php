<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Models\Version;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class CreateVersion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    protected $source;

    protected $destination;

    protected $temporaryPath;

    protected $db;

    protected $sourceDb;

    protected $dbCredentials;

    protected $profile;


    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
        $this->source = $profile->path_from;
        $this->destination = $profile->path_to;
        $this->db = [
            'host' => $profile->db_host,
            'name' => $profile->db_name,
            'user' => $profile->db_user,
            'password' => $profile->db_password,
        ];
        $this->temporaryPath = $profile->path_temp;
        $this->dbCredentials = " -h {$this->db['host']} --user={$this->db['user']} --password={$this->db['password']}";



        if (!is_dir($this->destination))
        {
            mkdir($this->destination);
        }
        if (!is_dir($this->temporaryPath))
        {
            mkdir($this->temporaryPath);
        }
    }


    public function handle()
    {
        $version = $this->createNewVersion();

        $slug = "v{$version->id}";
        $this->sourceDb = $this->db['name'];

        $this->dumpSourceDb("{$this->temporaryPath}{$slug}.sql");

        $this->findAndReplaceInDbDump("{$this->temporaryPath}{$slug}.sql");

        $this->createNewDb($slug);

        $this->uploadNewDb($slug);

        $this->copyDirectory($slug);

        $this->findAndReplaceInDirectory($slug);

        $this->updateConfig($slug);


        if (isset(json_decode($this->profile->options)->disable_maintenance) && json_decode($this->profile->options)->disable_maintenance == 'on')
        {
            $this->turnOfMaintenance($slug);
        }
        if (isset(json_decode($this->profile->options)->enable_gtm) && json_decode($this->profile->options)->enable_gtm == 'on')
        {
            $this->updateGTM($slug);
        }
        if (isset(json_decode($this->profile->options)->enable_indexing) && json_decode($this->profile->options)->enable_indexing == 'on')
        {
            $this->updateIndexing($slug);
        }
    }

    protected function createNewVersion()
    {
        return Version::create(['profile_id'=>$this->profile->id]);
    }

    protected function dumpSourceDb($path)
    {
        $cmd = "mysqldump";
        $cmd .= $this->dbCredentials;
        $cmd .= "  {$this->db['name']}";
        $cmd .= "  > $path";

        $this->run($cmd);
    }

    protected function findAndReplaceInDbDump($path)
    {
        foreach ($this->profile->replacements()->where('type','Database')->get() as $replacement)
        {
            $file = file_get_contents($path);
            $file = str_replace($replacement->from, $replacement->to, $file);
            file_put_contents($path, $file);
        }
    }

    protected function createNewDb($name)
    {
        DB::connection()->statement("CREATE DATABASE $name");
    }

    protected function uploadNewDb($name)
    {
        $cmd = "mysql";
        $cmd .= $this->dbCredentials;
        $cmd .= "  $name";
        $cmd .= "  < {$this->temporaryPath}/$name.sql";
        $this->run($cmd);
    }

    protected function copyDirectory($name)
    {
        mkdir("{$this->destination}$name");
        $cmd = "cp -a {$this->source}* {$this->destination}$name";
        $this->run($cmd);
    }

    protected function findAndReplaceInDirectory($name)
    {
        foreach ($this->profile->replacements()->where('type','File')->get() as $replacement)
        {
            $this->replaceText($replacement->from, $replacement->to, $this->destination.$name.'/'.$replacement->path, $replacement->pattern ?? null);
        }
    }

    protected function updateConfig($name)
    {
        $file = file_get_contents("{$this->destination}$name/wp-config.php");
        $file = str_replace("define( 'DB_NAME', '$this->sourceDb' );", "define( 'DB_NAME', '$name' );", $file);
        file_put_contents("{$this->destination}$name/wp-config.php", $file);
    }

    protected function replaceText($from,$to,$path, $pattern = null)
    {
        $cmd = "find $path -type f";
        $cmd .= isset($pattern) ? " -name \"$pattern\"" : "";
        $cmd .= " -exec sed -i 's#$from#$to#g' {} +";
        $this->run($cmd);
    }

    protected function updateGTM($name)
    {
        $value = $this->selectDB("$name.wp_options",'option_name','gtm4wp-options');
        $value= unserialize($value[0]->option_value);
        $value['gtm-env-gtm-auth'] = "";
        $value['gtm-env-gtm-preview'] = "";
        $value = serialize($value);
        $this->updateDB("$name.wp_options","option_value",$value,"option_name",'gtm4wp-options');
    }

    protected function updateIndexing($name)
    {
        $this->updateDB("$name.wp_options","option_value","1","option_name",'blog_public');
    }

    protected function turnOfMaintenance($name)
    {
        $value = $this->selectDB($name.'.wp_options','option_name','maintenance_options');
        $value= unserialize($value[0]->option_value);

        array_shift($value);
        $value = array_merge($value,['state'=>0]);

        $value = serialize($value);

        $this->updateDB("$name.wp_options","option_value",$value,"option_name",'maintenance_options');

    }

    protected function selectDB($tableName,$columnName,$columnValue)
    {
        $query = "select * FROM {$tableName}";
        $query .= " WHERE $columnName ='$columnValue'";

        return DB::connection()->select($query);
    }

    protected function updateDB($tableName,$setColumn,$setColumnValue,$findColumn,$findColumnValue)
    {
        $query = "UPDATE $tableName";
        $query .= " SET $setColumn='{$setColumnValue}'";
        $query .= " WHERE $findColumn = '{$findColumnValue}'";

        DB::connection()->statement($query);
    }

    protected function run($cmd)
    {
        return (new Process($cmd))->setTimeout(120)->run();
    }
}
