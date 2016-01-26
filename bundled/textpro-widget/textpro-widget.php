<?php
/*
Plugin Name: Shortcode Widget
Plugin URI: http://wordpress.org/extend/plugins/textpro-widget/
Author: Richard Blythe
Author URI: http://unity3software.com/richard-blythe
Version: 1.0
Text Domain: textpro-widget
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TextPro_Widget extends WP_Widget {

  public function __construct() {
    $widget_ops = array('classname' => 'textpro_widget', 'description' => __('TextPro or HTML or Plain Text.', 'textpro-widget'));
    $control_ops = array('width' => 400, 'height' => 350);
    parent::__construct('textpro-widget', __('TextPro Widget', 'textpro-widget'), $widget_ops, $control_ops);
  }

  /**
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ) {
    /** This filter is documented in wp-includes/default-widgets.php */
    $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
    
    /**
     * Filter the content of the Text widget.
     *
     * @param string    $widget_text The widget content.
     * @param WP_Widget $instance    WP_Widget instance.
     */
    $text = do_shortcode(apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance ));
    echo $args['before_widget'];
    if ( ! empty( $title ) ) {
      echo $args['before_title'] . $title . $args['after_title'];
    } ?>
      <div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
    <?php
    echo $args['after_widget'];
  }

  /**
   * @param array $new_instance
   * @param array $old_instance
   * @return array
   */
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    if ( current_user_can('unfiltered_html') )
      $instance['text'] =  $new_instance['text'];
    else
      $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
    $instance['filter'] = ! empty( $new_instance['filter'] );
    return $instance;
  }

  /**
   * @param array $instance
   */
  public function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
    $title = strip_tags($instance['title']);
    $text = esc_textarea($instance['text']);
?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'textpro-widget'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

    <p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:' ); ?></label>
    <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></p>

    <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', 'textpro-widget'); ?></label></p>
<?php
  }
}

//Register with wordpress

function textpro_widget_init(){
  register_widget('TextPro_Widget');
}
add_action('widgets_init','textpro_widget_init');

function textpro_widget_load_text_domain(){
  load_plugin_textdomain( 'textpro-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded','textpro_widget_load_text_domain');

function textpro_widget_test_output($args){
  return __( "It works" , 'textpro-widget' );
}
add_shortcode('textpro_widget_test', 'textpro_widget_test_output');
