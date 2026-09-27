<?php
// Lightweight harness: exercises plugin rendering without a database or real accounts.
define( 'ABSPATH', __DIR__ );
$test_options = array();
function add_action() {}
function add_filter() {}
function plugin_basename( $file ) { return basename( $file ); }
function plugin_dir_url( $file ) { return '/'; }
function get_option( $key, $default = false ) { return $GLOBALS['test_options'][ $key ] ?? $default; }
function get_theme_mod() { return 0; }
function wp_get_attachment_image_url( $id ) { return $GLOBALS['test_attachment_urls'][ $id ] ?? ''; }
function wp_attachment_is_image( $id ) { return ! empty( $GLOBALS['test_image_ids'][ $id ] ); }
function absint( $value ) { return abs( (int) $value ); }
function current_user_can() { return true; }
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
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $value ) ); }
function register_setting( $group, $key, $args ) { $GLOBALS['test_registered'][ $key ] = $args; }
require dirname( __DIR__ ) . '/hdy-login-branding.php';
