<?php

/**
 * Plugin Name: posts to QR code generator
 * Plugin Uri: https://post-to-qr-code
 * Description:  This plugin can count any wordpress post's total word
 * Author:  sejan ahmed bayzid
 * Version: 1.0
 * License: 
 * Text Domain: posts-to-qrcode
 * Domain path: /language 
 */

// function activation hook / when user  click to activate the plugin
/* function wordcount_activation_hook(){}
 register_activation_hook(__FILE__, "wordcount_activation_hook"); */

// function deactivation hook/ when user click to deactivate the plugin
/*  function wordcount_deactivation_hook(){ }
 register_deactivation_hook(__FILE__, "wordcount_deactivation_hook") */

/*
 *  loading text domain
 */
function posts_qrcode_textdomain()
{
    load_plugin_textdomain("posts-to-qrcode", false, dirname(__FILE__) . "/languages");
}
add_action("plugin_loaded", "posts_qrcode_textdomain");

//* Qr code generator
function pqrc_display_qr_code($content)
{
    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    $current_post_type = get_post_type($current_post_id);
    /* 
    * Post Type Checking Hook
    */
    $excluded_post_type = apply_filters('pqrc_excluded_post_types', array());
    if (in_array($current_post_type, $excluded_post_type)) {
        return $content;
    }
    /* 
    * Dimensions Hook / image sizing
    */
    $height = get_option('pqrc_height');
    $width = get_option('pqrc_width');
    $height = $height ? $height : 200;
    $width = $width ? $width : 200;
    $dimension = apply_filters('pqrc_qrcode_dimension', "{$width}x{$height}");

    $img_src = sprintf('http://api.qrserver.com/v1/create-qr-code/?color=000000&bgcolor=FFFFFF&data=%s&qzone=1&margin=0&size=%s&ecc=L', $current_post_url, $dimension);
    $content .= sprintf("<div class='qrcode'><img src='%s' alt='%s' /></div>", $img_src, $current_post_title);
    return $content;
}
add_filter('the_content', 'pqrc_display_qr_code');



/*
* working with Setting fields
*/
// creating setting fields in for wordpress plugin 
function pqrc_setting_init()
{
    add_settings_section('pqrc_section', __('Posts to QR Code', 'posts-to-qrcode'), 'pqrc_section_callback', 'general');
    // scanner image height and width
    add_settings_field('pqrc_height', __('QR code height', 'posts-to-qrcode'), 'pqrc_display_field', 'general', 'pqrc_section', array('pqrc_height'));
    add_settings_field('pqrc_width', __('QR code width', 'posts-to-qrcode'), 'pqrc_display_field', 'general', 'pqrc_section', array('pqrc_width'));
    // Dropdown select and checkbox
    add_settings_field('pqrc_select', __('Dropdown', 'posts-to-qrcode'), 'pqrc_display_select_field', 'general', 'pqrc_section');
    add_settings_field('pqrc_checkbox', __('select countries', 'posts-to-qrcode'), 'pqrc_display_checkboxgroup_field', 'general', 'pqrc_section');
    // mini-toggle button
    add_settings_field('pqrc_toggle', __('Toggle Button', 'posts-to-qrcode'), 'pqrc_display_toggle_field', 'general', 'pqrc_section');


    register_setting('general', 'pqrc_height',  array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqrc_width',   array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqrc_select',  array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqrc_checkbox');
    register_setting('general', 'pqrc_toggle');
}

function pqrc_section_callback()
{
    echo "<p>" . __('Settings for posts to QR Plugins', 'posts-to-qrcode') . "</p>";
}

/* 
 * instead of below functions repeat/ just one function can do multiple work by passing args
 * if i register a new field don't have to write another function / but every callback name has to be same 
 */
function pqrc_display_field($args)
{
    $options = get_option($args[0]);
    printf("<input type='text' id='%s' name='%s' value='%s'/>", $args[0], $args[0], $options);
}

// function pqrc_display_height()
// {
//     $height = get_option('pqrc_height');
//     printf("<input type='text' id='%s' name='%s' value='%s'/>", 'pqrc_height', 'pqrc_height', $height);
// }

// function pqrc_display_width()
// {
//     $width = get_option('pqrc_width');
//     printf("<input type='text' id='%s' name='%s' value='%s' />", 'pqrc_width', 'pqrc_width', $width);
// }

$pqrc_countries = array(
    __('None', 'posts-to-qrcode'),
    __('Bangladesh', 'posts-to-qrcode'),
    __('Sri lanka', 'posts-to-qrcode'),
    __('Bhutan', 'posts-to-qrcode'),
    __('India', 'posts-to-qrcode'),
    __('America', 'posts-to-qrcode'),
    __('Maldives', 'posts-to-qrcode'),
    __('argentina', 'posts-to-qrcode'),
    __('Pakistan', 'posts-to-qrcode'),
    __('Nepal', 'posts-to-qrcode'),

);

/**
 *   Dropdown fields selection
 */
function pqrc_display_select_field()
{
    global $pqrc_countries;
    $option = get_option('pqrc_select');

    printf("<select type='%s' name='%s'/>", 'pqrc_select', 'pqrc_select');
    foreach ($pqrc_countries as $country) {
        $selected = '';
        if ($option == $country) {
            $selected = 'selected';
        }
        printf("<option value='%s' %s>%s</option>", $country, $selected, $country);
    }
    echo "<select/>";
}
add_action("admin_init", 'pqrc_setting_init');


 /** 
  * Checkbox selection group 
 */
function pqrc_display_checkboxgroup_field()
{
    global $pqrc_countries;
    $pqrc_countries = apply_filters('pqrc_countries', $pqrc_countries);
    $option = get_option('pqrc_checkbox');

    foreach ($pqrc_countries as $country) {
        $selected = '';
        if ( is_array($option) && in_array($country, $option)) {
            $selected = 'checked';
        }
        printf( "<input type='checkbox' name='pqrc_checkbox[]' value='%s' %s>%s<br></input>", $country, $selected, $country);
    }
}
       
          
     
// toggle field
function pqrc_display_toggle_field(){
    $option = get_option('pqrc_toggle');
    echo '<div class="toggle"></div>';
    echo "<input type='hidden' name='pqrc_toggle' id='pqrc_toggle' value='$option'/>";
}

// enqueue the mini toggle button
function pqrc_assets($screen){
    if('options-general.php'== $screen){
        wp_enqueue_style( 'pqrc-minitoggle', plugin_dir_url(__FILE__).'assets/css/minitoggle.css' );

        wp_enqueue_script( 'pqrc-minitoggle', plugin_dir_url(__FILE__).'assets/js/minitoggle.js', array('jquery'),true);
        wp_enqueue_script( 'pqrc-main', plugin_dir_url(__FILE__) . 'assets/js/pqrc-main.js', array('jquery', 'pqrc-minitoggle'), time(), true);
  }
}
add_action('admin_enqueue_scripts', 'pqrc_assets');