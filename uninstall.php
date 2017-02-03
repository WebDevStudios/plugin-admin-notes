<?php
$args = array(
	'post_type'      => 'wdspp_plugin_police',
	'fields'         => 'id',
	'cache_results'  => false,
	'posts_per_page' => - 1,
);

$posts = new WP_Query( $args );

foreach ( $posts->results as $post ) {
	wp_delete_post( $post, true );
}

delete_option( 'wds_plugin_lock_updates' );
delete_option( 'wds_plugin_updates_auto_updates' );
