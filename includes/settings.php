<?php
/**
 * Branding settings and shared color resolution.
 *
 * @package HDYLoginBranding
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve per-flow overrides without changing the saved shared defaults.
 *
 * @param string $flow Authentication action.
 * @return array
 */
function hdylb_get_flow_colors( $flow ) {
	$flow     = strtr(
		$flow,
		array(
			'retrievepassword' => 'lostpassword',
			'rp'               => 'resetpass',
		)
	);
	$override = in_array( $flow, array( 'login', 'register', 'lostpassword', 'resetpass' ), true )
		&& ! get_option( 'hdylb_' . $flow . '_shared', 1 );
	$colors   = array();
	foreach ( array( 'background_color', 'button_color', 'button_text_color' ) as $key ) {
		$shared         = hdylb_sanitize_button_color( get_option( 'hdylb_' . $key, '' ) );
		$custom         = $override ? hdylb_sanitize_button_color( get_option( 'hdylb_' . $flow . '_' . $key, '' ) ) : '';
		$colors[ $key ] = $custom ? $custom : $shared;
	}
	return $colors;
}

/**
 * Render a labelled text setting.
 *
 * @param string $name Option name.
 * @param string $label Visible label.
 * @param string $help Field description.
 * @param bool   $multiline Whether to render a textarea.
 * @return void
 */
