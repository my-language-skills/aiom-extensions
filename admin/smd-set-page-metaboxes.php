<?php

/**
 * Set page content related
 *
 *
 * @link URL
 *
 * @package simple-metadata
 * @subpackage simple-metadata/smd-metaboxes
 * @since 1.0
 */


/**
 * Add option page 'Metadata'
 *
 * Creates 'Metadata' option page and for it:
 * Site settings page, Settings page and cpt site-meta
 *
 * @since 1.0
 *
 */
add_action ('admin_menu', 'smd_add_option_page');
function smd_add_option_page () {

	if (1 != get_current_blog_id() || !is_multisite()){
		//Adds main menu page for plugin and all addons
		add_menu_page('Simple Metadata', __('Metadata', 'simple-metadata'), 'manage_options', 'smd_set_page', 'smd_render_options_page', 'dashicons-search');
		//Fix having different name in admin menu for main subpage
		add_submenu_page('smd_set_page',__('Settings', 'simple-metadata'), __('Settings', 'simple-metadata'), 'manage_options', 'smd_set_page');
		if (!is_plugin_active('pressbooks/pressbooks.php') ){
			add_submenu_page('smd_set_page',__('Site Settings', 'simple-metadata'), __('Site Settings', 'simple-metadata'), 'manage_options', 'smd_set_page_site', 'smd_render_site_page');
		}

		if (is_plugin_active('pressbooks/pressbooks.php') ){
			smd_add_booktype_box(); // metabox 'booktype'
		}
	/*
		(Commented out v1.4.3) adding settings metaboxes and settigns sections
		add_meta_box('smd-location-settings', __('General Metadata', 'simple-metadata'), 'smd_render_locations_metabox', 'smd_set_page', 'normal', 'core');
	*/
		add_meta_box('smd-settings', __('Front Page', 'simple-metadata'), 'smd_render_metabox', 'smd_set_page_site', 'normal', 'core');
		smd_add_options_box(); // metabox 'Options'
		smd_add_logo_box(); // metabox 'Logo'

		$post_types = smd_get_all_post_types();
		$locations = get_option('smd_locations');

		$net_locations = [];

		if (is_multisite()){
			$net_locations = get_site_option('smd_net_locations');
		}

		//adding settings sections for type of site setting and locations
		add_settings_section( 'smd_set_page_site', '', '', 'smd_set_page_site' );
		add_settings_section( 'smd_locations', '', '', 'smd_locations' );
		//registering setting for type of site
		register_setting ('smd_set_page_site', 'smd_website_blog_type');
		//register_setting for options translation_of
		register_setting ('smd_set_page_site', 'smd_translation_of');
		//registering setting for locations
		register_setting('smd_locations', 'smd_locations');

		if (!get_option('smd_website_blog_type') && !is_plugin_active('pressbooks/pressbooks.php')) {
			update_option('smd_website_blog_type', __('Blog', 'simple-metadata') );
		}
		if (!is_plugin_active('pressbooks/pressbooks.php')){
			add_settings_field ('smd_website_blog_type', __('Type of Site', 'simple-metadata'), 'smd_render_switch_set', 'smd_set_page_site', 'smd_set_page_site');
		}

		//adding location option for every public CPT
		foreach ($post_types as $post_type) {
			// we ommit Book Info or Site-Meta as general meta is not applicable for them
			if ('metadata' == $post_type || 'site-meta' == $post_type){
				continue;
			}
			// Translate post type for internalization
	    switch ($post_type) {
	      case 'post':
	        $label = __('Post', 'simple-metadata');
	        break;
	      case 'page':
	        $label = __('Page', 'simple-metadata');
	        break;
	      default:
	        $label = ucfirst($post_type);
	        break;
	    }

			add_settings_field ('smd_locations['.$post_type.']', $label, function () use ($post_type, $locations, $net_locations){
				$checked = isset($locations[$post_type]) ? true : false;
				$disabled = isset($net_locations[$post_type]) ? 'disabled' : '';
				?>
					<input type="checkbox" name="smd_locations[<?=$post_type?>]" id="smd_locations[<?=$post_type?>]" value="1" <?php checked(1, $checked);?> <?=$disabled?>>
					<?php if('' !== $disabled): ?>
						<input type="hidden" name="smd_locations[<?=$post_type?>]" value="1">
					<?php endif; ?>
				<?php

			}, 'smd_locations', 'smd_locations');
		}
	}
}


