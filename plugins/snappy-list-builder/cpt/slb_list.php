<?php

// create custom lists post type
function slb_register_slb_list() {

	/**
	 * Post Type: Lists.
	 */

	$labels = [
		"name" => __( "Lists"),
		"singular_name" => __( "List"),
	];

	$args = [
		"label" => __( "Lists"),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "slb_list", "with_front" => false ],
		"query_var" => true,
		"supports" => [ "title" ],
		"show_in_graphql" => false,
	];

	register_post_type( "slb_list", $args );
}

add_action( 'init', 'slb_register_slb_list' );