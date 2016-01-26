<?php
require_once (WP_PLUGIN_DIR .'/unity3/unity3.php');

class Unity3_Page_Content {
    public static $page, $dir, $url, $data; 
    protected $cur_post_id;
    
    function __construct() {
        add_action('init', array($this, 'init_pod'));

        if (is_admin()) {
            add_action('unity3_tabs', array(&$this, 'define_tabs'));
            add_action( 'admin_enqueue_scripts', array($this,'admin_stylesheet') );
        }
    }
    
    function admin_stylesheet() {
        wp_enqueue_style( 'unity3-page-content-style', plugins_url('style.css', __FILE__) );
    }
    
    function init_pod() { 
        if (is_callable('pods')) {
            $pod = pods('page_content', null, true);
            if (!$pod) {
                PodsAPI::$instance->save_pod(array(
                    'name' => 'page_content',
                    'label' => 'Page Content',
                    'label_singular' => 'Page Content',
                    'label_plural' => 'Page Content',
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
                    'exclude_from_search' => '1',
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
                            'label' => 'Page Title',
                            'name'  => 'page_title',
                            'description' => 'Specifies the title of the page',
                            'type' => 'text',
                            'default_value' => ''
                        ),
                        array(
                            'label' => 'Menu Title',
                            'name'  => 'menu_title',
                            'description' => 'Specifies the title of the menu',
                            'type' => 'text',
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
                            'label' => 'Icon URL',
                            'name'  => 'icon_url',
                            'description' => 'Sets the url for the menu icon',
                            'type' => 'text',
                        ),
                        array(
                            'label' => 'Menu Position',
                            'name'  => 'menu_position',
                            'description' => 'Sets the position for the menu',
                            'type' => 'number',
                        ),
                        array(
                            'label' => 'Content',
                            'name'  => 'page_content',
                            'description' => 'Stores the page content',
                            'type' => 'wysiwyg',
                            'wysiwyg_editor' => 'tinymce',
                            'wysiwyg_media_buttons' => '1',
                            'wysiwyg_oembed' => 0,
                            'wysiwyg_wptexturize' => '1',
                            'wysiwyg_convert_chars' => '1',
                            'wysiwyg_wpautop' => '1',
                            'wysiwyg_allow_shortcode' => '1'
                        )
                    )
                ));
            } else {// the pod does exists...
                add_action('admin_menu', array($this, 'init_menus'), 0); 
            }
        }
    }

    function define_tabs($tabs) {
        $tabs['page-content'] = array(
            'page_title' => 'Page Content',                  //page title
            'tab_title'  => 'Page Content',                  //tab title
            'function_print'  => array(&$this, 'print_page'),
            'position'   => 0                               //position
        );
        return $tabs;
    }
    
    function print_page() {
        $admin_url = admin_url('admin.php?page=unity3&tab=page-content');
        
        $default_fields = array('post_title', 'ui_visible', 'page_title', 'menu_title', 'menu_position', 'icon_url', 'capability');
        
        $ui = array(
            'pod' => 'page_content',
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
    
    function init_menus() {
        
//        add_menu_page(
//            'Page Title', 
//            'Menu Title', 
//            'manage_options',
//            'page-content', 
//            array($this, 'edit_page_content'));
//        add_submenu_page('page-content', 'Sub Page Title', 'Sub Menu Title', 'manage_options', 'sub-unity3-slug');
//        return;
        
        $page_content = pods('page_content');
        $page_content->find();

        while ( $page_content->fetch() ) {
            $row = $page_content;
            if ($row->field('ui_visible')) {
                $icon_url = $row->field('icon_url');
                //if (!$icon_url)
               // $icon_url = ClearBase::$url . 'includes/images/default-gallery-icon.png';

                add_menu_page(
                        $row->field('page_title'), 
                        $row->field('menu_title'), 
                        $row->field('capability'),
                        'page-content-' . $row->field('ID'), 
                        array($this, 'edit_page_content'),
                        $icon_url,
                        $row->field('menu_position'));
            }
        }
    } 

    function edit_page_content() {
        $pod = pods( 'page_content', $gallery_id = substr(strrchr($_GET['page'], "-"),1));
        echo '<h2>' . $pod->field('page_title') . '</h2>';
        
        echo $pod->form(array('page_content' => array('type' => 'wysiwyg', 'label' => ''))); 
    }
}
new Unity3_Page_Content();