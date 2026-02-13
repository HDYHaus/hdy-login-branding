<?php
/**
 * Plugin Name: HDY Custom Login Logo
 * Plugin URI: https://hdyhaus.com/wp-plugins
 * Description: Brand the WordPress login screen from Settings with a Media Library image toggle.
 * Version: 1.0.0
 * Author: HDY Haus
 * Author URI: https://hdyhaus.com
 * Text Domain: hdy-custom-login-logo
 */

defined('ABSPATH') || exit;

define('CUSTOM_LOGIN_LOGO_VERSION', '1.0.0');
define('CUSTOM_LOGIN_LOGO_SLUG', 'hdy-custom-login-logo');
define('CUSTOM_LOGIN_LOGO_OPTION_ENABLED', 'custom_login_logo_enabled');
define('CUSTOM_LOGIN_LOGO_OPTION_ID', 'custom_login_logo_id');
define('CUSTOM_LOGIN_LOGO_OPTION_BUTTON_COLOR', 'custom_login_logo_button_color');
define('CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT', 'custom_login_logo_button_text');
define('CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT_COLOR', 'custom_login_logo_button_text_color');

function custom_login_logo_register_settings() {
    register_setting(
        CUSTOM_LOGIN_LOGO_SLUG,
        CUSTOM_LOGIN_LOGO_OPTION_ENABLED,
        [
            'type' => 'boolean',
            'sanitize_callback' => 'custom_login_logo_sanitize_enabled',
            'default' => 0,
        ]
    );

    register_setting(
        CUSTOM_LOGIN_LOGO_SLUG,
        CUSTOM_LOGIN_LOGO_OPTION_ID,
        [
            'type' => 'integer',
            'sanitize_callback' => 'custom_login_logo_sanitize_logo_id',
            'default' => 0,
        ]
    );

    register_setting(
        CUSTOM_LOGIN_LOGO_SLUG,
        CUSTOM_LOGIN_LOGO_OPTION_BUTTON_COLOR,
        [
            'type' => 'string',
            'sanitize_callback' => 'custom_login_logo_sanitize_button_color',
            'default' => '',
        ]
    );

    register_setting(
        CUSTOM_LOGIN_LOGO_SLUG,
        CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT,
        [
            'type' => 'string',
            'sanitize_callback' => 'custom_login_logo_sanitize_button_text',
            'default' => '',
        ]
    );

    register_setting(
        CUSTOM_LOGIN_LOGO_SLUG,
        CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT_COLOR,
        [
            'type' => 'string',
            'sanitize_callback' => 'custom_login_logo_sanitize_button_color',
            'default' => '',
        ]
    );
}
add_action('admin_init', 'custom_login_logo_register_settings');

function custom_login_logo_sanitize_enabled($value) {
    return $value ? 1 : 0;
}

function custom_login_logo_sanitize_logo_id($value) {
    $value = absint($value);
    if (!$value) {
        return 0;
    }

    if (!wp_attachment_is_image($value)) {
        return 0;
    }

    return $value;
}

function custom_login_logo_sanitize_button_color($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $value = sanitize_hex_color($value);

    return $value ? $value : '';
}

function custom_login_logo_sanitize_button_text($value) {
    return sanitize_text_field($value);
}

function custom_login_logo_add_settings_page() {
    $hook = add_options_page(
        __('HDY Custom Login Logo', 'hdy-custom-login-logo'),
        __('HDY Login Logo', 'hdy-custom-login-logo'),
        'manage_options',
        CUSTOM_LOGIN_LOGO_SLUG,
        'custom_login_logo_render_settings_page'
    );

    $GLOBALS['custom_login_logo_settings_page'] = $hook;
}
add_action('admin_menu', 'custom_login_logo_add_settings_page');

