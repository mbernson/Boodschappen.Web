<?php namespace Boodschappen\Http\ViewComposers;

use Boodschappen\Database\ShoppingList;
use Illuminate\Contracts\View\View;
use Auth;

class ShoppingListsComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if(Auth::check()) {
            $lists = ShoppingList::where('user_id', Auth::user()->id)
                ->get();
            $view->with('lists', $lists);
        } else {
            $view->with('lists', []);
        }
    }
}
