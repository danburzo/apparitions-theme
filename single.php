<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {

	if ($post->post_type === 'member') {
		// get the link to /organizatori or /organizers depending on the language
		$parent_page_id = apply_filters('wpml_object_id', 76, 'page');
		$context['parent'] = new TimberPost($parent_page_id);
	}

	Timber::render( array( 
		'single-' . $post->ID . '.twig', 
		'single-' . $post->post_type . '.twig', 
		'single.twig' 
	), $context );
}
