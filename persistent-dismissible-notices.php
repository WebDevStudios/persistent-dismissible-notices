<?php

/**
 * Plugin Name: Persistent Dismissible Notices
 * Description: Makes all admin notices dismissible and persists their dismissal across sessions for notices with IDs.
 * Plugin URI:  https://github.com/robertdevore/persistent-dismissible-notices/
 * Version:     1.0.0
 * Author:      Robert DeVore
 * Author URI:  https://robertdevore.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: persistent-dismissible-notices
 * Domain Path: /languages
 * Update URI:  https://github.com/deviodigital/persistent-dismissible-notices/
 */

defined( 'ABSPATH' ) || exit;

require 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/deviodigital/persistent-dismissible-notices/',
    __FILE__,
    'persistent-dismissible-notices'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'main' );

// Define the plugin version.
define( 'PDN_VERSION', '1.0.0' );

/**
 * Enqueue admin scripts and pass dismissed notices.
 * 
 * @since  1.0.0
 * @return void
 */
function pdn_enqueue_scripts_and_data() {
    wp_enqueue_script(
        'pdn-dismissible-notices',
        plugin_dir_url( __FILE__ ) . 'assets/js/dismissible-notices.js',
        [],
        PDN_VERSION,
        true
    );

    $user_id           = get_current_user_id();
    $dismissed_notices = get_user_meta( $user_id, 'pdn_dismissed_notices', true );

    if ( ! is_array( $dismissed_notices ) ) {
        $dismissed_notices = [];
    }

    wp_localize_script(
        'pdn-dismissible-notices',
        'PDN_Data',
        [
            'ajax_url'          => admin_url( 'admin-ajax.php' ),
            'nonce'             => wp_create_nonce( 'pdn_dismiss_notice' ),
            'dismissed_notices' => $dismissed_notices,
        ]
    );
}
add_action( 'admin_enqueue_scripts', 'pdn_enqueue_scripts_and_data' );

/**
 * Handle AJAX request to dismiss notices.
 * 
 * @since  1.0.0
 * @return void
 */
function pdn_dismiss_notice() {
    check_ajax_referer( 'pdn_dismiss_notice', 'nonce' );

    if ( ! isset( $_POST['notice_id'] ) || ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( esc_html__( 'Invalid request.', 'persistent-dismissible-notices' ) );
    }

    $notice_id         = sanitize_text_field( $_POST['notice_id'] );
    $user_id           = get_current_user_id();
    $dismissed_notices = get_user_meta( $user_id, 'pdn_dismissed_notices', true );

    if ( ! is_array( $dismissed_notices ) ) {
        $dismissed_notices = [];
    }

    if ( ! in_array( $notice_id, $dismissed_notices, true ) ) {
        $dismissed_notices[] = $notice_id;
        update_user_meta( $user_id, 'pdn_dismissed_notices', $dismissed_notices );
    }

    wp_send_json_success();
}
add_action( 'wp_ajax_pdn_dismiss_notice', 'pdn_dismiss_notice' );

/**
 * Filter admin notices and exclude dismissed ones.
 * 
 * @since  1.0.0
 * @return string
 */
function pdn_filter_admin_notices( $notices ) {
    $user_id           = get_current_user_id();
    $dismissed_notices = get_user_meta( $user_id, 'pdn_dismissed_notices', true );

    if ( ! is_array( $dismissed_notices ) ) {
        $dismissed_notices = [];
    }

    libxml_use_internal_errors( true );
    $dom = new DOMDocument();
    $dom->loadHTML( mb_convert_encoding( '<html><body>' . $notices . '</body></html>', 'HTML-ENTITIES', 'UTF-8' ) );
    $xpath = new DOMXPath( $dom );

    $notice_divs = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " notice ")]' );

    foreach ( $notice_divs as $div ) {
        $id = $div->getAttribute( 'id' );

        if ( $id && in_array( $id, $dismissed_notices, true ) ) {
            $div->parentNode->removeChild( $div );
        }
    }

    $html = '';
    foreach ( $dom->getElementsByTagName( 'body' )->item( 0 )->childNodes as $child ) {
        $html .= $dom->saveHTML( $child );
    }

    return $html;
}
add_filter( 'admin_notices', 'pdn_filter_admin_notices', 1000 );
