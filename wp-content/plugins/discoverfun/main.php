<?php
/**
* PLugin Name: Discover fun
* Plugin URI: www.philwebservices.com
* Author: Serg
* Author URI: www.casquejs.freevar.com
* Version: 1
* Description: Discover fun custom plugin
*/
define('PLUGIN_URL', plugins_url('' , __FILE__) . '/'); //plugin url
define('PLUGIN_PATH', plugin_dir_path(__FILE__) . '/'); //plugin absolute path
define('PLUGIN_ADMIN_URL', plugins_url('' , __FILE__) . '/admin/'); //plugin admin url
define('PLUGIN_ADMIN_FORM_URL', plugins_url('' , __FILE__) . '/admin/form/'); //plugin admin form url
define('PLUGIN_ADMIN_FORM_URL', plugins_url('' , __FILE__) . '/admin/form/'); //plugin admin form url
define('UPLOAD_DIR',WP_CONTENT_URL .'/uploads/');
define('UPLOAD_THUMB_DIR',WP_CONTENT_URL .'/uploads/thumbnail/');
 if ( ! defined( 'WP_CONTENT_URL' ) )
    define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
 if ( ! defined( 'WP_CONTENT_DIR' ) )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
 if ( ! defined( 'WP_PLUGIN_URL' ) )
    define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
 if ( ! defined( 'WP_PLUGIN_DIR' ) )
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
 if ( ! defined( 'WPMU_PLUGIN_URL' ) )
    define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL. '/mu-plugins' );
 if ( ! defined( 'WPMU_PLUGIN_DIR' ) )
    define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' );


require ('admin/admin.php');
require ('front/front.php');
require ('db/model.php');
require ('functions.php');
require (PLUGIN_PATH.'accomodation.php');
require (PLUGIN_PATH.'process-payment.php');

/* include(PLUGIN_PATH.'class/CurrencyConverter.php');
   $x = new CurrencyConverter();
   echo $x->convert(2.50,'GBP','USD');*/





