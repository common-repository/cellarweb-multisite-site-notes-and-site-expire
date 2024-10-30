<?php
/*
Plugin Name:        Multisite Site Notes and Site Expire from CellarWeb
Plugin URI:         https://www.cellarweb.com
Description:        Adds a Notes tab to the Site Info screen for network admins. Adds site expiration date to automatically disable a site. Disabled sites are redirected to the main site.
Version:            1.00
Requires at least:  5.2
Tested up to:       6.2
Requires PHP:       7.2
Author:             Rick Hellewell - CellarWeb.com
Author URI:         http://CellarWeb.com
License:            GPLv2 or later
License URI:        http://www.gnu.org/licenses/gpl-2.0.html
Text Domain:        CWMN
 */

/*
Copyright (c) 2016-2023 by Rick Hellewell and CellarWeb.com
All Rights Reserved

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA

 */

//------------------------------------------------------------------------------
// GETTING STARTED start
//------------------------------------------------------------------------------
if (!defined('ABSPATH')) {
	return;
}

define("CWMSINFO_VERSION", "1.00 (10 Feb 2023)");
// check for required versions of WP and PHP
$min_wp  = '4.9.6';
$min_php = '7.2';
// check for meeting minimum requirements, disable and notify
if ((!CWMN_is_requirements_met($min_wp, $min_php)) OR (!is_multisite())) {
	add_action('admin_init', 'CWMN_disable_plugin');
	add_action('admin_notices', 'CWMN_show_notice_disabled_plugin');
	add_action('network_admin_init', 'CWMN_disable_plugin');
	add_action('network_admin_notices', 'CWMN_show_notice_disabled_plugin');
	CWMN_deregister();
	return;
}
// ----------------------------------------------------------------------------
// disable plugin if WP/PHP versions are not enough
function CWMN_disable_plugin() {
	if (is_plugin_active(plugin_basename(__FILE__))) {
		deactivate_plugins(plugin_basename(__FILE__));
		// Hide the default "Plugin activated" notice
		if (isset($_GET['activate'])) {
			unset($_GET['activate']);
		}
	}
}

// ----------------------------------------------------------------------------
// show notice that plugin was deactivated because WP/PHP versions not enough
function CWMN_show_notice_disabled_plugin() {
	if (is_multisite()) {
		echo esc_html__('<div class="notice notice-error is-dismissible"><h3><strong>Plugin cannot be activated - the plugin is only for multisite installations. &nbsp;&nbsp;&nbsp;Plugin automatically deactivated.</p></div>');
	} else {
		echo esc_html__('<div class="notice notice-error is-dismissible"><h3><strong>Plugin cannot be activated - requires at least WordPress 4.6 and PHP 7.2.&nbsp;&nbsp;&nbsp;Plugin automatically deactivated.</p></div>');
	}
	return;
}

// ----------------------------------------------------------------------------
// ----------------------------------------------------------------------------
// check if at least WP 4.6 and PHP version at least 5.3
// based on https://www.sitepoint.com/preventing-wordpress-plugin-incompatibilities/
function CWMN_is_requirements_met($min_wp = '4.6', $min_php = '7.2') {
	// Check for WordPress version
	if (version_compare(get_bloginfo('version'), $min_wp, '<')) {
		return false;
	}
	// Check the PHP version
	if (version_compare(PHP_VERSION, $min_php, '<')) {
		return false;
	}
	return true;
}

// ============================================================================
// Add settings link on plugin page
// ----------------------------------------------------------------------------
function CWMN_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=CWMN_settings" title="Multisite Site Notes and Site Expire">Settings Information</a>';
	array_unshift($links, $settings_link);
	return $links;
}

// ============================================================================
// link to the settings page
// ----------------------------------------------------------------------------
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'CWMN_settings_link');

