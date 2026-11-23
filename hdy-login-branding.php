<?php
/**
 * Plugin Name: HDY Login and Registration Branding
 * Plugin URI: https://hdyhaus.com/wp-plugins/hdy-login-branding/
 * Description: Brand WordPress login, registration, and password-recovery screens, customize sender details, add a support link, and manage role-based Dashboard and Toolbar access.
 * Version: 1.4.0
 * Author: HDY Haus
 * Author URI: https://hdyhaus.com/wp-plugins/hdy-login-branding/
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: hdy-login-branding
 *
 * @package HDYLoginBranding
 */

defined( 'ABSPATH' ) || exit;

define( 'HDY_LOGIN_BRANDING_VERSION', '1.4.0' );
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
define( 'HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM_NAME', 'hdylb_email_from_name' );
define( 'HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM', 'hdylb_email_from' );
define( 'HDY_LOGIN_BRANDING_OPTION_SUPPORT_LABEL', 'hdylb_support_label' );
define( 'HDY_LOGIN_BRANDING_OPTION_SUPPORT_URL', 'hdylb_support_url' );
define( 'HDY_LOGIN_BRANDING_OPTION_SUPPORT_NEW_TAB', 'hdylb_support_new_tab' );
define( 'HDY_LOGIN_BRANDING_OPTION_DASHBOARD_BLOCKED_ROLES', 'hdylb_dashboard_blocked_roles' );
define( 'HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES', 'hdylb_toolbar_hidden_roles' );
define( 'HDY_LOGIN_BRANDING_OPTION_DASHBOARD_DENIAL_MESSAGE', 'hdylb_dashboard_denial_message' );

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

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM_NAME,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_sender_name',
			'default'           => '',
		)
	);
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_sender_email',
			'default'           => '',
		)
	);
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_SUPPORT_LABEL,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_text',
			'default'           => 'Support',
		)
	);
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_SUPPORT_URL,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_support_url',
			'default'           => '',
		)
	);
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_SUPPORT_NEW_TAB,
		array(
			'type'              => 'boolean',
			'sanitize_callback' => 'hdylb_sanitize_enabled',
			'default'           => 0,
		)
	);
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_DASHBOARD_BLOCKED_ROLES,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'hdylb_sanitize_role_list',
			'default'           => array(),
			'show_in_rest'      => false,
		)
	);
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'hdylb_sanitize_role_list',
			'default'           => array(),
			'show_in_rest'      => false,
		)
	);
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_DASHBOARD_DENIAL_MESSAGE,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_dashboard_denial_message',
			'default'           => hdylb_get_default_dashboard_denial_message(),
		)
	);
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
 * Sanitizes and validates an email sender name.
 *
 * @param mixed $value Setting value.
 * @return string
 */
function hdylb_sanitize_sender_name( $value ) {
	return hdylb_sanitize_text( $value );
}

/**
 * Sanitizes and validates an email sender address.
 *
 * @param mixed $value Setting value.
 * @return string
 */
function hdylb_sanitize_sender_email( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$value = sanitize_email( trim( (string) $value ) );

	return is_email( $value ) ? $value : '';
}

/**
 * Sanitizes a support URL, allowing only HTTP and HTTPS destinations.
 *
 * @param mixed $value Setting value.
 * @return string
 */
function hdylb_sanitize_support_url( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$value = esc_url_raw( trim( (string) $value ), array( 'http', 'https' ) );
	$parts = wp_parse_url( $value );

	if ( ! $value || ! is_array( $parts ) || empty( $parts['host'] ) || empty( $parts['scheme'] ) ) {
		return '';
	}

	return in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ? $value : '';
}

/**
 * Applies a valid custom sender name when configured.
 *
 * @param string $from_name Current sender name.
 * @return string
 */
function hdylb_filter_mail_from_name( $from_name ) {
	$custom_name = hdylb_sanitize_sender_name( get_option( HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM_NAME, '' ) );

	return '' !== $custom_name ? $custom_name : $from_name;
}
add_filter( 'wp_mail_from_name', 'hdylb_filter_mail_from_name', 1 );

/**
 * Applies a valid custom sender address when configured.
 *
 * @param string $from_email Current sender address.
 * @return string
 */
function hdylb_filter_mail_from( $from_email ) {
	$custom_email = hdylb_sanitize_sender_email( get_option( HDY_LOGIN_BRANDING_OPTION_EMAIL_FROM, '' ) );

	return '' !== $custom_email ? $custom_email : $from_email;
}
add_filter( 'wp_mail_from', 'hdylb_filter_mail_from', 1 );

/**
 * Get roles eligible for Dashboard and Toolbar restrictions.
 *
 * @return array
 */
function hdylb_get_selectable_roles() {
	$roles      = function_exists( 'wp_roles' ) ? wp_roles()->roles : array();
	$selectable = array();
	foreach ( $roles as $slug => $role ) {
		if ( ! empty( $role['capabilities']['manage_options'] ) ) {
			continue;
		}
		$name                    = isset( $role['name'] ) ? $role['name'] : $slug;
		$selectable[ $slug ] = function_exists( 'translate_user_role' ) ? translate_user_role( $name ) : $name;
	}
	return $selectable;
}

/** Sanitize role selections against currently registered eligible roles.
 *
 * @param mixed $value Submitted roles.
 * @return array
 */
function hdylb_sanitize_role_list( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}
	$allowed = array_keys( hdylb_get_selectable_roles() );
	$roles   = array();
	foreach ( $value as $role ) {
		if ( is_string( $role ) ) {
			$role = sanitize_key( $role );
			if ( '' !== $role && in_array( $role, $allowed, true ) ) {
				$roles[] = $role;
			}
		}
	}
	return array_values( array_unique( $roles ) );
}