/**
 * Render the options page for plugin
 *
 * @since   1.0
 *
 */
function smd_render_options_page() {

	if(!current_user_can('manage_options')){
		return;
	}

	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
	?>
        <div class="wrap">
        	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) { ?>
        	<div class="notice notice-success is-dismissible">
				<p><strong> <?php esc_html_e('Settings saved.', 'simple-metadata'); ?></strong></p>
			</div>
			<?php } ?>
			<h1><?php esc_html_e('Simple Metadata Settings', 'simple-metadata'); ?></h1>
            <div class="metabox-holder">
					<?php
					do_meta_boxes('smd_set_page', 'normal','');
					?>
            </div>
        </div>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                // close postboxes that should be closed
                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                // postboxes setup
                postboxes.add_postbox_toggles('smd_set_page');
            });
            //]]>
        </script>
		<?php
}


/**
 *
 * @since 1.0
 *
 */
function smd_render_site_page () {

	if(!current_user_can('manage_options')){
		return;
	}

	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
	?>
        <div class="wrap">
        	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) { //if settings were saved, we show notice?>
        	<div class="notice notice-success is-dismissible">
				<p><strong><?php esc_html_e('Settings saved.', 'simple-metadata'); ?></strong></p>
			</div>
			<?php } ?>
			<h1><?php esc_html_e('Simple Metadata Site Configuration', 'simple-metadata'); ?></h1>
            <div class="metabox-holder">
					<?php
					do_meta_boxes('smd_set_page_site', 'normal','');
					?>
            </div>
        </div>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready( function($) {
                // close postboxes that should be closed
                $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                // postboxes setup
                postboxes.add_postbox_toggles('smd_set_page_site');
            });
            //]]>
        </script>
		<?php
}

/**
 * Adds the metabox 'Options' in the settings page
 *
 * @since   1.4
 */
function smd_add_options_box(){
	add_meta_box('smd-box-options',	__('Options', 'simple-metadata'), 'smd_render_metabox_options', 'smd_set_page', 'normal', 'low');
	add_settings_section( 'smd_set_page_section_options', '', '', 'smd_set_page_section_options' );
	add_settings_field ('smd_options_hide_dates', __('Hide dates', 'simple-metadata'), 'smd_render_options_hide_dates', 'smd_set_page_section_options', 'smd_set_page_section_options');
	register_setting ('smd_set_page_section_options', 'smd_hide_metadata_dates');
}

/**
 * Adds the metabox 'Logo' in the settings page
 *
 * @since   1.4.1
 */
function smd_add_logo_box(){
	add_meta_box('smd_logo_box',	__('Logo', 'simple-metadata'), 'smd_render_logo_box', 'smd_set_page', 'normal', 'low');
	add_settings_section( 'smd_set_page_section_logo', '', '', 'smd_set_page_section_logo' );
	add_settings_field ('smd_logo_image', 'Url Image', 'smd_render_logo_field', 'smd_set_page_section_logo', 'smd_set_page_section_logo');
	register_setting ('smd_set_page_section_logo', 'smd_logo_image_id');
}

/**
 * Adds the metabox 'Book-type' in the settings page
 *
 * @since   1.4.2
 */
function smd_add_booktype_box(){
	add_meta_box('smd_book-type_metabox',	__('Metadata print configuration', 'simple-metadata'), 'smd_render_booktype_box', 'smd_set_page', 'normal', 'high');
	add_settings_section( 'smd_set_page_section_booktype', '', '', 'smd_set_page_section_booktype' );
	add_settings_field ('smd_booktype_option', __('Set book type preset', 'simple-metadata'), 'smd_render_booktype_field', 'smd_set_page_section_booktype', 'smd_set_page_section_booktype');
	add_settings_field ('smd_booktype_frontaback', __('Disable WebPage type for', 'simple-metadata'), 'smd_render_booktype_frontback', 'smd_set_page_section_booktype', 'smd_set_page_section_booktype');
	register_setting ('smd_set_page_section_booktype', 'smd_set_booktype_option');

	register_setting ('smd_set_page_section_booktype', 'smd_disable_frontmatter_type');
	register_setting ('smd_set_page_section_booktype', 'smd_disable_backmatter_type');
}

