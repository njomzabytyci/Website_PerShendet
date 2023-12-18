<?php
/**
 * @version    1.0
 * @package    greenmart
 * @author     Thembay Team <support@thembay.com>
 * @copyright  Copyright (C) 2017 Thembay.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: https://thembay.com
 */

add_action('wp_enqueue_scripts', 'greenmart_child_enqueue_styles', 10000);
function greenmart_child_enqueue_styles() {
	$parent_style = 'greenmart-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'greenmart-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

    //wp_deregister_style( 'icofont' );
    //wp_enqueue_style( 'greenmart-child-icofont', get_stylesheet_directory_uri() . '/css/icofont.css' );
}

/**
 * Function for remove srcset (WP4.4)
 *
 */
function greenmart_child_tbay_disable_srcset( $sources ) {
    return false;
}
add_filter( 'wp_calculate_image_srcset', 'greenmart_child_tbay_disable_srcset' );

if ( !function_exists('greenmart_child_remove_css_js_ver') ) {
    function greenmart_child_remove_css_js_ver( $src ) {
        if( strpos( $src, '?ver=' ) )
        $src = remove_query_arg( 'ver', $src );
        return $src;
        }
    add_filter( 'style_loader_src', 'greenmart_child_remove_css_js_ver', 10, 2 );
    add_filter( 'script_loader_src', 'greenmart_child_remove_css_js_ver', 10, 2 ); 
}