// ============================================================================
//  build the class for all of this
// ----------------------------------------------------------------------------
class CWMN_Settings_Page {

// start your engines!
	public function __construct() {
		add_action('admin_menu', array($this, 'CWMN_add_plugin_page'));
	}

// add options page
	public function CWMN_add_plugin_page() {
// This page will be under "Settings"
		add_options_page('Multisite Site Notes and Site Expire Info/Usage', 'Multisite Site Notes and Site Expire Info/Usage', 'manage_options', 'CWMN_settings', array($this, 'CWMN_create_admin_page'));
	}

// options page callback
	public function CWMN_create_admin_page() {
		// Set class property
		$this->options = get_option('CWMN_options');
		?>


<div align='center' class = 'CWMN_header'>
     <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/banner-1000x200.jpg" width="95%"  alt="" class='CWMN_shadow'>
</div>
    <p align='center'>Version: <?php echo esc_html(CWMSINFO_VERSION); ?></p>

<div >
    <div class="CWMN_options">
        <?php CWMN_info_top();?>
    </div>
    <div class='CWMN_sidebar'>
        <?php CWMN_sidebar();?>
    </div>
</div>
<!-- not sure why this one is needed ... -->
<div class="CWMN_footer">
    <?php CWMN_footer();?>
</div>
<?php }

// print the Section text
	public function CWMN_print_section_info() {
		print '<h3><strong>Information about Multisite Site Notes and Site Expire from CellarWeb.com</strong></h3>';
	}
}
// end of the class stuff
// ============================================================================

// ============================================================================
// if on the admin pages, set up the settings page
// ----------------------------------------------------------------------------

