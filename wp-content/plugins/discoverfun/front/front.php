<?php
/**
*discover fun function
*@param none
*@return none
*/




function init_front_script(){


    //if( is_page('accomodation')) {

        if(!wp_style_is('jquery-ui-css')) {
            wp_register_style('jquery-ui-css',PLUGIN_URL .'js/jquery-ui-1.10.0.custom/development-bundle/themes/base/jquery-ui.css');
            wp_enqueue_style('jquery-ui-css');
        }

        if(!wp_script_is('jquery-ui-js')) {
            wp_register_script('jquery-ui-js',PLUGIN_URL .'js/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.min.js',array('jquery'),FALSE,TRUE);
            wp_enqueue_script('jquery-ui-js');
        }


        if(!wp_script_is('front-custom')) {
            wp_register_script('front-custom', PLUGIN_URL . 'js/front-custom.js',array('jquery','jquery-ui-js'),FALSE,TRUE);
            wp_enqueue_script('front-custom');
        } 
    
    //}


    

   

}


add_action('wp_enqueue_scripts','init_front_script');