<?php

/* Includes: CUSTOM POSTS

@package	Greener Guest
@author		Digital Rockpool
@link		https://www.greenerguest.com
@copyright	Copyright (c) 2021, Digital Rockpool LTD
@license	GPL-2.0+ */

function custom_post_type() {

// Resources
	$labels = array(
		'name'                => _x( 'Resources', 'Post Type General Name', 'hello-elementor' ),
		'singular_name'       => _x( 'Resource', 'Post Type Singular Name', 'hello-elementor' ),
		'menu_name'           => __( 'Resources', 'hello-elementor' ),
		'parent_item_colon'   => __( 'Parent Resource', 'hello-elementor' ),
		'all_items'           => __( 'All Resources', 'hello-elementor' ),
		'view_item'           => __( 'View Resource', 'hello-elementor' ),
		'add_new_item'        => __( 'Add New Resource', 'hello-elementor' ),
		'add_new'             => __( 'Add New', 'hello-elementor' ),
		'edit_item'           => __( 'Edit Resource', 'hello-elementor' ),
		'update_item'         => __( 'Update Resource', 'hello-elementor' ),
		'search_items'        => __( 'Search Resources', 'hello-elementor' ),
		'not_found'           => __( 'Not Found', 'hello-elementor' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'hello-elementor' ),
	);

	$args = array(
		'label'               => __( 'resource', 'hello-elementor' ),
		'description'         => __( 'resource entry', 'hello-elementor' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail', 'excerpt'),
		'taxonomies'          => array( 'resource-tags', 'resource-categories' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'rewrite' => array( 'slug' => 'knowledge-hub/resources', 'with_front' => false ),
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-palmtree',
		'can_export'          => true,
		'has_archive'         => 'knowledge-hub/resources',
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);

	register_post_type( 'resources', $args );

}

add_action( 'init', 'custom_post_type', 0 );

function custom_taxonomies() {

  $labels = array(
    'name'              => _x( 'Resource Categories', 'Taxonomy General Name', 'hello-elementor' ),
    'singular_name'     => _x( 'Resource Category', 'Taxonomy Singular Name', 'hello-elementor' ),
    'search_items'      => __( 'Search Resource Categories', 'hello-elementor' ),
    'all_items'         => __( 'All Resource Categories', 'hello-elementor' ),
    'parent_item'       => __( 'Parent Resource Category', 'hello-elementor' ),
    'parent_item_colon' => __( 'Parent Resource Category:', 'hello-elementor' ),
    'edit_item'         => __( 'Edit Resource Category', 'hello-elementor' ),
    'update_item'       => __( 'Update Resource Category', 'hello-elementor' ),
    'add_new_item'      => __( 'Add New Resource Category', 'hello-elementor' ),
    'new_item_name'     => __( 'New Name', 'hello-elementor' ),
    'menu_name'         => __( 'Categories', 'hello-elementor' ),
    );

  $args = array(
    'hierarchical'      => true,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => 'resource-category' ),
  );

  register_taxonomy( 'resource-category', array( 'resources' ), $args );

  unset( $args );
  unset( $labels );

  $labels = array(
    'name'                       => _x( 'Resource Tags', 'Taxonomy General Name', 'hello-elementor' ),
    'singular_name'              => _x( 'Resource Tag', 'Taxonomy Singular Name', 'hello-elementor' ),
    'search_items'               => __( 'Search Resource Tags', 'hello-elementor' ),
    'popular_items'              => __( 'Popular Resource Tags', 'hello-elementor' ),
    'all_items'                  => __( 'All Resource Tags', 'hello-elementor' ),
    'parent_item'                => null,
    'parent_item_colon'          => null,
    'edit_item'                  => __( 'Edit Resource Tag', 'hello-elementor' ),
    'update_item'                => __( 'Update Resource Tag', 'hello-elementor' ),
    'add_new_item'               => __( 'Add New Resource Tag', 'hello-elementor' ),
    'new_item_name'              => __( 'New Name', 'hello-elementor' ),
    'separate_items_with_commas' => __( 'Separate tags with commas', 'hello-elementor' ),
    'add_or_remove_items'        => __( 'Add or remove tags', 'hello-elementor' ),
    'choose_from_most_used'      => __( 'Choose from the most used tags', 'hello-elementor' ),
    'not_found'                  => __( 'No tags found.', 'hello-elementor' ),
    'menu_name'                  => __( 'Tags', 'hello-elementor' ),
  );

  $args = array(
    'hierarchical'          => false,
    'labels'                => $labels,
    'show_ui'               => true,
    'show_admin_column'     => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var'             => true,
    'rewrite'               => array( 'slug' => 'resource-tag' ),
  );

    register_taxonomy( 'resource-tag', 'resources', $args );
}
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'custom_taxonomies', 0 );
