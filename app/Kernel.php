<?php

namespace App;

use Exception;
use App\Middleware\Auth;
use Viloveul\Kernel\Application;
use Viloveul\Database\Contracts\Manager as Database;

class Kernel extends Application
{
    public function initialize()
    {
        // load database manager
        $this->uses(function (Database $db) {
            $db->load();
        });
        // make middleware for authenticaion
        $this->middleware(Auth::class);
    }

    /**
     * @param int $status
     */
    public function terminate(int $status = 0): void
    {
        try {
            $this->uses(function (Database $db) {
                foreach ($db->all() as $connection) {
                    $connection->isConnected() and $connection->disconnect();
                }
            });
        } catch (Exception $e) {
            // do nothing
        }
        parent::terminate($status);
    }
}
