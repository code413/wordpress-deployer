<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class ReplaceInDatabase implements ShouldQueue
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
        $columns = $this->get_columns($this->name);

        $update_sql = '';
        $where_sql = '';




        foreach ($this->profile->replacements()->where('type', 'Database')->get() as $replacement) {
            $data = DB::connection()->select( "SELECT * FROM {$this->name}");
            foreach ($data as $datum)
            {
                foreach ($columns as $column)
                {
                    $field = $column->Field;
                    if ($column->Key == 'PRI')
                        $where_sql = "{$column->Field} = " .  $datum->$field ;

                    $data =  $this->recursive_unserialize_replace( $replacement->from, $replacement->to, $datum->$field );

                    if ($data != $datum->$field)
                    {
                        $data = addslashes($data);
                        $update_sql = "{$field} = '{$data}'"  ;
                        $this->updateDB($this->name,$update_sql,$where_sql);
                    }
                }
            }
        }
    }


    protected function updateDB($tableName, $update_sql, $where_sql)
    {
        $query = "UPDATE $tableName";
        $query .= " SET ".  $update_sql;
        $query .= " WHERE " .$where_sql ;
        DB::connection()->statement($query);
    }

    protected function get_columns( $table ) {
        $primary_key = array();
        $columns = array( );
        // Get a list of columns in this table
        $fields =  DB::connection()->select( "DESCRIBE {$table}" );

        foreach ($fields as $field)
        {
            if ($field->Key != 'PRI')
                $columns[] = $field->Field;
        }

        return $fields;
    }

    private function recursive_unserialize_replace($from = '', $to = '', $data = '', $serialised = false)
    {

        if ( is_string( $data ) && ( $unserialized = @unserialize( $data ) ) !== false ) {
            $data = $this->recursive_unserialize_replace( $from, $to, $unserialized, true );
        }
        elseif ( is_array( $data ) ) {
            $_tmp = array( );
            foreach ( $data as $key => $value ) {
                $_tmp[ $key ] = $this->recursive_unserialize_replace( $from, $to, $value, false );
            }
            $data = $_tmp;
            unset( $_tmp );
        }
        // Submitted by Tina Matter
//        elseif ( is_object( $data ) ) {
//            // $data_class = get_class( $data );
//            $_tmp = $data; // new $data_class( );
//            $props = get_object_vars( $data );
//            foreach ( $props as $key => $value ) {
//                $_tmp->$key = $this->recursive_unserialize_replace( $from, $to, $value, false );
//            }
//            $data = $_tmp;
//            unset( $_tmp );
//        }
        else {
            if ( is_string( $data ) ) {
                $data = str_replace( $from, $to, $data );
//                $data = str_replace( "'", "%91", $data );
            }
        }
        if ( $serialised )
            return serialize( $data );


        return $data;
    }
}
