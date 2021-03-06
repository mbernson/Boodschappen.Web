<?php namespace Boodschappen\Http\Controllers\Api;

use Illuminate\Database\Connection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use DispatchesJobs;

    protected $db;

    public function __construct(Connection $connection) {
        $this->db = $connection;
    }
}