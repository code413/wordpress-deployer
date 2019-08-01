<?php

namespace App\Http\Controllers;

use App\Jobs\copyDirectory;
use App\Jobs\createNewDb;
use App\Jobs\deleteCache;
use App\Jobs\deleteDbFile;
use App\Jobs\deleteDirectory;
use App\Jobs\deleteVersion;
use App\Jobs\dropDb;
use App\Jobs\dumpSourceDb;
use App\Jobs\findAndReplaceInDbDump;
use App\Jobs\findAndReplaceInDirectory;
use App\Jobs\ReplaceInDatabase;
use App\Jobs\turnOfMaintenance;
use App\Jobs\updateConfig;
use App\Jobs\updateGTM;
use App\Jobs\updateIndexing;
use App\Jobs\uploadNewDb;
use App\Models\Profile;
use App\Models\Version;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VersionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Profile $profile)
    {
        session()->flash('active', 'profile');
        $versions = Version::where('profile_id', $profile->id)->orderBy('id', 'DESC')->paginate(20);

        return view('versions.index', ['versions'=>$versions, 'profile'=>$profile]);
    }

    public function store(Profile $profile)
    {
        /*Check directory */
        if (!is_dir("{$profile->path_from}")) {
            return redirect()->action('VersionsController@index', ['profile'=>$profile])->with('error', 'Website directory not available');
        }

        /*Create directory if not exists*/
        if (!is_dir("{$profile->path_to}")) {
            mkdir("{$profile->path_to}");
        }

        /*Create new version*/
        $version = Version::create(['profile_id'=>$profile->id]);

        $db = [
            'host'     => $profile->db_host,
            'name'     => $this->dbName($profile),
            'user'     => $profile->db_user,
            'password' => $this->decrypt($profile->db_password),
        ];

        /*Database credentials*/
        $dbCredentials = " -h {$db['host']} --user={$db['user']} --password={$db['password']}";

        /*Create folder name*/
        $slug = "v{$version->id}";

        /*Make directory for database dump*/
        if (!is_dir($profile->path_temp)) {
            mkdir($profile->path_temp);
        }

        /*Dump db, change db, upload db*/
        try {
            dispatch(new dumpSourceDb("{$profile->path_temp}vtemp.sql", $db['name'], $dbCredentials));
            dispatch(new createNewDb('vtemp'));
            dispatch(new uploadNewDb('vtemp', $dbCredentials, $profile->path_temp));

            $tables = DB::connection()->select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'vtemp'");

            foreach ($tables as $table)
            {
                $table = 'vtemp.'.$table->table_name;
                $row_count = 0;
                if (!strripos($table,'cerber') && !strripos($table,'wp_icl_translation_status') && DB::table($table)->count() > 0)
                {
                    dispatch(new ReplaceInDatabase($table, $profile));
                }

            }
            dispatch(new dumpSourceDb("{$profile->path_temp}{$slug}.sql", 'vtemp', $dbCredentials));
            dispatch(new dropDb('temp'));
            dispatch(new deleteDbFile('temp',$profile));
            dispatch(new findAndReplaceInDbDump("{$profile->path_temp}{$slug}.sql", $profile));
            dispatch(new createNewDb($slug));
            dispatch(new uploadNewDb($slug, $dbCredentials, $profile->path_temp));


        } catch (\Exception $e) {
            $this->delete($version);
            return back()->with('error', $e->getMessage());
        }

        /*Copy directory delete cache and replace*/
        try {
            dispatch(new copyDirectory($slug, $profile));
            dispatch(new deleteCache( $profile->path_to.$slug));
            dispatch(new findAndReplaceInDirectory($profile, $slug));
        } catch (\Exception $e) {
            $this->delete($version);
            return back()->with('error', $e->getMessage());
        }

        /*Update wp-config file*/
        try {
            dispatch(new updateConfig($slug, $profile));
        } catch (\Exception $e) {
            $this->delete($version);
            return back()->with('error', $e->getMessage());
        }

        /*Search engine indexing*/
        if(isset(json_decode($profile->options)->enable_indexing))
            dispatch(new updateIndexing($slug,json_decode($profile->options)->enable_indexing));
        /*Update maintenance mode*/
        if(isset(json_decode($profile->options)->disable_maintenance))
            dispatch(new turnOfMaintenance($slug,json_decode($profile->options)->disable_maintenance));
        /*Update GTM*/
        if(isset(json_decode($profile->options)->enable_gtm))
            dispatch(new updateGTM($slug,$profile));

        return back()->with('message', 'Version created successfully ');
    }

    public function destroy(Version $version)
    {
        $this->delete($version);

        return back()->with('message', 'Version deleted successfully.');
    }

    protected function delete(Version $version)
    {
        try {
            dispatch(new deleteDbFile($version->id, $version->profile));
            dispatch(new dropDb($version->id));
            dispatch(new deleteDirectory($version->id));
            dispatch(new deleteVersion($version->id));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function decrypt($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC')
    {
        // hash
        $key = hash('sha256', $key);
        // create iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);
        // decode
        $string = base64_decode($string);
        // decrypt
        return openssl_decrypt($string, $method, $key, 0, $iv);
    }

    protected function dbName(Profile $profile){
        $file = file_get_contents($profile->path_from.'wp-config.php');
        $file = (explode("\n", $file));

        foreach ($file as $i=> $item) {
            if (Str::contains($item, 'DB_NAME')) {
                $item = explode(',', $item);
                $item = trim($item[1], ' ');

                return substr($item, 1, strrpos($item, "'") - 1) ?? substr($item, 1, strrpos($item, '"') - 1);
            }
        }
    }
}
