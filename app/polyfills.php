<?php
/**
 * A file of simple helper functions, that would be really handy
 * if they were actual language constructs
 */

/**
 * Make sure this is item is an array.
 * If it is not, wrap it in an array.
 * @param $items
 * @return array
 */
function ensure_array($items) {
    if (!is_array($items)) {
        $items = [$items];
    }
    return $items;
}