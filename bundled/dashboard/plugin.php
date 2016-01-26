<?php

// add new dashboard widgets
function unity3_add_dashboard_widgets() {
    wp_add_dashboard_widget( 'unity3_dashboard_welcome', 'Welcome', 'unity3_add_welcome_widget' );

//    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
//    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
//    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
//    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
//    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
//    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
}
function unity3_add_welcome_widget(){ ?>
    <img style="float: left; margin-right: 10px;" src="<?php echo plugins_url( 'unity3-logo.png' , __FILE__ ); ?>" />
    This content management system has been create for you by Unity 3 Software.  Located on the left is the main menu bar,
    which allows you to edit the content that your website clients will see.  You can add/edit text content as well as add/edit
    images with the integrated image gallery control.  You can also add additional users from the the Users menu.  
<?php }

add_action( 'wp_dashboard_setup', 'unity3_add_dashboard_widgets' );