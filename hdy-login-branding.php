<?php
/**
 * Plugin Name: HDY Login Branding
 * Plugin URI: https://hdyhaus.com/wp-plugins/hdy-login-branding/
 * Description: Brand WordPress login, registration, and password-recovery screens from Settings.
 * Version: 1.2.0
 * Author: HDY Haus
 * Author URI: https://hdyhaus.com/wp-plugins/hdy-login-branding/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: hdy-login-branding
 *
 * @package HDYLoginBranding
 */

defined( 'ABSPATH' ) || exit;

define( 'HDY_LOGIN_BRANDING_VERSION', '1.2.0' );
define( 'HDY_LOGIN_BRANDING_SLUG', 'hdy-login-branding' );
define( 'HDY_LOGIN_BRANDING_OPTION_ENABLED', 'hdylb_enabled' );
define( 'HDY_LOGIN_BRANDING_OPTION_ID', 'hdylb_id' );
define( 'HDY_LOGIN_BRANDING_OPTION_BUTTON_COLOR', 'hdylb_button_color' );
define( 'HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT', 'hdylb_button_text' );
define( 'HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT_COLOR', 'hdylb_button_text_color' );
define( 'HDY_LOGIN_BRANDING_OPTION_BACKGROUND_COLOR', 'hdylb_background_color' );
define( 'HDY_LOGIN_BRANDING_OPTION_BACKGROUND_IMAGE_ID', 'hdylb_background_image_id' );
define( 'HDY_LOGIN_BRANDING_OPTION_REGISTRATION_HEADING', 'hdylb_registration_heading' );
define( 'HDY_LOGIN_BRANDING_OPTION_REGISTRATION_BUTTON_TEXT', 'hdylb_registration_button_text' );
define( 'HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_MESSAGE', 'hdylb_lost_password_message' );
define( 'HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_BUTTON_TEXT', 'hdylb_lost_password_button_text' );
define( 'HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_MESSAGE', 'hdylb_reset_password_message' );
define( 'HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_BUTTON_TEXT', 'hdylb_reset_password_button_text' );

require_once __DIR__ . '/includes/settings.php';

/**
 * Registers plugin settings.
 *
 * @return void
 */
function hdylb_register_settings() {
	foreach ( array( 'login', 'register', 'lostpassword', 'resetpass' ) as $flow ) {
		register_setting(
			HDY_LOGIN_BRANDING_SLUG,
			'hdylb_' . $flow . '_shared',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'hdylb_sanitize_enabled',
				'default'           => 1,
			)
		);
		foreach ( array( 'background_color', 'button_color', 'button_text_color' ) as $color ) {
			register_setting(
				HDY_LOGIN_BRANDING_SLUG,
				'hdylb_' . $flow . '_' . $color,
				array(
					'type'              => 'string',
					'sanitize_callback' => 'hdylb_sanitize_button_color',
					'default'           => '',
				)
			);
		}
	}
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_ENABLED,
		array(
			'type'              => 'boolean',
			'sanitize_callback' => 'hdylb_sanitize_enabled',
			'default'           => 0,
		)
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_ID,
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'hdylb_sanitize_logo_id',
			'default'           => 0,
		)
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BACKGROUND_IMAGE_ID,
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'hdylb_sanitize_image_id',
			'default'           => 0,
		)
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BUTTON_COLOR,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_color',
			'default'           => '',
		)
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_text',
			'default'           => '',
		)
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT_COLOR,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_color',
			'default'           => '',
		)
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BACKGROUND_COLOR,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_color',
			'default'           => '',
		)
	);

	$text_options = array(
		HDY_LOGIN_BRANDING_OPTION_REGISTRATION_HEADING,
		HDY_LOGIN_BRANDING_OPTION_REGISTRATION_BUTTON_TEXT,
		HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_MESSAGE,
		HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_BUTTON_TEXT,
		HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_MESSAGE,
		HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_BUTTON_TEXT,
	);

	foreach ( $text_options as $option_name ) {
		register_setting(
			HDY_LOGIN_BRANDING_SLUG,
			$option_name,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'hdylb_sanitize_text',
				'default'           => '',
			)
		);
	}
}
add_action( 'admin_init', 'hdylb_register_settings' );

