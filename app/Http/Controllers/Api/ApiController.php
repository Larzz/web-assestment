<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use DB;

class ApiController extends Controller
{
    //
    protected $request;
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Handle data connection submission.
     * @param string $host The database host.
     * @param string $username The database username.
     * @param string $database The database name.
     * @param string $password The database password.
     */
    public function postConnection() {

        // sets the connection with the credentials that we post
        Config::set("database.connections.mysql_external", [
            'driver' => 'mysql',
            "host" => $this->request->host,
            "database" => $this->request->database,
            "username" => $this->request->username,
            "password" => $this->request->password,
            "port" => '3306',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);

        try {

            // check if the credentials are correct and if its connected
            if (DB::connection('mysql_external')->getPdo()) {

                $tables = DB::connection('mysql_external')->getDoctrineSchemaManager()->listTableNames();

                return response()->json([
                        'status' => true,
                        'tables' => (object) $tables,
                        'message' => 'Connected successfully to the database.'
                ]);

            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to connect to the database.'
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }

    }


    /**
     * Handles a database query.
     * @param string $query The database query to be handled.
     */
    public function postQuery() {

        try {

            Config::set("database.connections.mysql_external", [
                'driver' => 'mysql',
                "host" => $this->request->connection['host'],
                "database" => $this->request->connection['database'],
                "username" => $this->request->connection['username'],
                "password" => $this->request->connection['password'],
                "port" => '3306',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'strict'    => false,
            ]);

            DB::setDefaultConnection('mysql_external');

            if (DB::connection('mysql_external')->getPdo()) {
                $query = $this->request->input('query');
                if (str_starts_with(strtoupper($query), 'SELECT')) {
                    $results = DB::select($query);
    
                    return response()->json([
                        'status' => true,
                        'result' => $results,
                    ]);
                } else {
                    // For non-SELECT queries (e.g., INSERT, UPDATE, DELETE)
                    $result = DB::statement($query);
    
                    return response()->json([
                        'status' => true,
                        'result' => $result,
                    ]);
                }
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }

    }


    /**
     * Get Column Listing
     * @param string $query The database query to be handled.
     */
    public function getColumnListing() {

        try {

            if (isset($this->request->tableName)) {
                $tableName = trim($this->request->tableName);
            }

            Config::set("database.connections.mysql_external", [
                'driver' => 'mysql',
                "host" => $this->request->connection['host'],
                "database" => $this->request->connection['database'],
                "username" => $this->request->connection['username'],
                "password" => $this->request->connection['password'],
                "port" => '3306',
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'strict'    => false,
            ]);

            DB::setDefaultConnection('mysql_external');
            
            if (DB::connection('mysql_external')->getPdo()) {

                $columns = DB::getSchemaBuilder()->getColumnListing($tableName);

                if ($columns) {
                    return response()->json([
                        'status' => true,
                        'columns' => (object) $columns
                    ]);
                }

            }

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, Please try again'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => true,
                'message' => $th->getMessage()
            ]);
        }

    }

}
