<?php

namespace Boodschappen\Domain;

use Boodschappen\User;

class ShoppingList
{
    /** @var int */
    public $id;

    /** @var User */
    public $user;

    /** @var string */
    public $title;

    /** @var int */
    public $count = 0;

    /** @var array[Product] */
    public $products = [];
}