/**
 * Sanitizes enabled setting value.
 *
 * @param mixed $value Setting value.
 * @return int
 */
function hdylb_sanitize_enabled( $value ) {
	return $value ? 1 : 0;
}

/**
 * Sanitizes logo attachment ID.
 *
 * @param mixed $value Setting value.
 * @return int
 */
function hdylb_sanitize_logo_id( $value ) {
	return hdylb_sanitize_image_id( $value );
}

/**
 * Sanitizes an image attachment ID.
 *
 * @param mixed $value Setting value.
 * @return int
 */
function hdylb_sanitize_image_id( $value ) {
	$value = absint( $value );

	if ( ! $value ) {
		return 0;
	}

	if ( ! wp_attachment_is_image( $value ) ) {
		return 0;
	}

	return $value;
}

/**
 * Sanitizes button color values.
 *
 * @param mixed $value Setting value.
 * @return string
 */
function hdylb_sanitize_button_color( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	$value = sanitize_hex_color( $value );

	return $value ? $value : '';
}

/**
 * Sanitizes button text value.
 *
 * @param mixed $value Setting value.
 * @return string
 */
function hdylb_sanitize_button_text( $value ) {
	return hdylb_sanitize_text( $value );
}

/**
 * Sanitizes a plain-text setting value.
 *
 * @param mixed $value Setting value.
 * @return string
 */
function hdylb_sanitize_text( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	return sanitize_text_field( (string) $value );
}

/**
 * Adds plugin settings page.
 *
 * @return void
 */
function hdylb_add_settings_page() {
	add_options_page(
		esc_html__( 'HDY Login Branding', 'hdy-login-branding' ),
		esc_html__( 'HDY Login Branding', 'hdy-login-branding' ),
		'manage_options',
		HDY_LOGIN_BRANDING_SLUG,
		'hdylb_render_settings_page'
	);
}
add_action( 'admin_menu', 'hdylb_add_settings_page' );

/**
 * Enqueues admin assets for plugin settings screen.
 *
 * @param string $hook Current admin page hook suffix.
 * @return void
 */
