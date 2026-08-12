<?php
/**
 * Plugin Name: HDY Login Branding
 * Plugin URI: https://hdyhaus.com/wp-plugins
 * Description: Brand the WordPress login screen from Settings with a Media Library image toggle.
 * Version: 1.0.2
 * Author: HDY Haus
 * Author URI: https://hdyhaus.com
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: hdy-login-branding
 *
 * @package HDYLoginBranding
 */

defined( 'ABSPATH' ) || exit;

define( 'HDY_LOGIN_BRANDING_VERSION', '1.0.2' );
define( 'HDY_LOGIN_BRANDING_SLUG', 'hdy-login-branding' );
define( 'HDY_LOGIN_BRANDING_OPTION_ENABLED', 'hdylb_enabled' );
define( 'HDY_LOGIN_BRANDING_OPTION_ID', 'hdylb_id' );
define( 'HDY_LOGIN_BRANDING_OPTION_BUTTON_COLOR', 'hdylb_button_color' );
define( 'HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT', 'hdylb_button_text' );
define( 'HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT_COLOR', 'hdylb_button_text_color' );
define( 'HDY_LOGIN_BRANDING_OPTION_BACKGROUND_COLOR', 'hdylb_background_color' );

/**
 * Registers plugin settings.
 *
 * @return void
 */
function hdylb_register_settings() {
	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_ENABLED,
		[
			'type'              => 'boolean',
			'sanitize_callback' => 'hdylb_sanitize_enabled',
			'default'           => 0,
		]
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_ID,
		[
			'type'              => 'integer',
			'sanitize_callback' => 'hdylb_sanitize_logo_id',
			'default'           => 0,
		]
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BUTTON_COLOR,
		[
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_color',
			'default'           => '',
		]
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT,
		[
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_text',
			'default'           => '',
		]
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT_COLOR,
		[
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_color',
			'default'           => '',
		]
	);

	register_setting(
		HDY_LOGIN_BRANDING_SLUG,
		HDY_LOGIN_BRANDING_OPTION_BACKGROUND_COLOR,
		[
			'type'              => 'string',
			'sanitize_callback' => 'hdylb_sanitize_button_color',
			'default'           => '',
		]
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
	return sanitize_text_field( $value );
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
		[],
		HDY_LOGIN_BRANDING_VERSION
	);

	wp_enqueue_script(
		'hdy-login-branding-admin',
		plugin_dir_url( __FILE__ ) . 'assets/admin.js',
		[ 'jquery', 'wp-color-picker' ],
		HDY_LOGIN_BRANDING_VERSION,
		true
	);

	wp_localize_script(
		'hdy-login-branding-admin',
		'hdyLoginBranding',
		[
			'title'  => esc_html__( 'Select Login Logo', 'hdy-login-branding' ),
			'button' => esc_html__( 'Use this logo', 'hdy-login-branding' ),
		]
	);
}
add_action( 'admin_enqueue_scripts', 'hdylb_admin_assets' );

/**
 * Renders plugin settings page.
 *
 * @return void
 */
