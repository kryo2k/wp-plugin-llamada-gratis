<?php
/**
 * Plugin Name: Llamada Gratis
 * Description: Custom plugin to add support for online phone dialing (provided by http://llamegratisenlinea.com/) implemented on hotelarenalkioro.com
 * Version: 0.1
 * Author: Hans Doller
 * Author URI: http://ticonerd.com
 * License: GPLv3
 */


if (! function_exists ( 'add_action' )) {
	exit ();
}

define ( "LLAMA_I18N", 'llama' );
define ( "LLAMA_KEY_SETTINGS", 'llama-settings' );

define ( "LLAMA_SETTING_ENABLED",   'llama_enabled' );
define ( "LLAMA_SETTING_SITEID",    'llama_siteid' );
define ( "LLAMA_SETTING_BASEURL",   'llama_baseurl' );
define ( "LLAMA_SETTING_TARGETSEL", 'llama_targetselector' );
define ( "LLAMA_SETTING_POSITION",  'llama_position' );
define ( "LLAMA_SETTING_IMAGE",     'llama_image' );
define ( "LLAMA_SETTING_IMAGETITLE",'llama_imagetitle' );
define ( "LLAMA_SETTING_WINDOWTITLE",'llama_windowtitle' );

function llama_admin_init() {
	global $wp_version;
	if (! function_exists ( 'is_multisite' ) && version_compare ( $wp_version, '3.0', '<' )) {
		wp_die( __( 'Llamada gratis plugin requires wordpress >= 3.0' ) );
	}

	llama_admin_register_settings();
}
function llama_admin_plugin_action_links($links, $file) {
	if ($file == plugin_basename ( __FILE__ )) {
		$links [] = '<a href="' . add_query_arg ( array (
				'page' => LLAMA_KEY_SETTINGS
		), admin_url ( 'options-general.php' ) ) . '">' . __ ( 'Settings', LLAMA_I18N ) . '</a>';
	}

	return $links;
}
function llama_admin_menu() {
	add_options_page( __ ( 'Llamada Gratis Manager', LLAMA_I18N ), __ ( 'Llamada gratiss', LLAMA_I18N ), 'manage_options', LLAMA_KEY_SETTINGS, 'llama_admin_options' );
}
function llama_admin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	?><form method="POST" action="options.php">
<?php settings_fields(LLAMA_KEY_SETTINGS); // pass slug name of page, also referred to in Settings API as option group name
do_settings_sections(LLAMA_KEY_SETTINGS);  // pass slug name of page
submit_button();
?>
</form><?php
}
function llama_admin_setting_section_general() {
?><p><?php esc_html_e( 'General settings for llamada gratis plugin.', LLAMA_I18N ); ?></p><?php
}
function llama_admin_setting_section_image() {
?><p><?php esc_html_e( 'Image settings for llamada gratis plugin.', LLAMA_I18N ); ?></p><?php
}
function llama_admin_setting_enabled() {
	echo sprintf('<input name="%s" type="checkbox" value="1" class="code"%s>',LLAMA_SETTING_ENABLED, checked( 1, llama_get_enabled(), false ));
}
function llama_admin_setting_siteid() {
	echo sprintf('<input name="%s" size="40" type="text" value="%s">',LLAMA_SETTING_SITEID, llama_get_siteid());
}
function llama_admin_setting_baseurl() {
	echo sprintf('<input name="%s" size="50" type="text" value="%s">',LLAMA_SETTING_BASEURL, llama_get_baseurl());
}
function llama_admin_setting_targetselector() {
	echo sprintf('<input name="%s" size="50" type="text" value="%s">',LLAMA_SETTING_TARGETSEL, llama_get_targetselector());
}
function llama_admin_setting_position() {
	echo sprintf('<input name="%s" size="40" type="text" value="%s">',LLAMA_SETTING_POSITION, llama_get_position());
}
function llama_admin_setting_image() {
	echo sprintf('<input name="%s" size="75" type="text" value="%s">',LLAMA_SETTING_IMAGE, llama_get_image());
}
function llama_admin_setting_imagetitle() {
	echo sprintf('<input name="%s" size="75" type="text" value="%s">',LLAMA_SETTING_IMAGETITLE, llama_get_imagetitle());
}
function llama_admin_setting_windowtitle() {
	echo sprintf('<input name="%s" size="75" type="text" value="%s">',LLAMA_SETTING_WINDOWTITLE, llama_get_windowtitle());
}
function llama_admin_get_settings_sections() {
	return (array) apply_filters('llama_admin_get_settings_sections', array(
		'llama_general' => array(
			'title'    => __( 'General settings', LLAMA_I18N ),
			'callback' => 'llama_admin_setting_section_general'
		),
		'llama_image' => array(
			'title'    => __( 'Image settings', LLAMA_I18N ),
			'callback' => 'llama_admin_setting_section_image'
		)
	));
}
function llama_admin_get_settings_fields() {
	return (array) apply_filters('llama_admin_get_settings_fields', array(
		'llama_general' => array(
			LLAMA_SETTING_ENABLED => array(
				'title'             => __( 'Enable llamada gratis plugin', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_enabled',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),
			LLAMA_SETTING_SITEID => array(
				'title'             => __( 'Site id', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_siteid',
				'sanitize_callback' => 'trim',
				'args'              => array()
			),
			LLAMA_SETTING_BASEURL => array(
				'title'             => __( 'Site url (%s is replaced by site id)', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_baseurl',
				'sanitize_callback' => 'trim',
				'args'              => array()
			),
			LLAMA_SETTING_TARGETSEL => array(
				'title'             => __( 'jQuery target for llamada gratis', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_targetselector',
				'args'              => array()
			),
			LLAMA_SETTING_WINDOWTITLE => array(
				'title'             => __( 'Title for pop-up window', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_windowtitle',
				'args'              => array()
			)
		),
		'llama_image' => array(
			LLAMA_SETTING_POSITION => array(
				'title'             => __( 'Position of link', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_position',
				'args'              => array()
			),
			LLAMA_SETTING_IMAGE => array(
				'title'             => __( 'Image for link', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_image',
				'args'              => array()
			),
			LLAMA_SETTING_IMAGETITLE => array(
				'title'             => __( 'Image title for link', LLAMA_I18N ),
				'callback'          => 'llama_admin_setting_imagetitle',
				'args'              => array()
			)
		)
	));
}
function llama_admin_get_settings_fields_for_section( $section_id = '' ) {

	// Bail if section is empty
	if ( empty( $section_id ) )
		return false;

	$fields = llama_admin_get_settings_fields();
	$retval = isset( $fields[$section_id] ) ? $fields[$section_id] : false;

	return (array) apply_filters( 'llama_admin_get_settings_fields_for_section', $retval, $section_id );
}
function llama_admin_register_settings() {
	$sections = llama_admin_get_settings_sections();

	if ( empty( $sections ) )
		return false;

	foreach ( (array) $sections as $section_id => $section ) {

		$page = empty($section['page']) ? LLAMA_KEY_SETTINGS : $section['page'];
		$fields = llama_admin_get_settings_fields_for_section( $section_id );

		if ( empty( $fields ) )
			continue;

		add_settings_section(
			$section_id,
			$section['title'],
			$section['callback'],
			$page
		);

		foreach ( (array) $fields as $field_id => $field ) {

			if ( ! empty( $field['callback'] ) && !empty( $field['title'] ) ) {
				add_settings_field( $field_id, $field['title'], $field['callback'], $page, $section_id, $field['args'] );
			}

			register_setting( $page, $field_id, $field['sanitize_callback'] );
		}
	}
}
function llama_get_enabled() {
	return intval ( get_option ( LLAMA_SETTING_ENABLED, 1 ) ) === 1;
}
function llama_get_targetselector() {
	return get_option ( LLAMA_SETTING_TARGETSEL, 'header' );
}
function llama_get_position() {
	return get_option ( LLAMA_SETTING_POSITION, 'bottom-left' );
}
function llama_get_image() {
	return get_option ( LLAMA_SETTING_IMAGE, path_join(plugin_dir_url(__FILE__),
		"images/default.png") );
}
function llama_get_imagetitle() {
	return get_option ( LLAMA_SETTING_IMAGETITLE, "Llamada gratis" );
}
function llama_get_windowtitle() {
	return get_option ( LLAMA_SETTING_WINDOWTITLE, "Llamada gratis" );
}
function llama_get_baseurl() {
	return get_option ( LLAMA_SETTING_BASEURL );
}
function llama_get_siteid() {
	return get_option ( LLAMA_SETTING_SITEID );
}
function llama_site_header_style() {
	wp_enqueue_style('llama', path_join(plugin_dir_url(__FILE__),
		"css/style.css"), false);
}
function llama_site_header_script() {
	wp_enqueue_script('llama', path_join(plugin_dir_url(__FILE__),
		"js/core.js"), false);
}
function llama_site_header_script_config() {
	echo sprintf('<script type="text/javascript">window.llama_config = %s;</script>',
			json_encode(array(
			'selector' => llama_get_targetselector(),
			'positionCls' => llama_get_position(),
			'image' => llama_get_image(),
			'imageTitle' => llama_get_imagetitle(),
			'windowTitle' => llama_get_windowtitle(),
			'url' => sprintf( llama_get_baseurl(), llama_get_siteid() ),
			'enabled' => llama_get_enabled()
		))
	);
}
function llama_site_init() {
}
function llama_controller_admin_boot() {
	add_action ( 'admin_init', 'llama_admin_init' );
	add_action ( 'admin_menu', 'llama_admin_menu' );
	add_filter ( 'plugin_action_links', 'llama_admin_plugin_action_links', 10, 2 );
}
function llama_controller_site_boot() {
	if(!llama_get_enabled()) return;

	add_action( 'init', 'llama_site_init' );
	add_action( 'wp_enqueue_scripts', 'llama_site_header_style' );
	add_action( 'wp_enqueue_scripts', 'llama_site_header_script' );
	add_action( 'wp_head', 'llama_site_header_script_config' );
}

// bootstrap the correct front-end controller:
is_admin () ? llama_controller_admin_boot () : llama_controller_site_boot ();