<?php

function Load_DragSortPosts() {
  class DragSortPosts {
    public function __construct() {
        if ((defined('DOING_AJAX') && DOING_AJAX)) { 
          add_action( 'wp_ajax_unity3_drag_sort_posts', array(&$this, 'update_sorting'));
        } else {
          add_action('current_screen', array(&$this, 'current_screen'));
        }
    }

    public function current_screen() {
        global $current_screen;
        if ('edit' == $current_screen->base && in_array($current_screen->post_type, apply_filters('unity3_dragsortposts', array()))) {
          add_action( 'admin_enqueue_scripts', array(&$this,'admin_enqueue') );
          add_filter( "manage_{$current_screen->post_type}_posts_columns" , array(&$this,'add_drag_column') );
          add_filter('pre_get_posts', array(&$this, 'set_post_order') );
        }
    }

    public function set_post_order($wp_query) {
        $wp_query->set( 'orderby', 'menu_order' );
        $wp_query->set( 'order', 'ASC' );
    }

    public function admin_enqueue() 
    {
      wp_enqueue_script('jquery-ui-sortable');
      wp_enqueue_script('jquery-ui-touch-punch', plugins_url('jquery.ui.touch-punch.min.js', __FILE__ ), array('jquery-ui-sortable'), '0.2.3', true);
      wp_enqueue_script('dragsortposts-js', plugins_url('dragsortposts.js', __FILE__ ), array( 'jquery-ui-sortable' ),'1', true);
      wp_enqueue_style( 'dragsortposts-css', plugins_url('dragsortposts.css', __FILE__ ));
    }

    /* Add custom column to post list */
    public function add_drag_column( $columns ) {
        return array( 'dragsort' => '' ) + $columns;
    }

    public function update_sorting() {
      global $wpdb;
      $posts = isset($_REQUEST['posts']) ? $_REQUEST['posts'] : array();
      foreach ( $posts as $post_id => $menu_order ) {
          wp_update_post( array( 'ID' => $post_id, 'menu_order' =>  $menu_order) );
      }

      wp_send_json_success();
    }
  } 
  new DragSortPosts();
}

add_action('admin_init', 'Load_DragSortPosts');