<?php

function recursive_list($items, $max_depth = 2, $depth = 9999) {
    if($depth > $max_depth) return;
    echo '<ul>';

    foreach($items as $item) {
        echo "<li class=\"depth-$depth\"><a href=\"/categories/$item->id\"> $item->title</a>";
        if(!$item->children->isEmpty()) {
            recursive_list($item->children, $max_depth, $depth+1);
        }
        echo '</li>';
    }

    echo '</ul>';
}

recursive_list($products, 20, 1);
