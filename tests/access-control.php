<?php
require __DIR__ . '/bootstrap.php';
function access_expect( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		throw new RuntimeException( $message . ': ' . var_export( $actual, true ) );
	}
}
$test_roles = array(
	'subscriber' => array( 'name' => 'Subscriber', 'capabilities' => array( 'read' => true ) ),
	'editor' => array( 'name' => 'Editor', 'capabilities' => array( 'read' => true ) ),
	'custom_member' => array( 'name' => 'Custom Member', 'capabilities' => array( 'read' => true ) ),
	'site_admin' => array( 'name' => 'Site Admin', 'capabilities' => array( 'manage_options' => true ) ),
);
access_expect( array( 'subscriber', 'editor', 'custom_member' ), array_keys( hdylb_get_selectable_roles() ), 'Eligible built-in and plugin roles appear, manage_options role is omitted' );
access_expect( array( 'subscriber', 'custom_member' ), hdylb_sanitize_role_list( array( 'subscriber', 'custom_member', 'site_admin', 'missing', 'subscriber' ) ), 'Role list sanitizes, validates, and deduplicates' );
access_expect( array(), hdylb_sanitize_role_list( '' ), 'Invalid role selection becomes empty' );
access_expect( hdylb_get_default_dashboard_denial_message(), hdylb_sanitize_dashboard_denial_message( ' ' ), 'Blank denial message returns translated default' );
hdylb_register_settings();
access_expect( array(), $test_registered[ HDY_LOGIN_BRANDING_OPTION_DASHBOARD_BLOCKED_ROLES ]['default'], 'Role option defaults to no restrictions' );

$GLOBALS['test_capabilities']['manage_options'] = false;
$test_user = (object) array( 'roles' => array( 'subscriber' ) );
$test_options[ HDY_LOGIN_BRANDING_OPTION_DASHBOARD_BLOCKED_ROLES ] = array( 'subscriber' );
$test_is_admin = true;
$pagenow = 'index.php';
try {
	hdylb_restrict_dashboard_access();
	throw new RuntimeException( 'Selected role was not denied' );
} catch ( RuntimeException $error ) {
	if ( false === strpos( $error->getMessage(), 'wp_die:' ) ) { throw $error; }
	$args = json_decode( substr( $error->getMessage(), strlen( 'wp_die:' ) ), true );
	access_expect( 403, $args[2]['response'], 'Dashboard denial uses HTTP 403' );
}
$test_options[ HDY_LOGIN_BRANDING_OPTION_DASHBOARD_BLOCKED_ROLES ] = array( 'editor' );
hdylb_restrict_dashboard_access();
$test_options[ HDY_LOGIN_BRANDING_OPTION_DASHBOARD_BLOCKED_ROLES ] = array( 'subscriber' );
$test_ajax = true;
hdylb_restrict_dashboard_access();
$test_ajax = false;
$test_user->roles = array( 'subscriber', 'editor' );
$test_multisite = true;
$test_super_admin = true;
hdylb_restrict_dashboard_access();
$test_multisite = false;
$test_super_admin = false;
$test_capabilities['manage_options'] = true;
hdylb_restrict_dashboard_access();
$test_capabilities['manage_options'] = false;

$test_user->roles = array( 'subscriber', 'custom_member' );
$test_options[ HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES ] = array( 'custom_member' );
access_expect( false, hdylb_filter_admin_bar_visibility( true ), 'Toolbar is hidden for any selected role' );
$test_options[ HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES ] = array();
access_expect( true, hdylb_filter_admin_bar_visibility( true ), 'Toolbar preference passes through when no roles selected' );
$test_options[ HDY_LOGIN_BRANDING_OPTION_TOOLBAR_HIDDEN_ROLES ] = array( 'subscriber' );
ob_start();
hdylb_backend_admin_bar_spacing();
$css = ob_get_clean();
access_expect( true, false !== strpos( $css, 'padding-top:0' ), 'Backend toolbar spacing is removed' );
add_action( 'in_admin_header', 'wp_admin_bar_render', 0 );
$before = count( $test_actions['in_admin_header'][0] ?? array() );
hdylb_remove_backend_admin_bar();
$after = count( $test_actions['in_admin_header'][0] ?? array() );
access_expect( $before - 1, $after, 'Core backend toolbar renderer is removed' );

echo "PASS: role settings validation, Dashboard 403 exemptions and bypass, and front-end/backend Toolbar controls.\n";
