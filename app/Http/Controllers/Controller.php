<?php

namespace Boodschappen\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use NumberFormatter;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $currencyFormatter;

    public function __construct()
    {
        $this->currencyFormatter = new NumberFormatter('nl_NL', NumberFormatter::CURRENCY);
        view()->share('currencyFormatter', $this->currencyFormatter);
    }

}