/**
* Render the the content of logo Image metabox
*
* @since 1.4.1
*
*/
function smd_render_logo_box ( $post ) {
	?>
  <div class="wrap">
    <form method="post" action="options.php">
			<span class="description">
		 		<?php
					esc_html_e('The logo must be a rectangle, not a square.
					  The logo should fit in a 60x600px rectangle, and either be exactly 60px high (preferred), or exactly 600px wide.', 'simple-metadata');
					echo "<br>";
					esc_html_e('For example, 450x45px would not be acceptable, even though it fits within the 600x60px rectangle.', 'simple-metadata');
				?>
		  </span>
      <?php
			settings_fields( 'smd_set_page_section_logo' );
			do_settings_sections( 'smd_set_page_section_logo' );
      submit_button();
      ?>
    </form>
    <p></p>
  </div>
	<?php
}

/**
* Render booktype metabox
*
* @since 1.4.2
*
*/
function smd_render_booktype_box(){
	?>
  <div class="wrap">
    <form method="post" action="options.php">
				<?php
				settings_fields( 'smd_set_page_section_booktype' );
				do_settings_sections( 'smd_set_page_section_booktype' );
				submit_button();
				?>
		</form>
	</div>
<?php
}

/**
 * Display the content in the metabox 'Option'
 *
 * @since   1.4
 */
function smd_render_metabox_options(){
  ?>
  <div class="wrap">
    <form method="post" action="options.php">
      <?php
			settings_fields( 'smd_set_page_section_options' );
			do_settings_sections( 'smd_set_page_section_options' );
      submit_button();
      ?>
    </form>
    <p></p>
  </div>
  <?php
}

/**
 * Simple Metadata Settings
 *
 * @since 1.0
 *
 */
function smd_render_locations_metabox () {
	?>
	<div class="wrap">
			<span class="description">
				<span class="description">
					 <?php esc_html_e('Activate the post types where metadata will be available.', 'simple-metadata'); ?>
			 	</span>
			</span>
           <form method="post" action="options.php">
			<?php
			settings_fields( 'smd_locations' );
			do_settings_sections( 'smd_locations' );
			submit_button();
			?>
		   </form>
		   <p></p>
    </div>
    <?php
}

/**
 * Simple Metadata Site configuration
 *
 * @since 1.0
 *
 */
function smd_render_metabox(){
	?>
	<div class="wrap">
	<span class="description"></span>
           <form method="post" action="options.php">
			<?php
			settings_fields( 'smd_set_page_site' );
			do_settings_sections( 'smd_set_page_site' );
			submit_button();
			?>
		   </form>
		   <p></p>
    </div>
    <?php
}

/**
 * Function for rendering radio button fields
 *
 * @since 1.0
 */
function smd_render_switch_set() {

	$disabled = smd_is_option_disabled('smd_net_sites_type');
	?>
	<label for="smd_website_blog_type_1"><?php esc_html_e('Blog', 'simple-metadata'); ?> <input type="radio" id="smd_website_blog_type_1" name="smd_website_blog_type" value="Blog" <?php checked('Blog', get_option('smd_website_blog_type'))?> <?=$disabled?> ></label>
	<label for="smd_website_blog_type_2"><?php esc_html_e('WebSite', 'simple-metadata'); ?> <input type="radio" id="smd_website_blog_type_2" name="smd_website_blog_type" value="WebSite" checked="checked" <?php checked('WebSite', get_option('smd_website_blog_type'))?> <?=$disabled?> ></label>
	<?php // if education plugin is active, add new options to select (possibly new values with other addons)
	if (is_plugin_active('simple-metadata-education/simple-metadata-education.php')){
		?>
	<label for="smd_website_blog_type_3"><?php esc_html_e('Book', 'simple-metadata'); ?> <input type="radio" id="smd_website_blog_type_3" name="smd_website_blog_type" value="Book" <?php checked('Book', get_option('smd_website_blog_type'))?> <?=$disabled?> ></label>
	<label for="smd_website_blog_type_4"><?php esc_html_e('Course', 'simple-metadata'); ?> <input type="radio" id="smd_website_blog_type_4" name="smd_website_blog_type" value="Course" <?php checked('Course', get_option('smd_website_blog_type'))?> <?=$disabled?> ></label><br>
		<?php

	if ('disabled' === $disabled){
		echo '<input type="hidden" name="smd_website_blog_type" value="'.get_site_option('smd_net_sites_type').'">';
		echo '<br><span class="description">' .
						__('Type was selected by network administrator.
						You are not allowed to change it.', 'simple-metadata') . '</span>';
	} else {

	}
		echo '<br><span class="description">' . __('Select schema type which will be appplied
					for front-page metadata', 'simple-metadata') . '</span>';

	}
}


/**
 * Display the option 'Hide dates' in the metabox 'Options' field
 *
 * @since   1.4
 */
function smd_render_options_hide_dates(){
  ?>
  <label for="smd_hide_dates">
    <input type="checkbox" id="smd_hide_metadata_dates" name="smd_hide_metadata_dates" value="true"
      <?php checked('true', get_option('smd_hide_metadata_dates')) ?>
			<?php echo smd_is_option_disabled('smd_net_hide_metadata_dates') ?>
    >
  </label><br>
  <span class="description">
      <?php
      esc_html_e('If selected the metadata tags "dateCreated" and "datePublished" will be hidden.');
      ?>
  </span>
  <?php
}

/**
* Render logo field
*
* @since 1.4.1
*
*/
function smd_render_logo_field(){
	?>
		<input id="smd_logo_image_url" type="url" name="smd_logo_image_url" style="width:65%;float:left" value="<?php echo wp_get_attachment_image_url(get_option('smd_logo_image_id'), 'full'); ?>" />
		<input id="smd_upload_image_button" type="button" class="button-primary"  value="Insert Image" />
		<input id="smd_logo_image_id" type="hidden" name="smd_logo_image_id" value=""></input>
	<?php
}

/**
* Render 'Set book type' field
*
* @since 1.4.2
*
*/
function smd_render_booktype_field(){
	?>
		<input type="radio" name="smd_set_booktype_option" id="smd_booktype_option_course" value=""
			<?php checked('', get_option('smd_set_booktype_option')) ?>
		/> 	<label for="smd_booktype_option_course"><b>Course</b></label>
	  <br>
		<input type="radio" name="smd_set_booktype_option" id="smd_booktype_option_book"  value="book"
			<?php checked('book', get_option('smd_set_booktype_option')) ?>
		/>	<label for="smd_booktype_option_book"><b>Book</b></label>
		<span class="description">
				<p>By selecting one of the options, output metadata printed in the front-end get modified based on selection.</p>
		</span>
	<?php
}

/**
* Render 'Disable WebPage type for' field
*
* @since 1.4.3
*
*/
function smd_render_booktype_frontback(){
	?>
		<input type="checkbox" name="smd_disable_frontmatter_type" id="smd_booktype_option_frontmatter"  value="1"
			<?php checked('1', get_option('smd_disable_frontmatter_type')) ?>
		/>	<label for="smd_booktype_option_frontmatter"><b>Front-matter </b> </label>

		<br>
		<input type="checkbox" name="smd_disable_backmatter_type" id="smd_booktype_option_backmatter"  value ="1"
			<?php checked('1', get_option('smd_disable_backmatter_type')) ?>
		/>	<label for="smd_booktype_option_backmatter"><b>Back-matter</b></label>
		<span class="description">
				<p>By checking the box, 'WebPage' metadata type will NOT be printed.</p>
		</span>
	<?php

}

/**
 * is option disabled
 *
 * @since 1.4
 *
 * @param string $option_net_name  the name of the network option to check if it exist
 * @return string $disabled could be '' or 'disabled';
 */
function smd_is_option_disabled($option_net_name){
	$disabled = '';

	//if network option is set to something except 'Local value', we disable selection
	if (is_multisite()){

		//getting option for type of site for network
		$net_sites_type = get_site_option ($option_net_name) ?: '0';
		if ('0' !== $net_sites_type){
			$disabled = 'disabled';
		}
	}

	return $disabled;
}

/**
* Enqueue js for logo metabox
*
* @since 1.4.1
*
*/
function smd_enqueue_logo_script() {

	wp_enqueue_media(); // load media uploader
	wp_enqueue_script( 'smd-logo-box', plugins_url( 'simple-metadata') . '/inc/assets/js/smd-logo-box.js', array( 'jquery' ));
}
add_action( 'admin_enqueue_scripts', 'smd_enqueue_logo_script');