function hdylb_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$enabled           = (int) get_option( HDY_LOGIN_BRANDING_OPTION_ENABLED, 0 );
	$logo_id           = (int) get_option( HDY_LOGIN_BRANDING_OPTION_ID, 0 );
	$logo_url          = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
	$preview_class     = $logo_url ? 'is-set' : 'is-empty';
	$button_color      = (string) get_option( HDY_LOGIN_BRANDING_OPTION_BUTTON_COLOR, '' );
	$button_text       = (string) get_option( HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT, '' );
	$button_text_color = (string) get_option( HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT_COLOR, '' );
	$background_color  = (string) get_option( HDY_LOGIN_BRANDING_OPTION_BACKGROUND_COLOR, '' );
	$theme_logo_id     = (int) get_theme_mod( 'custom_logo' );
	$theme_logo_url    = $theme_logo_id ? wp_get_attachment_image_url( $theme_logo_id, 'full' ) : '';
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'HDY Login Branding', 'hdy-login-branding' ); ?></h1>
		<?php if ( $enabled && ! $logo_id ) : ?>
			<div class="notice notice-warning">
				<p>
					<?php
					if ( $theme_logo_url ) {
						echo esc_html__( 'Custom logo is enabled, but no image is selected. Your theme logo will be used until you choose a logo.', 'hdy-login-branding' );
					} else {
						echo esc_html__( 'Custom logo is enabled, but no image is selected. The default WordPress logo will remain until you choose a logo.', 'hdy-login-branding' );
					}
					?>
				</p>
			</div>
		<?php endif; ?>
		<form method="post" action="options.php">
			<?php settings_fields( HDY_LOGIN_BRANDING_SLUG ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php echo esc_html__( 'Enable custom logo', 'hdy-login-branding' ); ?></th>
					<td>
						<label>
							<input
								type="hidden"
								name="<?php echo esc_attr( HDY_LOGIN_BRANDING_OPTION_ENABLED ); ?>"
								value="0"
							>
							<input
								type="checkbox"
								name="<?php echo esc_attr( HDY_LOGIN_BRANDING_OPTION_ENABLED ); ?>"
								value="1"
								<?php checked( 1, $enabled ); ?>
							>
							<?php echo esc_html__( 'Replace the login logo with your selected image.', 'hdy-login-branding' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Login logo', 'hdy-login-branding' ); ?></th>
					<td>
						<input
							type="hidden"
							id="hdy-login-branding-id"
							name="<?php echo esc_attr( HDY_LOGIN_BRANDING_OPTION_ID ); ?>"
							value="<?php echo esc_attr( $logo_id ); ?>"
						>
						<div class="hdy-login-branding-actions">
							<button type="button" class="button" id="hdy-login-branding-select">
								<?php echo esc_html__( 'Select logo', 'hdy-login-branding' ); ?>
							</button>
							<button type="button" class="button" id="hdy-login-branding-remove" <?php disabled( 0, $logo_id ); ?>>
								<?php echo esc_html__( 'Remove logo', 'hdy-login-branding' ); ?>
							</button>
						</div>
						<div class="hdy-login-branding-preview <?php echo esc_attr( $preview_class ); ?>">
							<img
								id="hdy-login-branding-preview"
								src="<?php echo esc_url( $logo_url ); ?>"
								alt="<?php echo esc_attr__( 'Login logo preview', 'hdy-login-branding' ); ?>"
							>
							<p class="hdy-login-branding-placeholder">
								<?php echo esc_html__( 'No logo selected.', 'hdy-login-branding' ); ?>
							</p>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Login page background color', 'hdy-login-branding' ); ?></th>
					<td>
						<input
							type="text"
							class="hdy-login-branding-color"
							name="<?php echo esc_attr( HDY_LOGIN_BRANDING_OPTION_BACKGROUND_COLOR ); ?>"
							value="<?php echo esc_attr( $background_color ); ?>"
							data-default-color="#f0f0f1"
						>
						<p class="description">
							<?php echo esc_html__( 'Leave blank to keep the default WordPress login background.', 'hdy-login-branding' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Login button text', 'hdy-login-branding' ); ?></th>
					<td>
						<input
							type="text"
							class="regular-text"
							name="<?php echo esc_attr( HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT ); ?>"
							value="<?php echo esc_attr( $button_text ); ?>"
						>
						<p class="description">
							<?php echo esc_html__( 'Leave blank to use the default "Log In" label.', 'hdy-login-branding' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Login button background color', 'hdy-login-branding' ); ?></th>
					<td>
						<input
							type="text"
							class="hdy-login-branding-color"
							name="<?php echo esc_attr( HDY_LOGIN_BRANDING_OPTION_BUTTON_COLOR ); ?>"
							value="<?php echo esc_attr( $button_color ); ?>"
							data-default-color="#2271b1"
						>
						<p class="description">
							<?php echo esc_html__( 'Leave blank to keep the default WordPress button color.', 'hdy-login-branding' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Login button text color', 'hdy-login-branding' ); ?></th>
					<td>
						<input
							type="text"
							class="hdy-login-branding-color"
							name="<?php echo esc_attr( HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT_COLOR ); ?>"
							value="<?php echo esc_attr( $button_text_color ); ?>"
							data-default-color="#ffffff"
						>
						<p class="description">
							<?php echo esc_html__( 'Leave blank to keep the default button text color.', 'hdy-login-branding' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

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

	$button_color = trim( (string) get_option( HDY_LOGIN_BRANDING_OPTION_BUTTON_COLOR, '' ) );

	if ( '' !== $button_color ) {
		$button_color = sanitize_hex_color( $button_color );

		if ( $button_color ) {
			$css .= sprintf(
				'#login #wp-submit{background-color:%1$s;border-color:%1$s;box-shadow:0 1px 0 %1$s;}#login #wp-submit:hover,#login #wp-submit:focus{filter:brightness(0.92);}',
				$button_color
			);
		}
	}

	$button_text_color = trim( (string) get_option( HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT_COLOR, '' ) );

	if ( '' !== $button_text_color ) {
		$button_text_color = sanitize_hex_color( $button_text_color );

		if ( $button_text_color ) {
			$css .= sprintf(
				'#login #wp-submit{color:%1$s;}',
				$button_text_color
			);
		}
	}

	$background_color = trim( (string) get_option( HDY_LOGIN_BRANDING_OPTION_BACKGROUND_COLOR, '' ) );

	if ( '' !== $background_color ) {
		$background_color = sanitize_hex_color( $background_color );

		if ( $background_color ) {
			$css .= sprintf(
				'body.login{background-color:%1$s;background-image:none;}',
				$background_color
			);
		}
	}

	if ( '' !== $css ) {
		wp_add_inline_style( 'login', $css );
	}

	$button_text = sanitize_text_field( (string) get_option( HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT, '' ) );
	$button_text = trim( $button_text );

	if ( '' !== $button_text ) {
		$script = 'document.addEventListener("DOMContentLoaded",function(){var btn=document.getElementById("wp-submit");if(btn){btn.value=' . wp_json_encode( $button_text ) . ';}});';

		wp_register_script( 'hdy-login-branding-login', '', [], HDY_LOGIN_BRANDING_VERSION, true );
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
 * Adds plugin row meta links.
 *
 * @param array  $links Existing plugin row links.
 * @param string $file  Plugin basename being filtered.
 * @return array
 */
function hdylb_plugin_row_meta( $links, $file ) {
	if ( plugin_basename( __FILE__ ) !== $file ) {
		return $links;
	}

	$links[] = sprintf(
		'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
		esc_url( 'https://hdyhaus.com/wp-plugins' ),
		esc_html__( 'View details', 'hdy-login-branding' )
	);

	return $links;
}
add_filter( 'plugin_row_meta', 'hdylb_plugin_row_meta', 10, 2 );