function hdylb_admin_assets( $hook ) {
	if ( 'settings_page_' . HDY_LOGIN_BRANDING_SLUG !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_style(
		'hdy-login-branding-admin',
		plugin_dir_url( __FILE__ ) . 'assets/admin.css',
		array(),
		HDY_LOGIN_BRANDING_VERSION
	);

	wp_enqueue_script(
		'hdy-login-branding-admin',
		plugin_dir_url( __FILE__ ) . 'assets/admin.js',
		array( 'jquery', 'wp-color-picker' ),
		HDY_LOGIN_BRANDING_VERSION,
		true
	);

	wp_localize_script(
		'hdy-login-branding-admin',
		'hdyLoginBranding',
		array(
			'logoTitle'       => esc_html__( 'Select Login Logo', 'hdy-login-branding' ),
			'logoButton'      => esc_html__( 'Use this logo', 'hdy-login-branding' ),
			'backgroundTitle' => esc_html__( 'Select Login Background', 'hdy-login-branding' ),
			'backgroundButton' => esc_html__( 'Use this image', 'hdy-login-branding' ),
			'unsaved'         => __( 'Unsaved changes', 'hdy-login-branding' ),
			'contrastPass'    => __( 'Button contrast meets 4.5:1 for normal text.', 'hdy-login-branding' ),
			'contrastFail'    => __( 'Button contrast is below 4.5:1. Choose a darker background or a lighter text color, or vice versa.', 'hdy-login-branding' ),
			'contrastInvalid' => __( 'Enter a valid hex color to preview it. Invalid colors will not be saved.', 'hdy-login-branding' ),
			'username'        => __( 'Username or Email Address', 'hdy-login-branding' ),
			'newPassword'     => __( 'New Password', 'hdy-login-branding' ),
			'password'        => __( 'Password', 'hdy-login-branding' ),
			'email'           => __( 'Email', 'hdy-login-branding' ),
			'defaults'        => array(
				'login'        => array(
					'message' => '',
					'button'  => __( 'Log In', 'hdy-login-branding' ),
				),
				'register'     => array(
					'message' => __( 'Register For This Site', 'hdy-login-branding' ),
					'button'  => __( 'Register', 'hdy-login-branding' ),
				),
				'lostpassword' => array(
					'message' => __( 'Please enter your username or email address. You will receive an email message with instructions on how to reset your password.', 'hdy-login-branding' ),
					'button'  => __( 'Get New Password', 'hdy-login-branding' ),
				),
				'resetpass'    => array(
					'message' => __( 'Enter your new password below or generate one.', 'hdy-login-branding' ),
					'button'  => __( 'Save Password', 'hdy-login-branding' ),
				),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'hdylb_admin_assets' );


/**
 * Returns the current WordPress login action with equivalent actions normalized.
 *
 * @return string
 */
function hdylb_get_login_action() {
	if ( isset( $GLOBALS['action'] ) && is_string( $GLOBALS['action'] ) ) {
		$action = sanitize_key( $GLOBALS['action'] );
	} else {
		$action = 'login';
	}

	if ( 'retrievepassword' === $action ) {
		return 'lostpassword';
	}

	if ( 'rp' === $action ) {
		return 'resetpass';
	}

	return $action;
}

/**
 * Returns custom button text for the current login action.
 *
 * @param string $action Login action.
 * @return string
 */
function hdylb_get_button_text_for_action( $action ) {
	$options = array(
		'login'        => HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT,
		'register'     => HDY_LOGIN_BRANDING_OPTION_REGISTRATION_BUTTON_TEXT,
		'lostpassword' => HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_BUTTON_TEXT,
		'resetpass'    => HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_BUTTON_TEXT,
	);

	if ( ! isset( $options[ $action ] ) ) {
		return '';
	}

	return trim( hdylb_sanitize_text( get_option( $options[ $action ], '' ) ) );
}

/**
 * Replaces the default prompt for supported authentication flows.
 *
 * @param string $message Existing login message markup.
 * @return string
 */
function hdylb_filter_login_message( $message ) {
	$messages = array(
		'register'     => array(
			'option'  => HDY_LOGIN_BRANDING_OPTION_REGISTRATION_HEADING,
			// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- Match the translated WordPress core prompt.
			'default' => __( 'Register For This Site', 'default' ),
		),
		'lostpassword' => array(
			'option'  => HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_MESSAGE,
			// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- Match the translated WordPress core prompt.
			'default' => __( 'Please enter your username or email address. You will receive an email message with instructions on how to reset your password.', 'default' ),
		),
		'resetpass'    => array(
			'option'  => HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_MESSAGE,
			// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- Match the translated WordPress core prompt.
			'default' => __( 'Enter your new password below or generate one.', 'default' ),
		),
	);
	$action   = hdylb_get_login_action();

	if ( ! isset( $messages[ $action ] ) ) {
		return $message;
	}

	$custom_message = trim( hdylb_sanitize_text( get_option( $messages[ $action ]['option'], '' ) ) );

	if ( '' === $custom_message ) {
		return $message;
	}

	$default_message = esc_html( $messages[ $action ]['default'] );
	$custom_message  = esc_html( $custom_message );

	if ( false !== strpos( $message, $default_message ) ) {
		return str_replace( $default_message, $custom_message, $message );
	}

	return sprintf(
		'<div class="notice notice-info message"><p>%s</p></div>%s',
		$custom_message,
		$message
	);
}
add_filter( 'login_message', 'hdylb_filter_login_message' );

/**
 * Adds logo and button customizations to the login screen.
 *
 * @return void
 */
function hdylb_login_styles() {
	$css     = '';
	$enabled = (int) get_option( HDY_LOGIN_BRANDING_OPTION_ENABLED, 0 );

	if ( $enabled ) {
		$logo_id  = (int) get_option( HDY_LOGIN_BRANDING_OPTION_ID, 0 );
		$logo_url = '';

		if ( $logo_id ) {
			$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
		} else {
			$theme_logo_id = (int) get_theme_mod( 'custom_logo' );

			if ( $theme_logo_id ) {
				$logo_url = wp_get_attachment_image_url( $theme_logo_id, 'full' );
			}
		}

		if ( $logo_url ) {
			$css .= sprintf(
				'#login h1 a{background:url("%s") center/contain no-repeat !important;width:100%%;max-width:320px;height:120px;margin:0 auto 25px;display:block;overflow:hidden;text-indent:-9999px;}',
				esc_url( $logo_url )
			);
		}
	}

	$colors       = hdylb_get_flow_colors( hdylb_get_login_action() );
	$button_color = $colors['button_color'];

	if ( '' !== $button_color ) {
		$button_color = sanitize_hex_color( $button_color );

		if ( $button_color ) {
			$css .= sprintf(
				'#login #wp-submit{background-color:%1$s;border-color:%1$s;box-shadow:0 1px 0 %1$s;}#login #wp-submit:hover,#login #wp-submit:focus{filter:brightness(0.92);}',
				$button_color
			);
		}
	}

	$button_text_color = $colors['button_text_color'];

	if ( '' !== $button_text_color ) {
		$button_text_color = sanitize_hex_color( $button_text_color );

		if ( $button_text_color ) {
			$css .= sprintf(
				'#login #wp-submit{color:%1$s;}',
				$button_text_color
			);
		}
	}

	$background_color = $colors['background_color'];

	if ( '' !== $background_color ) {
		$background_color = sanitize_hex_color( $background_color );

		if ( $background_color ) {
			$css .= sprintf(
				'body.login{background-color:%1$s;}',
				$background_color
			);
		}
	}

	$background_image_id  = (int) get_option( HDY_LOGIN_BRANDING_OPTION_BACKGROUND_IMAGE_ID, 0 );
	$background_image_url = $background_image_id ? wp_get_attachment_image_url( $background_image_id, 'full' ) : '';

	if ( $background_image_url ) {
		$css .= sprintf(
			'body.login{background-image:url("%s");background-position:center;background-repeat:no-repeat;background-size:cover;}',
			esc_url( $background_image_url )
		);
	}

	if ( '' !== $css ) {
		wp_add_inline_style( 'login', $css );
	}

	$button_text = hdylb_get_button_text_for_action( hdylb_get_login_action() );

	if ( '' !== $button_text ) {
		$script = 'document.addEventListener("DOMContentLoaded",function(){var btn=document.getElementById("wp-submit");if(btn){btn.value=' . wp_json_encode( $button_text ) . ';}});';

		wp_register_script( 'hdy-login-branding-login', '', array(), HDY_LOGIN_BRANDING_VERSION, true );
		wp_enqueue_script( 'hdy-login-branding-login' );
		wp_add_inline_script( 'hdy-login-branding-login', $script );
	}
}
add_action( 'login_enqueue_scripts', 'hdylb_login_styles' );

/**
 * Sets the login logo URL target.
 *
 * @param string $url Existing URL.
 * @return string
 */
function hdylb_header_url( $url ) {
	$enabled = (int) get_option( HDY_LOGIN_BRANDING_OPTION_ENABLED, 0 );

	if ( ! $enabled ) {
		return $url;
	}

	$logo_id = (int) get_option( HDY_LOGIN_BRANDING_OPTION_ID, 0 );

	if ( ! $logo_id ) {
		return $url;
	}

	return home_url( '/' );
}
add_filter( 'login_headerurl', 'hdylb_header_url' );

/**
 * Adds Settings link to the plugin actions row.
 *
 * @param array $links Existing plugin action links.
 * @return array
 */
function hdylb_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=' . HDY_LOGIN_BRANDING_SLUG ) ),
		esc_html__( 'Settings', 'hdy-login-branding' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'hdylb_plugin_action_links' );
