<?php
add_action('init', 'unity3_register_url_shortcodes');

function unity3_register_url_shortcodes() {
    add_shortcode('url_base', 'unity3_url_base_function');
    add_shortcode('url_uploads', 'unity3_url_uploads_function');
    add_shortcode('url_template', 'unity3_url_template_function');
    add_shortcode('permalink', 'unity3_permalink_function');
    add_shortcode('permalink_list', 'unity3_permalink_list_function');
    add_shortcode('wooaudio', 'unity3_wooaudio_function');
}


function unity3_url_base_function() {
	return get_bloginfo( "url" );
}

function unity3_url_uploads_function() {
    $upload_dir = wp_upload_dir();
    return $upload_dir['baseurl'];
}

// [url_template]
function unity3_url_template_function() {
    return get_stylesheet_directory_uri();
}

function unity3_permalink_function($atts) {
    extract(shortcode_atts(array(
        'id' => 1,
        'text' => ""  // default value if none supplied
    ), $atts));

    if ($text) {
        $url = get_permalink($id);
        return "<a href='$url'>$text</a>";
    } else {
	   return get_permalink($id);
    }
}

function unity3_permalink_list_function($atts) {
    $atts = array_merge(array(
        'post_type' => 'post',
	'post_status' => 'publish',
        'list_type' => 'ul',
    ), $atts);
    
    $list_type = $atts['list_type'];
    unset($atts['list_type']);
    
    $postslist = get_posts( $atts );
    $permalink_list = '';
    foreach ( $postslist as $post ) {
        $permalink_list .= '<li><a href="' . get_the_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a></li>';
    }
    
    return "<$list_type>" . $permalink_list . "</$list_type>";
}

function unity3_wooaudio_function($atts) {
    $atts = shortcode_atts(array(
        'id' => get_the_ID()
    ), $atts);
        
    $title = get_the_title($atts['id']);
    
    $upload_dir = wp_upload_dir();
    $base_url = $upload_dir['baseurl'] . '/woo-audio/' . $atts['id'];

    
    $mp3 = "$base_url/preview.mp3"; 
    $ogg = "$base_url/preview.ogg";   
    
    return do_shortcode("[mp3j title='$title - Preview' track='$mp3' counterpart='$ogg']");
}