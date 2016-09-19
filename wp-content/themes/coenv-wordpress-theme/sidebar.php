<?php  
/**
 * The sidebar template
 *
 * Serves up sidebar widgets for individual top level pages
 */
$ancestor_id = coenv_get_ancestor('ID');

if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( $ancestor_id )): endif;