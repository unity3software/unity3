<?php
function Load_Unity3Defaults() {
  class Unity3Defaults {
    public function __construct() {
        add_action( 'admin_init', array(&$this, 'admin_init') );

        //Remove the URL section on comments
        if (apply_filters('unity3_remove_genesis_comment_url', true)) {
          add_filter( 'genesis_comment_form_args', array(&$this, 'url_filtered') );
          add_filter( 'comment_form_default_fields', array(&$this, 'url_filtered') );
        }

        //* Change the footer text
        add_filter('genesis_footer_creds_text', array(&$this, 'genesis_footer_creds') );
    }

    public function admin_init() {
      add_filter( 'mce_buttons_2', array(&$this, 'mce_buttons_2') );
      add_filter( 'tiny_mce_before_init', array(&$this, 'mce_before_init'), 10, 2 );

      add_editor_style( plugins_url('/css/genesis-editor-columns.css', __FILE__) );
    }

    /**
    * Show the style dropdown on the second row of the editor toolbar.
    *
    * @param array $buttons Exising buttons
    * @return array Amended buttons
    */
    function mce_buttons_2( $buttons ) {

      // Check if style select has not already been added
      if ( isset( $buttons['styleselect'] ) )
      return;

      // Appears not, so add it ourselves.
      array_unshift( $buttons, 'styleselect' );
      return $buttons;

    }

    /**
    * Add column entries to the style dropdown.
    *
    * 'unity3-defaults' should be replaced with your theme or plugin text domain for
    * translations.
    *
    * @param array $settings Existing settings for all toolbar items
    * @return array Amended settings
    */
    function mce_before_init( $settings ) {

    $style_formats = array(
        array(
        'title' => __( 'First Half', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-half first'
        ),
        array(
        'title' => __( 'Half', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-half'
        ),
        array(
        'title' => __( 'First Third', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-third first'
        ),
        array(
        'title' => __( 'Third', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-third'
        ),
        array(
        'title' => __( 'First Quarter', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-fourth first'
        ),
        array(
        'title' => __( 'Quarter', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-fourth'
        ),
        array(
        'title' => __( 'First Fifth', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-fifth first'
        ),
        array(
        'title' => __( 'Fifth', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-fifth'
        ),
        array(
        'title' => __( 'First Sixth', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-sixth first'
        ),
        array(
        'title' => __( 'Sixth', 'unity3-defaults' ),
        'block' => 'div',
        'classes' => 'one-sixth'
        )
      );

      // Check if there are some styles already
      if ( $settings['style_formats'] )
        $settings['style_formats'] = array_merge( $settings['style_formats'], json_encode( $style_formats ) );
      else
        $settings['style_formats'] = json_encode( $style_formats );

      return $settings;

    }

    public function url_filtered( $fields ) {
      if ( isset( $fields['url'] ) )
      unset( $fields['url'] );
       
      if ( isset( $fields['fields']['url'] ) )
      unset( $fields['fields']['url'] );
       
      return $fields;
    }

    public function genesis_footer_creds( $creds ) {
      return '[footer_copyright] &middot; Site design by <a href="mailto:unity3software@gmail.com" title="Unity 3 Software">Unity 3 Software</a>';
    }

  } 
  new Unity3Defaults();
}

add_action('init', 'Load_Unity3Defaults');