function custom_login_logo_admin_assets($hook) {
    if (empty($GLOBALS['custom_login_logo_settings_page']) || $hook !== $GLOBALS['custom_login_logo_settings_page']) {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_style('wp-color-picker');

    wp_enqueue_style(
        'custom-login-logo-admin',
        plugin_dir_url(__FILE__) . 'assets/admin.css',
        [],
        CUSTOM_LOGIN_LOGO_VERSION
    );

    wp_enqueue_script(
        'custom-login-logo-admin',
        plugin_dir_url(__FILE__) . 'assets/admin.js',
        ['jquery', 'wp-color-picker'],
        CUSTOM_LOGIN_LOGO_VERSION,
        true
    );

    wp_localize_script(
        'custom-login-logo-admin',
        'customLoginLogo',
        [
            'title' => esc_html__('Select Login Logo', 'hdy-custom-login-logo'),
            'button' => esc_html__('Use this logo', 'hdy-custom-login-logo'),
        ]
    );
}
add_action('admin_enqueue_scripts', 'custom_login_logo_admin_assets');

function custom_login_logo_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $enabled = (int) get_option(CUSTOM_LOGIN_LOGO_OPTION_ENABLED, 0);
    $logo_id = (int) get_option(CUSTOM_LOGIN_LOGO_OPTION_ID, 0);
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
    $preview_class = $logo_url ? 'is-set' : 'is-empty';
    $button_color = (string) get_option(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_COLOR, '');
    $button_text = (string) get_option(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT, '');
    $button_text_color = (string) get_option(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT_COLOR, '');
    $theme_logo_id = (int) get_theme_mod('custom_logo');
    $theme_logo_url = $theme_logo_id ? wp_get_attachment_image_url($theme_logo_id, 'full') : '';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('HDY Custom Login Logo', 'hdy-custom-login-logo'); ?></h1>
        <?php if ($enabled && !$logo_id) : ?>
            <div class="notice notice-warning">
                <p>
                    <?php
                    if ($theme_logo_url) {
                        echo esc_html__('Custom logo is enabled, but no image is selected. Your theme logo will be used until you choose a logo.', 'hdy-custom-login-logo');
                    } else {
                        echo esc_html__('Custom logo is enabled, but no image is selected. The default WordPress logo will remain until you choose a logo.', 'hdy-custom-login-logo');
                    }
                    ?>
                </p>
            </div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php settings_fields(CUSTOM_LOGIN_LOGO_SLUG); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php echo esc_html__('Enable custom logo', 'hdy-custom-login-logo'); ?></th>
                    <td>
                        <label>
                            <input
                                type="hidden"
                                name="<?php echo esc_attr(CUSTOM_LOGIN_LOGO_OPTION_ENABLED); ?>"
                                value="0"
                            >
                            <input
                                type="checkbox"
                                name="<?php echo esc_attr(CUSTOM_LOGIN_LOGO_OPTION_ENABLED); ?>"
                                value="1"
                                <?php checked(1, $enabled); ?>
                            >
                            <?php echo esc_html__('Replace the login logo with your selected image.', 'hdy-custom-login-logo'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Login logo', 'hdy-custom-login-logo'); ?></th>
                    <td>
                        <input
                            type="hidden"
                            id="custom-login-logo-id"
                            name="<?php echo esc_attr(CUSTOM_LOGIN_LOGO_OPTION_ID); ?>"
                            value="<?php echo esc_attr($logo_id); ?>"
                        >
                        <div class="custom-login-logo-actions">
                            <button type="button" class="button" id="custom-login-logo-select">
                                <?php echo esc_html__('Select logo', 'hdy-custom-login-logo'); ?>
                            </button>
                            <button type="button" class="button" id="custom-login-logo-remove" <?php disabled(0, $logo_id); ?>>
                                <?php echo esc_html__('Remove logo', 'hdy-custom-login-logo'); ?>
                            </button>
                        </div>
                        <div class="custom-login-logo-preview <?php echo esc_attr($preview_class); ?>">
                            <img
                                id="custom-login-logo-preview"
                                src="<?php echo esc_url($logo_url); ?>"
                                alt="<?php echo esc_attr__('Login logo preview', 'hdy-custom-login-logo'); ?>"
                            >
                            <p class="custom-login-logo-placeholder">
                                <?php echo esc_html__('No logo selected.', 'hdy-custom-login-logo'); ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Login button text', 'hdy-custom-login-logo'); ?></th>
                    <td>
                        <input
                            type="text"
                            class="regular-text"
                            name="<?php echo esc_attr(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT); ?>"
                            value="<?php echo esc_attr($button_text); ?>"
                        >
                        <p class="description">
                            <?php echo esc_html__('Leave blank to use the default "Log In" label.', 'hdy-custom-login-logo'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Login button background color', 'hdy-custom-login-logo'); ?></th>
                    <td>
                        <input
                            type="text"
                            class="custom-login-logo-color"
                            name="<?php echo esc_attr(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_COLOR); ?>"
                            value="<?php echo esc_attr($button_color); ?>"
                            data-default-color="#2271b1"
                        >
                        <p class="description">
                            <?php echo esc_html__('Leave blank to keep the default WordPress button color.', 'hdy-custom-login-logo'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html__('Login button text color', 'hdy-custom-login-logo'); ?></th>
                    <td>
                        <input
                            type="text"
                            class="custom-login-logo-color"
                            name="<?php echo esc_attr(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT_COLOR); ?>"
                            value="<?php echo esc_attr($button_text_color); ?>"
                            data-default-color="#ffffff"
                        >
                        <p class="description">
                            <?php echo esc_html__('Leave blank to keep the default button text color.', 'hdy-custom-login-logo'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function custom_login_logo_login_styles() {
    $css = '';

    $enabled = (int) get_option(CUSTOM_LOGIN_LOGO_OPTION_ENABLED, 0);
    if ($enabled) {
        $logo_id = (int) get_option(CUSTOM_LOGIN_LOGO_OPTION_ID, 0);
        $logo_url = '';

        if ($logo_id) {
            $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        } else {
            $theme_logo_id = (int) get_theme_mod('custom_logo');
            if ($theme_logo_id) {
                $logo_url = wp_get_attachment_image_url($theme_logo_id, 'full');
            }
        }

        if ($logo_url) {
            $css .= sprintf(
                '#login h1 a{background:url("%s") center/contain no-repeat !important;width:100%%;max-width:320px;height:120px;margin:0 auto 25px;display:block;overflow:hidden;text-indent:-9999px;}',
                esc_url($logo_url)
            );
        }
    }

    $button_color = trim((string) get_option(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_COLOR, ''));
    if ($button_color !== '') {
        $button_color = sanitize_hex_color($button_color);
        if ($button_color) {
            $button_color = esc_attr($button_color);
            $css .= sprintf(
                '#login #wp-submit{background-color:%1$s;border-color:%1$s;box-shadow:0 1px 0 %1$s;}#login #wp-submit:hover,#login #wp-submit:focus{filter:brightness(0.92);}',
                $button_color
            );
        }
    }

    $button_text_color = trim((string) get_option(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT_COLOR, ''));
    if ($button_text_color !== '') {
        $button_text_color = sanitize_hex_color($button_text_color);
        if ($button_text_color) {
            $button_text_color = esc_attr($button_text_color);
            $css .= sprintf(
                '#login #wp-submit{color:%1$s;}',
                $button_text_color
            );
        }
    }

    if ($css !== '') {
        wp_add_inline_style('login', $css);
    }

    $button_text = trim((string) get_option(CUSTOM_LOGIN_LOGO_OPTION_BUTTON_TEXT, ''));
    if ($button_text !== '') {
        $script = 'document.addEventListener("DOMContentLoaded",function(){var btn=document.getElementById("wp-submit");if(btn){btn.value=' . wp_json_encode($button_text) . ';}});';

        wp_register_script('custom-login-logo-login', '', [], CUSTOM_LOGIN_LOGO_VERSION, true);
        wp_enqueue_script('custom-login-logo-login');
        wp_add_inline_script('custom-login-logo-login', $script);
    }
}
add_action('login_enqueue_scripts', 'custom_login_logo_login_styles');

function custom_login_logo_header_url($url) {
    $enabled = (int) get_option(CUSTOM_LOGIN_LOGO_OPTION_ENABLED, 0);
    if (!$enabled) {
        return $url;
    }

    $logo_id = (int) get_option(CUSTOM_LOGIN_LOGO_OPTION_ID, 0);
    if (!$logo_id) {
        return $url;
    }

    return home_url('/');
}
add_filter('login_headerurl', 'custom_login_logo_header_url');

function custom_login_logo_plugin_row_meta($links, $file) {
    if ($file !== plugin_basename(__FILE__)) {
        return $links;
    }

    $links[] = sprintf(
        '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
        esc_url('https://hdyhaus.com/wp-plugins'),
        esc_html__('View details', 'hdy-custom-login-logo')
    );

    return $links;
}
add_filter('plugin_row_meta', 'custom_login_logo_plugin_row_meta', 10, 2);
