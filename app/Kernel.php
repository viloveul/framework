<?php

namespace App;

use Exception;
use App\Middleware\Auth;
use Viloveul\Kernel\Application;
use Viloveul\Log\Contracts\Logger;
use Viloveul\Database\Contracts\Manager as Database;

class Kernel extends Application
{
    public function initialize()
    {
        $this->uses(function (Database $db, Logger $log) {
            set_error_handler([$log, 'handleError']);
            set_exception_handler([$log, 'handleException']);
            $db->load();
        });
        $this->middleware(Auth::class);
    }

    /**
     * @param int $status
     */
    public function terminate(int $status = 0): void
    {
        try {
            $this->uses(function (Database $db, Logger $log) {
                foreach ($db->all() as $connection) {
                    $connection->isConnected() and $connection->disconnect();
                }
                $log->process();
            });
        } catch (Exception $e) {
            // do nothing
        }
        parent::terminate($status);
    }
}
