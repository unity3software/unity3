<?php
require_once (WP_PLUGIN_DIR .'/unity3/unity3.php');

class Unity3_Page_Menus {
    public static $page, $dir, $url, $data; 
    protected $cur_post_id;
    
    function __construct() {
        
        Unity3_Page_Menus::$dir = Unity3::$dir . 'bundled/dynamicphp/';
        Unity3_Page_Menus::$url = Unity3::$url . 'bundled/dynamicphp/';       
        require_once (Unity3_Page_Menus::$dir . 'includes/widget.php');        

        add_action('init', array($this, 'init_pod'));

        if (is_admin())
            add_action('unity3_tabs', array(&$this, 'define_tabs'));
    }
    
    function init_pod() { 
        if (is_callable('pods')) {
            $pod = pods('unity3_page_menus', null, true);
            if (!$pod) {
                PodsAPI::$instance->save_pod(array(
                    'name' => 'unity3_page_menus',
                    'label' => 'Page Menu',
                    'label_singular' => 'Page Menu',
                    'label_plural' => 'Page Menus',
                    'type' => 'post_type',
                    'storage' => 'meta',
                    'rewrite' => '0',
                    'supports_title' => '1',
                    'supports_editor' => '0',
                    'supports_author' => '0',
                    'supports_thumbnail' => '0',
                    'supports_excerpt' => '0',
                    'supports_trackbacks' => '0',
                    'supports_custom_fields' => '0',
                    'supports_comments' => '0',
                    'supports_revisions' => '0',
                    'supports_page_attributes' => '0',
                    'supports_post_formats' => '0',
                    'show_ui' => '0',
                    'show_in_menu' => '0',
                    'show_in_nav_menus' => '0',
                    'show_in_admin_bar' => '0',
                    'public' => '0',
                    'publicly_queryable' => '0',
                    'exclude_from_search' => '0',
                    'hierarchical' => '0',
                    'has_archive' => '0',
                    'can_export' => '0',
                    'default_status' => 'publish',
                    'fields' => array(
                        array(
                            'label' => 'UI Visible',
                            'name'  => 'ui_visible',
                            'description' => 'Determines if the menu will be visible in the admin UI',
                            'type' => 'boolean',
                            'default_value' => '1'
                        ),
                        array(
                            'label' => 'Page ID',
                            'name'  => 'page_id',
                            'description' => 'Specifies the id for the page',
                            'type' => 'number',
                            'default_value' => ''
                        ),
                        array(
                            'label' => 'Capability',
                            'name'  => 'capability',
                            'description' => 'Sets the security level for the page',
                            'type' => 'text',
                            'default_value' => 'manage_options'
                        ),
                        array(
                            'label' => 'Menu Position',
                            'name'  => 'menu_position',
                            'description' => 'Sets the position for the menu',
                            'type' => 'number',
                            'default_value' => ''
                        )
                    )
                ));
            } else {// the pod does exists...
                add_action('admin_menu', array($this, 'init_menus')); 
            }
        }
    }

    function define_tabs($tabs) {
        $tabs['page-menus'] = array(
            'page_title' => 'Page Menus',                  //page title
            'tab_title'  => 'Page Menus',                  //tab title
            'function_init'   => array(&$this, 'init_page'),     //this is handled by an inherited class
            'function_print'  => array(&$this, 'print_page'),
            'position'   => 0                               //position
        );
        return $tabs;
    }
    
    function init_menus() {
        //add_menu_page( 'My Page Title', 'My Menu Title', 'manage_options', 'unique_menu_slug', array($this, 'temp_render'), $icon_url, 4 );
        //add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug)
        $page_menus = pods('unity3_page_menus');
        $page_menus->find();
        $base_url = get_bloginfo( "url" );
        
        
        while ( $page_menus->fetch() ) {
            $m = $page_menus;
            if ($m->field('ui_visible')) {
                $post = get_post((int)$m->field('page_id'));
                
                $menu_title = $m->field('menu_title');
                $capability = $m->field('capability');
                //$icon_url = $m->field('icon_url');
                //$position = $m->field('menu_position');
                //$function = array($this, 'dummy_render');
                
                //if (!$icon_url)
               // $icon_url = ClearBase::$url . 'includes/images/default-gallery-icon.png';

                add_menu_page($post->post_title, $post->post_title, $capability, "post.php?post={$post->ID}&action=edit");
            }
        }
    } 
    
    function init_page() {
        
    }
    
    function print_page() {
        $admin_url = admin_url('admin.php?page=unity3&tab=page-menus');
        $default_fields = array('post_title', 'ui_visible', 'page_id', 'capability', 'menu_position');
        
        $ui = array(
            'pod' => 'unity3_page_menus',
            'title' => 'My Pod',
            //field definitions
            'fields'    => array(
                'add'       => $default_fields,
                'edit'      => $default_fields,
                //What columns to show on manage screen - old 'columns' parameter
                'manage'    => array('post_title' => 'Title', 'ui_visible' => 'UI Visible')
            ),
            'action_links' => array(
                'manage' => $admin_url.'&action=manage',
                'add' => $admin_url.'&action=add',
                'edit' => $admin_url.'&action=edit&id={@ID}',
                'delete' => $admin_url.'&action=delete&id={@ID}'
            ),
            'actions_disabled' => array('view','duplicate', 'export'),
            'action_after' => array(
                'edit' => $admin_url
            )
        );

        pods_ui( $ui );
    }
}
new Unity3_Page_Menus();