if (is_admin()) { // display info on the admin pages  only
	$my_settings_page = new CWMN_Settings_Page();
	// ----------------------------------------------------------------------------
	// supporting functions
	// ----------------------------------------------------------------------------
	//  display the top info part of the settings page
	// ----------------------------------------------------------------------------
	function CWMN_info_top() {
		?>
<h1 align='center'>Settings Information </h1>
<p>This plugin adds a 'Notes' tab to the Edit Site screen, which is only available in the Network Admin, Sites menu choice. That page is only available to the 'super-admin' of the multisite installation.</p>
<?php CWMN_show_notes();?>

<?php
return;}

// ============================================================================
	// display the copyright info part of the admin  page
	// ----------------------------------------------------------------------------
	function CWMN_info_bottom() {
		// print copyright with current year, never needs updating
		$xstartyear    = "2022";
		$xname         = "Rick Hellewell";
		$xcompanylink1 = ' <a href="http://CellarWeb.com" title="CellarWeb" >CellarWeb.com</a>';
		echo wp_kses_post('<hr><div style="background-color:#9FE8FF;padding-left:15px;padding:10px 0 10px 0;margin:15px 0 15px 0;">
<p align="center">Copyright &copy; ' . $xstartyear . '  - ' . date("Y") . ' by ' . $xname . ' and ' . $xcompanylink1 );
		echo wp_kses_post(' , All Rights Reserved. Released under GPL2 license.</p></div><hr>');
		return;
	}

	// end  copyright ---------------------------------------------------------

	// ----------------------------------------------------------------------------
	// ``end of admin area
	//here's the closing bracket for the is_admin thing
}

// ============================================================================
// add the css to the settings page
// ----------------------------------------------------------------------------
function CWMN_init() {
	wp_register_style('CWMN', plugins_url('/css/settings.css', __FILE__), array(), time());
	wp_enqueue_style('CWMN'); // gets the above css file in the proper spot
}

add_action('init', 'CWMN_init');

// ============================================================================
//  settings page sidebar content
// ----------------------------------------------------------------------------
function CWMN_sidebar() {
	?>
    <h3 align="center">But wait, there's more!</h3>
    <p>There's our plugin that will automatically add your <strong>Amazon Affiliate code</strong> to any Amazon links - even links entered in comments by others!&nbsp;&nbsp;&nbsp;Check out our nifty <a href="https://wordpress.org/plugins/amazolinkenator/" target="_blank">AmazoLinkenator</a>! It will probably increase your Amazon Affiliate revenue!</p>
    <p>We've got a <a href="https://wordpress.org/plugins/simple-gdpr/" target="_blank"><strong>Simple GDPR</strong></a> plugin that displays a GDPR banner for the user to acknowledge. And it creates a generic Privacy page, and will put that Privacy Page link at the bottom of all pages.</p>
    <p>How about our <strong><a href="https://wordpress.org/plugins/url-smasher/" target="_blank">URL Smasher</a></strong> which automatically shortens URLs in pages/posts/comments?</p>
    <hr />
    <p><strong>To reduce and prevent spam</strong>, check out:</p>
    <p><a href="https://wordpress.org/plugins/formspammertrap-for-comments/" target="_blank"><strong>FormSpammerTrap for Comments</strong></a>: reduces spam without captchas, silly questions, or hidden fields - which don't always work. </p>
    <p><a href="https://wordpress.org/plugins/formspammertrap-for-contact-form-7/" target="_blank"><strong>FormSpammerTrap for Contact Form 7</strong></a>: reduces spam when you use Contact Form 7 forms. All you do is add a little shortcode to the contact form.</p>
    <hr />
    <p>For <strong>multisites</strong>, we've got:

    <ul>
        <li><strong><a href="https://wordpress.org/plugins/multisite-comment-display/" target="_blank">Multisite Comment Display</a></strong> to show all comments from all subsites.</li>
        <li><strong><a href="https://wordpress.org/plugins/multisite-post-reader/" target="_blank">Multisite Post Reader</a></strong> to show all posts from all subsites.</li>
        <li><strong><a href="https://wordpress.org/plugins/multisite-media-display/" target="_blank">Multisite Media Display</a></strong> shows all media from all subsites with a simple shortcode. You can click on an item to edit that item. </li>
    </ul>
    </p>
    <hr />
    <p><strong>They are all free and fully featured!</strong></p>
    <hr />
    <p>I don't drink coffee, but if you are inclined to donate any amount because you like my WordPress plugins, go right ahead! I'll grab a nice hot chocolate, and maybe a blueberry muffin. Thanks!</p>
    <div align="center">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="SKSN99LR67WS6">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>
    <hr />
    <p><strong>Privacy Notice</strong>: This plugin does not store or use any personal information or cookies.</p>
<!--</div> -->
<?php
CWMN_cellarweb_logo();
	return;
}

// ============================================================================
// show the logo in the sidebar
// ----------------------------------------------------------------------------
function CWMN_cellarweb_logo() {
	?>
 <p align="center"><a href="https://www.cellarweb.com" target="_blank" title="CellarWeb.com site"><img src="<?php echo plugin_dir_url(__FILE__); ?>assets/cellarweb-logo-2022.jpg"  width="90%" class="CWMN_shadow" ></a></p>
 <?php
return;
}

// ============================================================================
// show the footer
// ----------------------------------------------------------------------------
function CWMN_footer() {
	?>
<p align="center"><strong>Copyright &copy; 2022- <?php echo date('Y'); ?> by Rick Hellewell and <a href="http://CellarWeb.com" title="CellarWeb" >CellarWeb.com</a> , All Rights Reserved. Released under GPL2 license. <a href="http://cellarweb.com/contact-us/" target="_blank" title="Contact Us">Contact us page</a>.</strong></p>
<?php
return;
}

// ============================================================================
//  functions for plugin
// ============================================================================

// -------------------------------------------------------------------------
//  "Notes" information text
function CWMN_show_notes() {
	?>
	<h2>The Network Site List Screen</h2>
	<p>This plugin adds a new "Expires" column to the Network, Sites, All Sites screen. That will show the site expiration date from the Notes tab of the Site Edit page. That page is only shown via the Network Admin, Sites choice, and only for the network 'super-admin'. </p>
<p>The Expiration date is when the site is automatically marked as deleted. A site that is about to expire will show the date with a yellow background; an expired site will show the date with a red background. If a site does not have an expiration date, 'no expiration' is shown. The main site will not have an expiration date.</p>
<p>Site expiration dates are helpful if your site is subscription-based. Just set the expiration date when you create the site, or when the site subscription is renewed.</p>
<p>An expired site will be redirected as specified below.</p>
<p></p>
        <h2>Notes About the 'Notes' Tab Settings</h2>
        <p>The Notes tab and settings are only available to the network 'super-admin' on the Network Site Edit screen. For the main site, only the 'Notes" text area is shown (because you don't want your main site to expire).</p>
        <ul class='CWMN_list'>
            <li>The <b>Deleted Site Redirect URL</b> is used to redirect a visitor if the sub-site (blog) has been set to 'delete'. This can be a URL on the 'master' site, or another site. By default, the redirect will go to the master site's 404 page. You may want to build a separate 'blog not active' page to use here.</li>
 <li>The <b>Site Status</b> line shows if the current site is active or inactive. A site will be inactive if it is in deleted mode. The deleted mode is set by the expiration date value. If the blog has expired - current date is after the expire date below - the site is automatically set to 'deleted' mode. The "deleted" attributes on the Site Edit/Info screen is overridden by the Site Expired date. Visitors will be sent to the Deleted Redirect URL specified above.<br>A 'deleted' site is not permanently removed from the network database. Public access to a 'deleted' site is redirected to the Redirect URL you specify (or the main site's 404 page, if not specified).</li>
<li>The <b>Site Expired</b> date is when the site will automatically become inactive (status/attribute = deleted). The only way a site can be activated is by changing this date to a future date. A blank date will keep the site active.</li>
<li>The <b>Notes</b> area is a text box you can use for site notes of any kind. </li>
        </ul>
<p>All plugin settings on the Edit Sites screen 'Notes' tab are only available to the 'super-admin' user. The settings are not available on the plugin Settings screen on the main site, nor shown to any sub-site administrators.  </p>
<h2>Support</h2>
<p>For questions or comments about this plugin, please use the Support area of the plugin.</p>
        <?php
return;
}

// -------------------------------------------------------------------------

// switch to master blog if current blog has deleted flag
add_filter('plugins_loaded', 'CWMN_blog_deleted');
//CWMN_blog_deleted();
// 		- make sure no output, otherwise headers already sent error
function CWMN_blog_deleted() {
	$id      = get_current_blog_id();
	$main_id = get_main_site_id();
	if ($main_id === $id) {return;} // don't do anything on the main site
	// check if site is past expiration date and set site active flag
	$today       = date("Y-m-d");
	$expire_date = get_blog_option($id, 'site_expire_date');
	if (!$expire_date) {return;}
	if ($expire_date < $today) {
		$status = update_blog_status($id, 'deleted', '1');
	} else {
		$status = update_blog_status($id, 'deleted', '0');
	}
	$site_details = get_site($id); //returns object
	$site_deleted = $site_details->deleted; // look for that object
	// update the deleted flag if expired (needs to happen before edit page is displayed
	if (($site_deleted === "1") and (is_super_admin())) {
		add_action('admin_notices', 'CWMN_admin_redirect_notice');
	}
		$redirect_url = get_blog_option($id, "redirect_url");

	if (($site_deleted === "1") ) {
		if (!$redirect_url) {$redirect_url = network_home_url() . "notfound";}
		wp_safe_redirect($redirect_url, 301);
		exit;
		die();
		exit;
	}
	return;
}

// -------------------------------------------------------------------------
// notice to admins about redirects if site is disabled

function CWMN_admin_redirect_notice() {
	$id           = get_current_blog_id();
	$redirect_url = get_blog_option($id, "redirect_url");
	$class        = 'notice notice-error';
	$message      = __('Disabled site redirect: normally redirected to $redirect_url , but since you are a network admin, this notice is displayed.', 'CWMN');

	printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

// -------------------------------------------------------------------------
// Adds message about the deleted setting on the site-info page

add_filter('network_site_info_form', 'CWMN_add_form_message');
function CWMN_add_form_message() {
	printf("<p><b>Note</b>: The <b>Deleted</b> attribute is set (and overridden) by the <b>Site Expiration</b> date on the <b>Notes</b> tab. Adjust that date to set/clear the Deleted attribute. Deleted sites will redirect to the Deleted URL set on the Notes page.</p>");
}

// ==============================================================================
//  Adds new  tab to ehe Site Edit screen
//      - based on  https://rudrastyh.com/wordpress-multisite/custom-tabs-with-options.html
// -------------------------------------------------------------------------

//  add the tab to the site-edit page, but not on the main site
add_filter('network_edit_site_nav_links', 'CWMN_new_siteinfo_tab');

function CWMN_new_siteinfo_tab($tabs) {

	$tabs['site-notes'] = array(
		'label' => 'Notes',
		// 'url' => 'sites.php?page=notespage',
		'url' => add_query_arg('page', 'notespage', 'sites.php'),
		'cap' => 'manage_sites'
	);
	return $tabs;
}

// -------------------------------------------------------------------------
// add settings for that site-edit page tab

add_action('network_admin_menu', 'CWMN_new_page');

function CWMN_new_page() {
	add_submenu_page(null, 'Edit site', 'Edit site', 'manage_network_options', 'notespage', 'CWMN_page_callback');
}

// -------------------------------------------------------------------------
//      the 'info' page form and data

function CWMN_page_callback() {

	$id   = absint($_REQUEST['id']);
	$site = get_site($id);
	?>
        <div class="wrap">
            <h1 id="edit-site">Edit Site: <?php echo $site->blogname; ?></h1>
            <p class="edit-site-actions">
                <a href="<?php echo esc_url(get_home_url($id, '/')); ?>">Visit</a> | <a href="<?php echo esc_url(get_admin_url($id)); ?>">Dashboard</a>
            </p>
            <?php
// navigation tabs
	network_edit_site_nav(
		array(
			'blog_id' => $id,
			'selected' => 'site-notes' // current tab
		)
	);
	$deleted_flag = ((int) get_blog_status($id, 'deleted')) ? true : false;
	?>
            <form method="post" action="edit.php?action=notesupdate">
                <?php wp_nonce_field('notes-check' . $id);?>
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <table class="form-table">
                    <tr><td  style="text-align:right;"><h2>Site Additional Settings</h2></td><td></td></tr>
<?php if (!is_main_site($id)) {?>
                    <tr>
                        <th scope="row" style="text-align:right;"><label for="redirect_url">Deleted Site Redirect URL</label></th>
                        <td><input name="redirect_url" class="regular-text" type="url" id="redirect_url" value="<?php echo esc_url(get_blog_option($id, 'redirect_url')); ?>" placeholder="enter URL" /> <br>Specify full URL for redirection for deleted sites. If not set or non-existent, will redirect to your main site's 404 page.<br>The main site is currently <b><?php echo esc_url(network_home_url()); ?></b> .<br>Default redirect will be <b><?php echo esc_url(network_home_url()) . "notfound"; ?></b>.<br>URLs are not checked to actually exist, just that they are a valid URL.</td>
                    </tr>
                     <tr>
                        <th scope="row" style="text-align:right;"><label for="site_deleted">Site Status</label></th>
<td style="vertical-align: inherit;"><?php echo (!$deleted_flag) ? "Site is <b>active</b>." : "Site is <b>inactive</b> (deleted attribute on Info tab was set by this expiration date)."; ?></td>
                    </tr>
                   <tr>
                        <th scope="row" style="text-align:right;"><label for="site_expire_date">Site Expires</label></th>
                        <td><input name="site_expire_date" class="regular-text" type="date" id="site_expire_date" style="width:auto;" value="<?php echo esc_attr(get_blog_option($id, 'site_expire_date')); ?>" />  mm/dd/yyyy<br>Changing expiration date will set/reset Delete attribute on the Info tab.<br>Note that a 'deleted' site's data is still available, but public access to the site is redirected to the Redirect URL.<br>Clear the date to make the site non-expiring.</td>
                    </tr>
                    <?php }?>
                    <tr>
                        <th scope="row" style="text-align:right;"><label for="site_notes">Site Notes</label></th>
                        <td><textarea name="site_notes" class="regular-text"  id="site_notes" rows=10 cols=50 ><?php echo esc_attr(get_blog_option($id, 'site_notes')); ?> </textarea></td>
                    </tr>
                </table>
                <?php submit_button();?>
            </form>
			<?php //CWMN_show_site_info();?>
            <?php CWMN_show_notes();?>
        </div>
    <?php
return;
}

// -------------------------------------------------------------------------
// save settings code for the new tab in site edit

add_action('network_admin_edit_notesupdate', 'CWMN_save');
function CWMN_save() {

	$id = absint($_POST['id']);
	check_admin_referer('notes-check' . $id); // nonce check, will 403 if nonce not valid
	$expire_date = preg_replace("([^0-9/-])", "", $_POST['site_expire_date']);  // sanitize date
	// update values
	update_blog_option($id, 'redirect_url', sanitize_url($_POST['redirect_url']));
	update_blog_option($id, 'site_expire_date', $expire_date);
	update_blog_option($id, 'site_notes', sanitize_text_field($_POST['site_notes']));

	// redirect to /wp-admin/sites.php?page=notespage&blog_id=ID&updated=true
	wp_safe_redirect(
		add_query_arg(
			array(
				'page' => 'notespage',
				'id' => $id,
				'updated' => 'true'
			),
			network_admin_url('sites.php')
		)
	);
	exit;
	return;
}

// -------------------------------------------------------------------------
// add admin notice on settings save
add_action('network_admin_notices', 'CWMN_notice');
function CWMN_notice() {

	if (isset($_GET['updated']) && isset($_GET['page']) && 'notespage' === $_GET['page']) {
		?>
            <div id="message" class="updated notice is-dismissible">
                <p>Settings saved.</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        <?php
}
	return;
}

// -------------------------------------------------------------------------
// ensure current site can be edited
add_action('current_screen', 'CWMN_double_check');
function CWMN_double_check() {

	// do nothing if we are on another page
	$screen = get_current_screen();
	if ('sites_page_notespage-network' !== $screen->id) {
		return;
	}
	// $id is a blog ID
	$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
	if (!$id) {
		wp_die(__('Incorrect site ID.'));
	}
	if (!get_site($id)) {
		wp_die(__('The requested site does not exist.'));
	}
	return;
}

// -------------------------------------------------------------------------
// adding the expire date column and data to the sites.php Site List page

add_filter('manage_sites-network_columns', 'CWMN_add_expired_date_column');
add_action('manage_sites_custom_column', 'CWMN_exipred_date_data', 10, 2);

// add the column to the sites.php page
function CWMN_add_expired_date_column($sites_columns) {
	$sites_columns['expired_Date'] = 'Expires';
	return $sites_columns;
}

// Populate site_name with blogs site_name, add background color on expiration date
function CWMN_exipred_date_data($column_name, $blog_id) {
	$column_name          = 'expired_Date';
	$current_blog_details = get_blog_details(array('blog_id' => $blog_id));
	$current_blog_expire  = get_blog_option($blog_id, 'site_expire_date');
	$days                 = "";
	if ($current_blog_expire) {
		$from_date = time(); // Input your date here e.g. strtotime("2014-01-02")
		$to_date   = strtotime($current_blog_expire);
		$day_diff  = $to_date - $from_date;
		$days      = ceil($day_diff / (60 * 60 * 24));
	}

	$current_blog_expire = ($current_blog_expire > 0) ? $current_blog_expire : null;
	switch (true) {
		case ($days == null):
		case ($days == "");
		default:
			$exp_date_msg = "no expiration date";
			break;
		case ($days > 60) :
			$exp_date_msg = "<span style='padding:2px 5px;' >" . $current_blog_expire . "&nbsp;&nbsp;(in $days days)</span>";
			break;
		case (($days <= 60) and ($days >= 30)):
			$exp_date_msg = "<span style='background-color:cyan;color:black;padding:2px 5px;' >" . $current_blog_expire . "&nbsp;&nbsp;<i>(in $days days)</i></span>";
			break;
		case (($days < 30) and ($days > 0)):
			$exp_date_msg = "<span title='expires soon' style='background-color:yellow;color:black;padding:2px 5px;' >" . $current_blog_expire . "&nbsp;&nbsp;<i>(in $days days)</i></span>";
			break;
		case ($days <= 0):
			$exp_date_msg = "<div title='site has expired, will be redirected' style='background-color:red;color:white;padding:2px 5px;' ><b>" . $current_blog_expire . "<br>EXPIRED</b> - will be redirected</div>";
			break;

	}
	// note following already sanitized, $exp_date_msg is html code from switch statement above
	//	- date value comes from settings, which was sanitized during form submission
    echo ($current_blog_expire == NULL) ? wp_kses_post("no expiration") : wp_kses_post($exp_date_msg);
	return;
}

// -------------------------------------------------------------------------
//  plugin end
// -------------------------------------------------------------------------
