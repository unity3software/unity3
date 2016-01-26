<?php
/**
 * Featured URL widget class.
 *
 * @since 0.1.8
 *
 * @package Genesis\Widgets
 */
class Unity3_Featured_Url extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.1.8
	 */
	function __construct() {

		$this->defaults = array(
			'title'           => '',
			'url'         => '',
			'show_image'      => 0,
			'image_alignment' => '',
			'image_size'      => '',
			'show_title'      => 0,
			'show_content'    => 0,
			'content_limit'   => '',
			'more_text'       => '',
		);

		$widget_ops = array(
			'classname'   => 'featured-content unity3-featured-url',
			'description' => __( 'Displays a featured url with optional thumbnail', 'unity3_featured_url' ),
		);

		$control_ops = array(
			'id_base' => 'unity3-featured-url',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'unity3-featured-url', __( 'Unity3 - Featured Url', 'unity3_featured_url' ), $widget_ops, $control_ops );

	}

	/**
	 * Echo the widget content.
	 *
	 * @since 0.1.8
	 *
	 * @global WP_Query $wp_query Query object.
	 * @global integer  $more
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

                if (!empty($instance['url']))
                   $instance['url'] = do_shortcode($instance['url']);
                if (!empty($instance['image_url']))
                   $instance['image_url'] = do_shortcode($instance['image_url']);
                if (!empty($instance['description']))
                   $instance['description'] = do_shortcode($instance['description']);
                
		echo $args['before_widget'];

                
		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];


                genesis_markup( array(
                        'html5'   => '<article %s>',
                        'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
                        'context' => 'entry',
                ) );

                if (!empty($instance['url']))
                    echo '<a href="'. $instance['url'] .'" title="'. $instance['title'] .'" class="'. esc_attr($instance['image_alignment']) .'">'.
                            '<img src="'. $instance['image_url'] .'" class="entry-image" itemprop="image" />' .
                        '</a>';


                if ( ! empty( $instance['description'] ) ) {

                        echo genesis_html5() ? '<div class="entry-content">' : '';
                        echo esc_html( $instance['description'] );
                        echo genesis_html5() ? '</div>' : '';

                }

                genesis_markup( array(
                        'html5' => '</article>',
                        'xhtml' => '</div>',
                ) );

		echo $args['after_widget'];

	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 0.1.8
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$new_instance['title']     = strip_tags( $new_instance['title'] );
		$new_instance['url'] = strip_tags( $new_instance['url'] );
                $new_instance['image_url'] = strip_tags( $new_instance['image_url'] );
                $new_instance['description'] = strip_tags( $new_instance['description'] );
		return $new_instance;

	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 0.1.8
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Url', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo esc_attr( $instance['url'] ); ?>" class="widefat" />
		</p>
                
                <p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'genesis' ); ?>:</label>
                        <textarea rows="10" cols="20" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" class="widefat"><?php echo esc_attr( $instance['description'] ); ?></textarea>
		</p>

                <p>
			<label for="<?php echo $this->get_field_id( 'image_url' ); ?>"><?php _e( 'Image Url', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'image_url' ); ?>" name="<?php echo $this->get_field_name( 'image_url' ); ?>" value="<?php echo esc_attr( $instance['image_url'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'image_alignment' ); ?>"><?php _e( 'Image Alignment', 'genesis' ); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'image_alignment' ); ?>">
				<option value="alignnone">- <?php _e( 'None', 'genesis' ); ?> -</option>
				<option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php _e( 'Left', 'genesis' ); ?></option>
				<option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php _e( 'Right', 'genesis' ); ?></option>
				<option value="aligncenter" <?php selected( 'aligncenter', $instance['image_alignment'] ); ?>><?php _e( 'Center', 'genesis' ); ?></option>
			</select>
		</p>
		<?php

	}

}

add_action( 'widgets_init', 'unity3_featured_url_widget_register');
    
function unity3_featured_url_widget_register() {
    register_widget( 'Unity3_Featured_Url' );
}
