<?php
$upload = wp_upload_dir();
if (file_exists($upload['basedir'] . '/unity3-functions.php')) {
   require_once($upload['basedir'] . '/unity3-functions.php');
}

require_once (Unity3::$dir . 'bundled/defaults/defaults.php');
require_once (Unity3::$dir . 'bundled/dashboard/plugin.php');
require_once (Unity3::$dir . 'bundled/url-shortcodes/url-shortcodes.php');
require_once (Unity3::$dir . 'bundled/drag-sort-posts/drag-sort-posts.php');
require_once (Unity3::$dir . 'bundled/taxonomy-metabox/plugin.php');

//widgets:
require_once (Unity3::$dir . 'bundled/featured-url-widget/plugin.php');
require_once (Unity3::$dir . 'bundled/textpro-widget/textpro-widget.php');