function hdylb_text_control( $name, $label, $help = '', $multiline = false ) {
	$value = get_option( $name, '' );
	?>
	<div class="hdylb-field">
		<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php if ( $multiline ) : ?>
			<textarea id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3" aria-describedby="<?php echo esc_attr( $name . '-help' ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
		<?php else : ?>
			<input type="text" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" aria-describedby="<?php echo esc_attr( $name . '-help' ); ?>">
		<?php endif; ?>
		<p class="description" id="<?php echo esc_attr( $name . '-help' ); ?>"><?php echo esc_html( $help ); ?></p>
	</div>
	<?php
}

/**
 * Render a flow's color controls.
 *
 * @param string $flow Shared or an authentication action.
 * @return void
 */
function hdylb_color_controls( $flow ) {
	$colors = array(
		'background_color'  => __( 'Page background', 'hdy-login-branding' ),
		'button_color'      => __( 'Button background', 'hdy-login-branding' ),
		'button_text_color' => __( 'Button text color', 'hdy-login-branding' ),
	);
	$prefix = 'shared' === $flow ? 'hdylb_' : 'hdylb_' . $flow . '_';
	?>
	<fieldset class="hdylb-colors" data-flow="<?php echo esc_attr( $flow ); ?>">
		<legend><?php esc_html_e( 'Colors', 'hdy-login-branding' ); ?></legend>
		<?php if ( 'shared' !== $flow ) : ?>
			<label class="hdylb-inherit-label">
				<input type="hidden" name="<?php echo esc_attr( $prefix . 'shared' ); ?>" value="0">
				<input class="hdylb-inherit" type="checkbox" name="<?php echo esc_attr( $prefix . 'shared' ); ?>" value="1" aria-controls="<?php echo esc_attr( $prefix . 'colors' ); ?>" <?php checked( 1, get_option( $prefix . 'shared', 1 ) ); ?>>
				<?php esc_html_e( 'Use shared colors', 'hdy-login-branding' ); ?>
			</label>
		<?php endif; ?>
		<div class="hdylb-color-fields" id="<?php echo esc_attr( $prefix . 'colors' ); ?>">
			<?php foreach ( $colors as $key => $label ) : ?>
				<div class="hdylb-field">
					<label for="<?php echo esc_attr( $prefix . $key ); ?>"><?php echo esc_html( $label ); ?></label>
					<input class="hdy-login-branding-color" type="text" id="<?php echo esc_attr( $prefix . $key ); ?>" name="<?php echo esc_attr( $prefix . $key ); ?>" value="<?php echo esc_attr( get_option( $prefix . $key, '' ) ); ?>" aria-describedby="<?php echo esc_attr( $prefix . 'color-help' ); ?>">
				</div>
			<?php endforeach; ?>
			<p class="description" id="<?php echo esc_attr( $prefix . 'color-help' ); ?>">
				<?php echo 'shared' === $flow ? esc_html__( 'Blank colors use WordPress defaults.', 'hdy-login-branding' ) : esc_html__( 'Blank colors inherit the shared color. Saved overrides are retained when shared colors are enabled.', 'hdy-login-branding' ); ?>
			</p>
		</div>
	</fieldset>
	<?php
}

/**
 * Render the single settings form. JavaScript progressively enhances its panels.
 *
 * @return void
 */
function hdylb_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$logo_id    = (int) get_option( HDY_LOGIN_BRANDING_OPTION_ID, 0 );
	$logo_url   = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
	$theme_logo = wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' );
	$tabs       = array(
		'shared'   => __( 'Shared Branding', 'hdy-login-branding' ),
		'login'    => __( 'Login', 'hdy-login-branding' ),
		'register' => __( 'Registration', 'hdy-login-branding' ),
		'recovery' => __( 'Password Recovery', 'hdy-login-branding' ),
	);
	$blank_help = __( 'Leave blank to keep the WordPress default.', 'hdy-login-branding' );
	?>
	<div class="wrap hdylb-settings">
		<h1><?php esc_html_e( 'HDY Login Branding', 'hdy-login-branding' ); ?></h1>
		<nav class="hdylb-tabs" aria-label="<?php esc_attr_e( 'Branding settings', 'hdy-login-branding' ); ?>">
			<?php foreach ( $tabs as $key => $label ) : ?>
				<a id="<?php echo esc_attr( 'hdylb-tab-' . $key ); ?>" href="<?php echo esc_attr( '#hdylb-panel-' . $key ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<form method="post" action="options.php" id="hdylb-form">
			<?php settings_fields( HDY_LOGIN_BRANDING_SLUG ); ?>
			<div class="hdylb-layout">
				<div class="hdylb-controls">
					<section id="hdylb-panel-shared" class="hdylb-panel" aria-labelledby="hdylb-tab-shared">
						<h2><?php esc_html_e( 'Shared Branding', 'hdy-login-branding' ); ?></h2>
						<p><?php esc_html_e( 'Default appearance for login, registration, and password recovery.', 'hdy-login-branding' ); ?></p>
						<div class="hdylb-field">
							<label><input type="hidden" name="hdylb_enabled" value="0"><input type="checkbox" name="hdylb_enabled" value="1" <?php checked( 1, get_option( HDY_LOGIN_BRANDING_OPTION_ENABLED, 0 ) ); ?>> <?php esc_html_e( 'Use custom logo', 'hdy-login-branding' ); ?></label>
							<input type="hidden" id="hdy-login-branding-id" name="hdylb_id" value="<?php echo esc_attr( $logo_id ); ?>">
							<div class="hdy-login-branding-actions">
								<button type="button" class="button" id="hdy-login-branding-select"><?php esc_html_e( 'Select logo', 'hdy-login-branding' ); ?></button>
								<button type="button" class="button" id="hdy-login-branding-remove" <?php disabled( 0, $logo_id ); ?>><?php esc_html_e( 'Remove logo', 'hdy-login-branding' ); ?></button>
							</div>
							<div class="hdy-login-branding-preview <?php echo $logo_url ? 'is-set' : 'is-empty'; ?>">
								<img id="hdy-login-branding-preview" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'Selected logo', 'hdy-login-branding' ); ?>">
								<p class="hdy-login-branding-placeholder"><?php esc_html_e( 'No logo selected.', 'hdy-login-branding' ); ?></p>
							</div>
							<p class="description"><?php echo $theme_logo ? esc_html__( 'With no image selected, the enabled custom logo uses your theme logo.', 'hdy-login-branding' ) : esc_html__( 'With no image selected, the WordPress logo remains.', 'hdy-login-branding' ); ?></p>
						</div>
						<?php hdylb_color_controls( 'shared' ); ?>
					</section>
					<section id="hdylb-panel-login" class="hdylb-panel" aria-labelledby="hdylb-tab-login">
						<h2><?php esc_html_e( 'Login', 'hdy-login-branding' ); ?></h2>
						<?php
						hdylb_text_control( HDY_LOGIN_BRANDING_OPTION_BUTTON_TEXT, __( 'Login button label', 'hdy-login-branding' ), $blank_help );
						hdylb_color_controls( 'login' );
						?>
					</section>
					<section id="hdylb-panel-register" class="hdylb-panel" aria-labelledby="hdylb-tab-register">
						<h2><?php esc_html_e( 'Registration', 'hdy-login-branding' ); ?></h2>
						<?php
						hdylb_text_control( HDY_LOGIN_BRANDING_OPTION_REGISTRATION_HEADING, __( 'Registration heading', 'hdy-login-branding' ), $blank_help );
						hdylb_text_control( HDY_LOGIN_BRANDING_OPTION_REGISTRATION_BUTTON_TEXT, __( 'Register button label', 'hdy-login-branding' ), $blank_help );
						hdylb_color_controls( 'register' );
						?>
					</section>
					<section id="hdylb-panel-recovery" class="hdylb-panel" aria-labelledby="hdylb-tab-recovery">
						<h2><?php esc_html_e( 'Password Recovery', 'hdy-login-branding' ); ?></h2>
						<div data-preview-flow="lostpassword" class="hdylb-recovery-group">
							<h3><?php esc_html_e( 'Request a reset link', 'hdy-login-branding' ); ?></h3>
							<?php
							hdylb_text_control( HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_MESSAGE, __( 'Lost-password instructions', 'hdy-login-branding' ), $blank_help, true );
							hdylb_text_control( HDY_LOGIN_BRANDING_OPTION_LOST_PASSWORD_BUTTON_TEXT, __( 'Lost-password button label', 'hdy-login-branding' ), $blank_help );
							hdylb_color_controls( 'lostpassword' );
							?>
						</div>
						<div data-preview-flow="resetpass" class="hdylb-recovery-group">
							<h3><?php esc_html_e( 'Set a new password', 'hdy-login-branding' ); ?></h3>
							<?php
							hdylb_text_control( HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_MESSAGE, __( 'Reset-password instructions', 'hdy-login-branding' ), $blank_help, true );
							hdylb_text_control( HDY_LOGIN_BRANDING_OPTION_RESET_PASSWORD_BUTTON_TEXT, __( 'Reset-password button label', 'hdy-login-branding' ), $blank_help );
							hdylb_color_controls( 'resetpass' );
							?>
						</div>
					</section>
				</div>
				<aside class="hdylb-preview" hidden aria-labelledby="hdylb-preview-title" data-theme-logo="<?php echo esc_url( $theme_logo ); ?>" data-default-logo="<?php echo esc_url( admin_url( 'images/wordpress-logo.svg' ) ); ?>">
					<h2 id="hdylb-preview-title"><?php esc_html_e( 'Preview', 'hdy-login-branding' ); ?></h2>
					<label for="hdylb-preview-flow"><?php esc_html_e( 'Preview page', 'hdy-login-branding' ); ?></label>
					<select id="hdylb-preview-flow">
						<option value="login"><?php esc_html_e( 'Login', 'hdy-login-branding' ); ?></option>
						<option value="register"><?php esc_html_e( 'Registration', 'hdy-login-branding' ); ?></option>
						<option value="lostpassword"><?php esc_html_e( 'Lost password', 'hdy-login-branding' ); ?></option>
						<option value="resetpass"><?php esc_html_e( 'Reset password', 'hdy-login-branding' ); ?></option>
					</select>
					<div class="hdylb-preview-stage" aria-hidden="true">
						<img class="hdylb-preview-logo" alt="">
						<p class="hdylb-preview-message"></p>
						<div class="hdylb-preview-form">
							<span class="hdylb-preview-label"></span><div class="hdylb-preview-input"></div>
							<div class="hdylb-preview-second"><span><?php esc_html_e( 'Password', 'hdy-login-branding' ); ?></span><div class="hdylb-preview-input"></div></div>
							<span class="hdylb-preview-button"></span>
						</div>
					</div>
					<p id="hdylb-contrast" role="status" aria-live="polite"></p>
				</aside>
			</div>
			<div class="hdylb-save">
				<?php submit_button( __( 'Save all changes', 'hdy-login-branding' ), 'primary', 'submit', false ); ?>
				<span id="hdylb-save-state" role="status"></span>
			</div>
		</form>
	</div>
	<?php
}
