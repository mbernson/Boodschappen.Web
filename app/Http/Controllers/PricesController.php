<?php

namespace Boodschappen\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use Boodschappen\Http\Requests;
use Boodschappen\Http\Controllers\Controller;
use Carbon\Carbon;

use DB;

class PricesController extends Controller
{
    public function __construct() {
        $currencyFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::CURRENCY);
        view()->share(compact('currencyFormatter'));
    }

    public function priceChanges(Request $request) {
        $days = intval($request->get('since', 2));
        $since = Carbon::parse("$days days ago");

        $changes = DB::select("
            select id, title, prices, last_updated,
            (prices[1] - prices[2]) as difference,
            round((prices[1] - prices[2]) / prices[2], 2) as change
            from price_changes
            where last_updated >= ?
            order by change asc
            limit 500;
        ", [$since]);
        $changes = new Collection($changes);
        $changes->map(function($item) {
            $item->prices = priceChanges($item->prices);
            return $item;
        });

        return view('price_changes', compact('changes', 'days'));
    }
}