/** Return the default Dashboard denial message.
 *
 * @return string
 */
function hdylb_get_default_dashboard_denial_message() {
	return __( 'You do not have permission to access the WordPress Dashboard.', 'hdy-login-branding' );
}

/** Sanitize the Dashboard denial message.
 *
 * @param mixed $value Submitted message.
 * @return string
 */
function hdylb_sanitize_dashboard_denial_message( $value ) {
	if ( ! is_scalar( $value ) ) {
		return hdylb_get_default_dashboard_denial_message();
	}
	$value = sanitize_textarea_field( (string) $value );
	return '' !== $value ? $value : hdylb_get_default_dashboard_denial_message();
}

/** Get a safe configured Dashboard denial message.
 *
 * @return string
 */
function hdylb_get_dashboard_denial_message() {
	return hdylb_sanitize_dashboard_denial_message( get_option( HDY_LOGIN_BRANDING_OPTION_DASHBOARD_DENIAL_MESSAGE, hdylb_get_default_dashboard_denial_message() ) );
}

/** Determine whether the current user is exempt from restrictions.
 *
 * @return bool
 */
function hdylb_current_user_is_exempt() {
	return current_user_can( 'manage_options' ) || ( is_multisite() && is_super_admin() );
}

/** Check whether the current user has one of the selected roles.
 *
 * @param string $option_name Role option key.
 * @return bool
 */
function hdylb_current_user_has_restricted_role( $option_name ) {
	if ( ! is_user_logged_in() || hdylb_current_user_is_exempt() ) {
		return false;
	}
	$selected = (array) get_option( $option_name, array() );
	$user     = wp_get_current_user();
	return (bool) array_intersect( (array) $user->roles, $selected );
}

/** Block selected users from normal Dashboard screens.
 *
 * @return void
 */
function hdylb_restrict_dashboard_access() {
	if ( ! is_admin() || ! hdylb_current_user_has_restricted_role( HDY_LOGIN_BRANDING_OPTION_DASHBOARD_BLOCKED_ROLES ) ) {
		return;
	}
	global $pagenow;
	$allowed_pages = array( 'admin-ajax.php', 'admin-post.php', 'async-upload.php' );
	if ( ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) || ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) || in_array( $pagenow, $allowed_pages, true ) ) {
		return;
	}
	wp_die( esc_html( hdylb_get_dashboard_denial_message() ), esc_html__( 'Dashboard Access Restricted', 'hdy-login-branding' ), array( 'response' => 403 ) );
}
add_action( 'admin_init', 'hdylb_restrict_dashboard_access', 1 );

/** Hide the front-end Toolbar for selected roles.
 *
 * @param bool $show Whether WordPress intends to show the Toolbar.
 * @return bool
 */
function hdylb_filter_admin_bar_visibility( $show ) {
	return hdylb_current_user_has_restricted_role( HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES ) ? false : $show;
}
add_filter( 'show_admin_bar', 'hdylb_filter_admin_bar_visibility', 1 );

/** Remove backend Toolbar rendering for selected roles before core renders it.
 *
 * @return void
 */
function hdylb_remove_backend_admin_bar() {
	if ( hdylb_current_user_has_restricted_role( HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES ) ) {
		remove_action( 'in_admin_header', 'wp_admin_bar_render', 0 );
	}
}
add_action( 'in_admin_header', 'hdylb_remove_backend_admin_bar', -1 );

/** Clear the admin Toolbar's reserved spacing when it is hidden.
 *
 * @return void
 */
function hdylb_backend_admin_bar_spacing() {
	if ( ! hdylb_current_user_has_restricted_role( HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES ) ) {
		return;
	}
	echo '<style>html.wp-toolbar{padding-top:0!important}@media screen and (max-width:782px){#wpbody{padding-top:0!important}}</style>';
}
add_action( 'admin_head', 'hdylb_backend_admin_bar_spacing' );

/**
 * Adds plugin settings page.
 *
 * @return void
 */
function hdylb_add_settings_page() {
	add_options_page(
		esc_html__( 'HDY Login and Registration Branding', 'hdy-login-branding' ),
		esc_html__( 'HDY Login and Registration Branding', 'hdy-login-branding' ),
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

	if ( hdylb_get_support_link_url() ) {
		$css .= 'body.login p.hdylb-support-link{text-align:center;margin:16px auto 0;padding:0 24px;}body.login p.hdylb-support-link a{color:#50575e;text-decoration:underline;}body.login p.hdylb-support-link a:focus{color:#135e96;outline:2px solid #2271b1;outline-offset:2px;}';
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
 * Returns a validated support URL.
 *
 * @return string
 */
function hdylb_get_support_link_url() {
	return hdylb_sanitize_support_url( get_option( HDY_LOGIN_BRANDING_OPTION_SUPPORT_URL, '' ) );
}

/**
 * Prints the optional support link after the standard login footer links.
 *
 * @return void
 */
function hdylb_login_support_link() {
	$url = hdylb_get_support_link_url();
	if ( '' === $url ) {
		return;
	}

	$label = hdylb_sanitize_text( get_option( HDY_LOGIN_BRANDING_OPTION_SUPPORT_LABEL, 'Support' ) );
	if ( '' === $label ) {
		$label = __( 'Support', 'hdy-login-branding' );
	}
	$new_tab = (bool) hdylb_sanitize_enabled( get_option( HDY_LOGIN_BRANDING_OPTION_SUPPORT_NEW_TAB, 0 ) );
	?>
	<p class="hdylb-support-link"><a href="<?php echo esc_url( $url ); ?>"<?php echo $new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $label ); ?></a></p>
	<?php
}
add_action( 'login_footer', 'hdylb_login_support_link', 5 );

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
