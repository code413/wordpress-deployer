<?php

namespace App\Http\Controllers;

use App\Jobs\copyDirectory;
use App\Jobs\createNewDb;
use App\Jobs\deleteDbFile;
use App\Jobs\deleteDirectory;
use App\Jobs\deleteVersion;
use App\Jobs\dropDb;
use App\Jobs\dumpSourceDb;
use App\Jobs\findAndReplaceInDbDump;
use App\Jobs\findAndReplaceInDirectory;
use App\Jobs\turnOfMaintenance;
use App\Jobs\updateConfig;
use App\Jobs\updateGTM;
use App\Jobs\updateIndexing;
use App\Jobs\uploadNewDb;
use App\Models\Profile;
use App\Models\Version;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use test\Mockery\Fixtures\MethodWithVoidReturnType;


class VersionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Profile $profile)
    {
        session()->flash('active','profile');
        $versions = Version::where('profile_id',$profile->id)->orderBy('id', 'DESC')->paginate(20);
        return view('versions.index',['versions'=>$versions,'profile'=>$profile]);
    }

    public function store(Profile $profile)
    {
        /*Check directory */
        if (!is_dir("{$profile->path_from}"))
        {
            return redirect()->action('VersionsController@index',['profile'=>$profile])->with('error','Deployment path from not available');
        }

        /*Create directory if not exists*/
        if (!is_dir("{$profile->path_to}"))
        {
            mkdir("{$profile->path_to}");
        }

        /*Create new version*/
        $version = Version::create(['profile_id'=>$profile->id]);

        $db = [
            'host' => $profile->db_host,
            'name' => $profile->db_name,
            'user' => $profile->db_user,
            'password' => $this->decrypt($profile->db_password),
        ];

        /*Database credentials*/
        $dbCredentials = " -h {$db['host']} --user={$db['user']} --password={$db['password']}";

        /*Create folder name*/
        $slug = "v{$version->id}";

        /*Make directory for database dump*/
        if (!is_dir($profile->path_temp))
        {
            mkdir($profile->path_temp);
        }


        /*Dump db, change db, upload db*/
        try{
            dispatch(new dumpSourceDb("{$profile->path_temp}{$slug}.sql",$db, $dbCredentials));
            dispatch(new findAndReplaceInDbDump("{$profile->path_temp}{$slug}.sql",$profile));
            dispatch(new createNewDb($slug));
            dispatch(new uploadNewDb($slug,$dbCredentials,$profile->path_temp));
        }
        catch (\Exception $e)
        {
            $this->delete($version);
            return back()->with('error',  $e->getMessage());
        }

        /*Copy directory and replace*/
        try{
            dispatch(new copyDirectory($slug,$profile));
            dispatch(new findAndReplaceInDirectory($profile,$slug));
        }
        catch (\Exception $e)
        {
            $this->delete($version);
            return back()->with('error',$e->getMessage());
        }


        /*Update wp-config file*/
        try{
            dispatch(new updateConfig($slug,$profile));
        }
        catch (\Exception $e)
        {
            $this->delete($version);
            return back()->with('error',$e->getMessage());
        }

        if (isset(json_decode($profile->options)->disable_maintenance) && json_decode($profile->options)->disable_maintenance == 'on')
        {
            $this->turnOfMaintenance($slug,$version);
        }
        if (isset(json_decode($profile->options)->enable_gtm) && json_decode($profile->options)->enable_gtm == 'on')
        {
          $this->updateGTM($slug,$version);
        }

        if (isset(json_decode($profile->options)->enable_indexing) && json_decode($profile->options)->enable_indexing == 'on')
        {
            $this->updateIndexing($slug,$version);
        }

        return back()->with('message', 'Version created successfully ');
    }

    public function destroy(Version $version)
    {
        $this->delete($version);
        return back()->with('message', 'Version deleted successfully.');
    }

    protected function delete(Version $version){
        try{
            dispatch(new deleteDbFile($version->id));
            dispatch(new dropDb($version->id));
            dispatch(new deleteDirectory($version->id));
            dispatch(new deleteVersion($version->id));
        }
        catch (\Exception $e)
        {
            return back()->with('error',$e->getMessage());
        }
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

    protected function turnOfMaintenance($slug,Version $version){
        try{
            dispatch(new turnOfMaintenance($slug));
        }
        catch (\Exception $e)
        {
            $this->delete($version);
            return back()->with('error', 'Turn of maintenance mode failed.'.$e->getMessage());
        }
    }

    protected function updateGTM($slug,Version $version)
    {
        try{
            dispatch(new updateGTM($slug));
        }
        catch (\Exception $e)
        {
            $this->delete($version);
            return back()->with('error', 'Update GTM failed.'.$e->getMessage());
        }
    }

    protected function updateIndexing($slug,Version $version){

        try{
            dispatch(new updateIndexing($slug));
        }
        catch (\Exception $e)
        {
            $this->delete($version);
            return back()->with('error', 'Update Indexing Failed.'.$e->getMessage());
        }
    }
}
