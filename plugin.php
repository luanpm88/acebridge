<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WordPress Plugin for Acelle Plugin
 * Plugin URI:        https://acellemail.com/
 * Description:       A plugin.
 * Version:           1.0
 * Author:            Acelle Team @ Basic Technology
 * Author URI:        https://acellemail.com/
 */

// Get laravel app response
function acebridge_getResponse($path=null)
{
    if (!defined('LARAVEL_START')) {
        define('LARAVEL_START', microtime(true));
    }
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $acebridgeKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    if (!$path) {
        $path = isset($_REQUEST['path']) ? $_REQUEST['path'] : '/';
    }
    $response = $acebridgeKernel->handle(
        App\Wordpress\LaravelRequest::capture($path)
    );

    return $response;
}

function acebridge_activate() {
    acebridge_getResponse('/');

    // actually call the Artisan command
    \Artisan::call('config:cache');
}
  
register_activation_hook( __FILE__, 'acebridge_activate' );

// Main admin menu
function acebridge_menu()
{
    // add menu page
    $menu = add_menu_page(esc_html__('Acelle Connect', 'acebridge'), esc_html__('Acelle Connect', 'acebridge'), 'edit_pages', 'wp-acebridge-main', function () {
    }, null, 54);
}
add_action('admin_menu', 'acebridge_menu');

// Default sub menu
function acebridge_menu_main()
{
    $hook = add_submenu_page('wp-acebridge-main', esc_html__('Dashboard', 'acebridge'), esc_html__('Dashboard', 'acebridge'), 'edit_pages', 'wp-acebridge-main', function () {
        $response = acebridge_getResponse('/acelle-connect');

        // send response
        $response->sendHeaders();
        $response->sendContent();
    });
}
add_action('admin_menu', 'acebridge_menu_main');

// Ajax page
function acebridge_ajax()
{
    $response = acebridge_getResponse($path);

    // Comment line below, do not send response
    $response->send();

    // Do not use wp_die() here, it will produce WP default layout, use die() instead;
    die();
}
add_action('wp_ajax_acebridge_ajax', 'acebridge_ajax');

// Helpers
/**
 * WP action helper for laravel.
 */

function acebridge_public_url($path)
{
    return plugins_url('acebridge/public/' . $path);
}

/**
 * WP action helper for laravel.
 */
function acebridge_wp_action($name, $parameters = [], $absolute = true)
{
    $base = url('/');
    $full = app('url')->action($name, $parameters, $absolute);
    $path = str_replace($base, '', $full);

    return admin_url('admin.php?page=wp-acebridge-main&path=' . str_replace('?', '&', $path));
}

/**
 * WP action helper for laravel.
 */
function acebridge_lr_action($name, $parameters = [], $absolute = true)
{
    $base = url('/');
    $full = app('url')->action($name, $parameters, $absolute);
    $path = str_replace($base, '', $full);
    return admin_url('admin-ajax.php?action=acebridge_ajax&path=' . str_replace('?', '&', $path));
}

/**
 * WP url helper for laravel.
 */
function acebridge_wp_url($path = null, $parameters = [], $secure = null)
{
    if (is_null($path)) {
        $path = app(\Illuminate\Routing\UrlGenerator::class);
    }

    $base = url('/');
    $full = app(\Illuminate\Routing\UrlGenerator::class)->to($path, $parameters, $secure);
    $path = str_replace($base, '', $full);

    return admin_url('admin.php?page=wp-acebridge-main&path=' . str_replace('?', '&', $path));
}

/**
 * WP url helper for laravel.
 */
function acebridge_lr_url($path = null, $parameters = [], $secure = null)
{
    if (is_null($path)) {
        $path = app(\Illuminate\Routing\UrlGenerator::class);
    }

    $base = url('/');
    $full = app(\Illuminate\Routing\UrlGenerator::class)->to($path, $parameters, $secure);
    $path = str_replace($base, '', $full);

    return admin_url('admin-ajax.php?action=acebridge_ajax&path=' . str_replace('?', '&', $path));
}

// WordPress rest api connect
function acebridge_connect( $data ) {
    $response = acebridge_getResponse('/connect');
    // Comment line below, do not send response
    $response->send();

    die();
}
add_action( 'rest_api_init', function () {
    register_rest_route( '/acelle', '/bridge', array(
        'methods' => 'GET',
        'callback' => 'acebridge_connect',
    ));
});

// add beemail css to WordPress admin area
function acebridge_add_theme_scripts()
{
    wp_enqueue_style('acebridge', plugin_dir_url(__FILE__) . 'public/css/wp-admin.css');
}
add_action('admin_enqueue_scripts', 'acebridge_add_theme_scripts');
