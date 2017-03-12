<?php
/* Template Name: Redirect To First Child */
$child_pages = get_pages( "child_of=" . $post->ID . "&sort_column=menu_order" );
if ($child_pages) {
    // get id of first child page
    $firstchild = $child_pages[0];
    wp_redirect( get_permalink( $firstchild->ID ) );
}
?>