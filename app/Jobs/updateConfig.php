<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class updateConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $name;
    protected $profile;
    protected $db;
    protected $dbCredentials;


    public function __construct($name,Profile $profile)
    {
        $this->name = $name;
        $this->profile = $profile;

        $this->db = [
            'host' => $profile->db_host,
            'name' => $profile->db_name,
            'user' => $profile->db_user,
            'password' => $this->decrypt($profile->db_password),
        ];

        /*Database credentials*/
        $this->dbCredentials = " -h {$this->db['host']} --user={$this->db['user']} --password={$this->db['password']}";
    }


    public function handle()
    {
        $file = file_get_contents("{$this->profile->path_to}$this->name/wp-config.php");
        $file = (explode("\n",$file));

        foreach ($file as $i=>$item)
        {

            if (Str::contains($item,'DB_USER')) {
                $item = explode(',', $item);
                $item = trim($item[1], ' ');
                $item = substr($item, 1, strrpos($item, "'") - 1);

                $cmd = "mysql ";
                $cmd .= $this->dbCredentials;
                $cmd .= " -e \"GRANT ALL PRIVILEGES ON ";
                $cmd .= $this->name . ".* TO '{$item}'@'%'\"";


                dd($cmd);
                (new Process($cmd))->setTimeout(120)->run();

            }

            if (Str::contains($item,'DB_NAME'))
            {
                $file[$i] = "define( 'DB_NAME' , '" . $this->name ."' );";
            }
        }

        $file = implode("\n", $file);
        file_put_contents("{$this->profile->path_to}$this->name/wp-config.php", $file);
    }

    protected function decrypt($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC') {
        // hash
        $key = hash('sha256', $key);
        // create iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);
        // decode
        $string = base64_decode($string);
        // decrypt
        return openssl_decrypt($string, $method, $key, 0, $iv);
    }
}
