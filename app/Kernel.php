<?php

namespace App;

use Exception;
use App\Database;
use App\Middleware\Auth;
use Viloveul\Kernel\Application;

class Kernel extends Application
{
    public function initialize()
    {
        $this->container->get(Database::class)->load();
        $this->middleware([
            $this->container->make(Auth::class),
        ]);
    }

    /**
     * @param int $status
     */
    public function terminate(int $status = 0): void
    {
        try {
            $db = $this->container->get(Database::class);
            $db->getConnection('default')->disconnect();
        } catch (Exception $e) {
            // do nothing
        }
        parent::terminate($status);
    }
}
