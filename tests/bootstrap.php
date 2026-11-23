<?php
// Lightweight harness: exercises plugin rendering without a database or real accounts.
define( 'ABSPATH', __DIR__ );
$test_options = array();
function add_action( $hook, $callback, $priority = 10 ) { $GLOBALS['test_actions'][ $hook ][ $priority ][] = $callback; }
function remove_action( $hook, $callback, $priority = 10 ) { foreach ( $GLOBALS['test_actions'][ $hook ][ $priority ] ?? array() as $key => $registered ) { if ( $registered === $callback ) { unset( $GLOBALS['test_actions'][ $hook ][ $priority ][ $key ] ); } } }
function add_filter( $hook, $callback, $priority = 10 ) { $GLOBALS['test_filters'][ $hook ][ $priority ][] = $callback; }
function do_action( $hook ) {
	if ( empty( $GLOBALS['test_actions'][ $hook ] ) ) { return; }
	ksort( $GLOBALS['test_actions'][ $hook ] );
	foreach ( $GLOBALS['test_actions'][ $hook ] as $callbacks ) { foreach ( $callbacks as $callback ) { call_user_func( $callback ); } }
}
function apply_filters( $hook, $value ) {
	if ( empty( $GLOBALS['test_filters'][ $hook ] ) ) { return $value; }
	ksort( $GLOBALS['test_filters'][ $hook ] );
	foreach ( $GLOBALS['test_filters'][ $hook ] as $callbacks ) { foreach ( $callbacks as $callback ) { $value = call_user_func( $callback, $value ); } }
	return $value;
}
function plugin_basename( $file ) { return basename( $file ); }
function plugin_dir_url( $file ) { return '/'; }
function get_option( $key, $default = false ) { return $GLOBALS['test_options'][ $key ] ?? $default; }
function get_theme_mod() { return 0; }
function wp_get_attachment_image_url( $id ) { return $GLOBALS['test_attachment_urls'][ $id ] ?? ''; }
function wp_attachment_is_image( $id ) { return ! empty( $GLOBALS['test_image_ids'][ $id ] ); }
function absint( $value ) { return abs( (int) $value ); }
function current_user_can( $capability ) { return isset( $GLOBALS['test_capabilities'][ $capability ] ) ? $GLOBALS['test_capabilities'][ $capability ] : true; }
function is_user_logged_in() { return ! empty( $GLOBALS['test_user'] ); }
function wp_get_current_user() { return (object) ( $GLOBALS['test_user'] ?? array( 'roles' => array() ) ); }
function wp_roles() { return (object) array( 'roles' => $GLOBALS['test_roles'] ?? array() ); }
function translate_user_role( $role ) { return $role; }
function is_admin() { return ! empty( $GLOBALS['test_is_admin'] ); }
function is_multisite() { return ! empty( $GLOBALS['test_multisite'] ); }
function is_super_admin() { return ! empty( $GLOBALS['test_super_admin'] ); }
function wp_doing_ajax() { return ! empty( $GLOBALS['test_ajax'] ); }
function wp_doing_cron() { return ! empty( $GLOBALS['test_cron'] ); }
function wp_is_json_request() { return ! empty( $GLOBALS['test_rest'] ); }
function wp_die( $message, $title = '', $args = array() ) { throw new RuntimeException( 'wp_die:' . json_encode( array( $message, $title, $args ) ) ); }
function __( $text, $domain = '' ) { return $text; }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $text ) { return esc_html( $text ); }
function esc_url( $text ) { return esc_html( $text ); }
function esc_textarea( $text ) { return esc_html( $text ); }
function esc_html__( $text, $domain = '' ) { return esc_html( $text ); }
function esc_attr__( $text, $domain = '' ) { return esc_attr( $text ); }
function esc_html_e( $text, $domain = '' ) { echo esc_html( $text ); }
function esc_attr_e( $text, $domain = '' ) { echo esc_attr( $text ); }
function checked( $a, $b ) { if ( (string) $a === (string) $b ) { echo ' checked'; } }
function disabled( $a, $b ) { if ( (string) $a === (string) $b ) { echo ' disabled'; } }
function settings_fields() {}
function submit_button( $label ) { echo '<button type="submit" class="button button-primary">' . esc_html( $label ) . '</button>'; }
function admin_url( $path ) { return '/?asset=logo'; }
function home_url() { return 'https://example.test/'; }
function get_bloginfo( $show = '', $filter = 'raw' ) { return 'Example Site'; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_unslash( $value ) { return stripslashes( $value ); }
function esc_url_raw( $url, $protocols = array( 'http', 'https' ) ) {
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) { return ''; }
	$scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );
	return in_array( $scheme, $protocols, true ) ? $url : '';
}
function sanitize_email( $email ) { return filter_var( $email, FILTER_SANITIZE_EMAIL ); }
function is_email( $email ) { return (bool) filter_var( $email, FILTER_VALIDATE_EMAIL ); }
function wp_enqueue_media() {}
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function wp_register_script() {}
function wp_add_inline_script() {}
function wp_json_encode( $value ) { return json_encode( $value ); }
function wp_add_inline_style( $handle, $css ) { $GLOBALS['test_inline_styles'][ $handle ] = $css; }
function wp_localize_script( $handle, $name, $data ) { $GLOBALS['test_localized'] = $data; }
function sanitize_hex_color( $value ) { return preg_match( '/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $value ) ? $value : ''; }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_textarea_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $value ) ); }
function register_setting( $group, $key, $args ) { $GLOBALS['test_registered'][ $key ] = $args; }
require dirname( __DIR__ ) . '/hdy-login-branding.php';
