<?php
/**
 * Plugin Name: Kashiwazaki SEO Custom 404
 * Plugin URI: https://www.tsuyoshikashiwazaki.jp
 * Description: HTTP 404エラー時にオリジナルのエラーページを表示し、リダイレクト候補や関連性の高い記事・最新記事を提示します。配色テーマ選択、構造化マークアップ、フッター機能付き。リダイレクト動作のカスタマイズも可能です。
 * Version: 1.0.2
 * Author: 柏崎 剛
 * Author URI: https://www.tsuyoshikashiwazaki.jp
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kashiwazaki-seo-custom-404
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'KSC404_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KSC404_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KSC404_VERSION', '1.0.2' );

require_once KSC404_PLUGIN_DIR . 'includes/utils.php';
require_once KSC404_PLUGIN_DIR . 'includes/settings.php';
require_once KSC404_PLUGIN_DIR . 'includes/custom-404-page.php';

register_activation_hook( __FILE__, 'ksc404_activate' );
register_deactivation_hook( __FILE__, 'ksc404_deactivate' );
register_uninstall_hook( __FILE__, 'ksc404_uninstall' );

function ksc404_activate() {
    $default_settings = ksc404_get_default_settings();
    $current_settings = get_option( 'ksc404_settings' );
    if ( false === $current_settings ) {
        add_option( 'ksc404_settings', $default_settings );
    } else {
        $updated_settings = wp_parse_args( $current_settings, $default_settings );
        if ($updated_settings !== $current_settings) {
            update_option('ksc404_settings', $updated_settings);
        }
    }
}

function ksc404_deactivate() {}

function ksc404_uninstall() {
    delete_option( 'ksc404_settings' );
}

function ksc404_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=ksc404_settings_page">' . __( 'Settings', 'kashiwazaki-seo-custom-404' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
$plugin_basename = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin_basename", 'ksc404_add_settings_link' );

function ksc404_load_textdomain() {
    load_plugin_textdomain( 'kashiwazaki-seo-custom-404', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'ksc404_load_textdomain' );

?>