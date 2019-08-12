<?php

/**
 * Summary (no period for file headers)
 *
 * file containing functions, used over all the post types and all the addons
 * identical functions (except class methods) should be added to this file
 *
 * @link URL
 *
 * @package simple-metadata
 * @subpackage XXXXXXX/XXXXXXX
 * @since x.x.x (when the file was introduced)
 */

/**
* Function for getting properties' metatags, collected from WP Core data
*
* @since
*
*/

function smd_get_general_tags($post_meta_type) {
  $post_id = get_the_ID();

  /*---- Data, related to 'Article' type properties ----*/
  //get the content and filter from html
	$post_content = strip_tags(apply_filters('the_content', get_post( $post_id )->post_content));
  //wordcount
	$word_count = str_word_count($post_content);
  ///> articleSection
	$categories = get_the_category( $post_id);
	$categories_arr = [];
	foreach ($categories as $category) {
		$categories_arr[] = $category->name;
	}
	$categories_string = implode(', ', $categories_arr);
	///<

	/*----- Data, related to 'CreativeWork' properties -----*/
	/*--- Author ---*/
	$author_id = get_post_field('post_author', $post_id);
	$author = get_the_author_meta('first_name', $author_id) && get_the_author_meta('last_name', $author_id) ? get_the_author_meta('first_name', $author_id).' '.get_the_author_meta('last_name', $author_id) : get_the_author_meta('display_name', $author_id);
  /*--- end Author ---*/

	//dateCreated
	$creation_date = get_the_date();
	//dateModified
	$last_modification_date = get_the_modified_date();
	//datePublished
	$publication_date = get_the_time(get_option( 'date_format' ));
	//editor
	$last_modifier = get_the_modified_author();
	//inLanguage
	$language = get_bloginfo( 'language' );
	//headline
	$title = get_the_title();

  /*--- Publisher ---*/
	//logo
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo = isset(wp_get_attachment_image_src( $custom_logo_id , 'full' )[0]) ? wp_get_attachment_image_src( $custom_logo_id , 'full' )[0] : (get_avatar_url($author_id) ?: '');
  $logo_measures = wp_get_attachment_image_src( $custom_logo_id , 'full'); //retrieve and array (url, width, height)
	//publisher (name), by default name of the website
	$publisher = get_bloginfo();
	//type of publisher
	//TODO when Google will support Person type for publisher, check type in SEO plugins
	$type = 'Organization';
	/*--- end Publisher ---*/

  // get the thumbnail of the post/page/homepage/ecc...
	$thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');

  /*--- Checking main SEO plugin for publisher information ---*/
	//include to check if YOAST or SEO Framework are installed
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	//if YOAST plugin is active, get company or person name to set for publisher property
	if (is_plugin_active('wordpress-seo/wp-seo.php')){

		$all_vals = get_option('wpseo_titles');

		if (!empty($all_vals)){

			$publisher = 'company' == $all_vals['company_or_person'] ? $all_vals['company_name'] : ('person' == $all_vals['company_or_person'] ? $all_vals['person_name'] : get_bloginfo()) ;

			if ($all_vals['company_logo'] && 'company' == $all_vals['company_or_person']){
				$logo = $all_vals['company_logo'];
        $logo_measures = wp_get_attachment_image_src( $all_vals['company_logo_id'], 'full'); //retrieve and array (url, width, height)
			}
		}
	}

	//if SEO Framework plugin is active, get company or person name to set for publisher property
	if (is_plugin_active('autodescription/autodescription.php')){

		$all_vals = get_option('autodescription-site-settings');

		if (!empty($all_vals)){

			$publisher = $all_vals['knowledge_name'] ?: get_bloginfo();

			if ($all_vals['knowledge_logo_url'] && 'organization' == $all_vals['knowledge_type']){
				$logo = $all_vals['knowledge_logo_url'];
        $logo_measures = wp_get_attachment_image_src( $all_vals['knowledge_logo_id'], 'full'); //retrieve and array (url, width, height)
			}
		}
	}
	/*--- end check plugin SEO ---*/
  /*----- end Data, related to 'CreativeWork' properties */



	$image = $thumbnail_url ?: $logo;
  $html = '';
  if(get_option('smd_hide_metadata_dates')){
    //Hide date options is activated in network menu or site munu
    $html .= '"dateModified":   "'.$last_modification_date.'",' . "\n\t";
  }else{
    $html .= '"dateCreated" :   "'.$creation_date.'",
    "dateModified":   "'.$last_modification_date.'",
    "datePublished":  "'.$publication_date.'",' . "\n\t";
  }
  $html .= '"inLanguage":     "'.$language.'",
    "headline":       "'.$title.'",';

  if(!empty($thumbnail_url)){
    $html .= '
    "thumbnailUrl":   "'.$thumbnail_url.'",';
  }

  if('G' == get_option('avatar_rating')){
    // The content is family friendly
    // It's selected in settings->discussion "G — Suitable for all audiences"
    $html .= '
    "isFamilyFriendly": "true",';
  }

  //array of types, which support 'Article' type fields
	$supported_types = ['Article', 'AdvertiserContentArticle', 'BlogPosting', 'DiscussionForumPosting', 'LiveBlogPosting',	'Report', 'SatiricalArticle' , 'SocialMediaPosting', 'TechArticle'];

	//adding 'Article' properties to supported types
	if(in_array($post_meta_type, $supported_types)){
    $html .= '
    "articleBody":  "'.$post_content.'",
    "articleSection": "'.$categories_string.'",
    "wordCount":  "'.$word_count.'",';
	}

  if( has_post_thumbnail() ){
    // The feature image is set
    //Get all attributes
    $img_thumbnail_title = get_post(get_post_thumbnail_id())->post_title;
    $img_caption = get_post(get_post_thumbnail_id())->post_excerpt;
    $img_description = get_post(get_post_thumbnail_id())->post_content;
    $img_url = get_the_post_thumbnail_url();
    $img_author = get_the_author_meta('display_name', get_post(get_post_thumbnail_id())->post_author);
    $img_date = get_post_time('F j, Y g:i a', get_post_thumbnail_id());
    $img_type = get_post_mime_type(get_post_thumbnail_id());
    $img_measures = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full'); //retrieve and array (url, width, height)
    $img_size = size_format(filesize(get_attached_file(get_post_thumbnail_id())));

    $html .= '
    "image": {
      "@type": "ImageObject",
      "name": "'.$img_thumbnail_title.'",
      "caption": "'.$img_caption.'",
      "description": "'.$img_description.'",
      "url": "'.$img_url.'",
      "uploadDate": "'.$img_date.'",
      "encodingFormat": "'.$img_type.'",
      "width": "'.$img_measures[1].'",
      "height": "'.$img_measures[2].'",
      "contentSize": "'.$img_size.'",
      "author": {
        "@type": "Person",
        "name": "'.$img_author.'"
      }
    },';
  }else{
    $html .= '
    "image": "'.$logo.'",';
  }



  $html .='
    "publisher": {
      "@type":  "'.$type.'",
      "name": "'.$publisher.'",
      "logo": {
        "@type":  "ImageObject",
        "url": "'.$logo.'",
        "width": "'.$logo_measures[1].'",
        "height": "'.$logo_measures[2].'"
      }
    },
    "author": {
      "@type":  "Person",
      "name":  "'.$author.'"
    }';

	return $html;
}


/**
* Function for getting all post types of installation
*
* @since
*
*/

 function smd_get_all_post_types(){
 	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
 	//Gathering the post types that are public including the wordpress ones if pressbooks is disabled
 	if(!is_plugin_active('pressbooks/pressbooks.php')){

 		$postTypes = array_keys( get_post_types( array( 'public' => true )) );
 		$postTypes1 =array_reverse($postTypes);

 		//we unset attachment sa it needs different markup (for future)
 		if (($key = array_search('attachment', $postTypes1)) !== false){
 			unset($postTypes1[$key]);
 		}
 	}else{
 		$postTypes1 = ['metadata','front-matter','chapter','part', 'back-matter'];
 	}
 	return $postTypes1;
 }

 /**
 * Check if the post type is a subtype of Creative Work
 *
 * Used in smd_annotation and smd_lifecycle to make them work only if the post is creative work
 *
 * @since 1.3
 * @param int $post_id the Id of the post to check
 * @return boolean
 */
 function smd_is_post_CreativeWork($post_id){

 	// Retrieve the post_meta_type choosen in the Post type metaboxe
 	if('page' == get_post_type($post_id) && !is_front_page()){
 		$post_meta_type = get_post_meta($post_id, 'smd_page_type', true) ?: 'no_type';
 		$creative_works_arr = ['WebPage' , 'AboutPage'  , 'CheckoutPage'  ,
 		'CollectionPage', 'ContactPage' , 'FAQPage'	 , 'ImageGallery' ,
 		'ItemPage', 'MedicalWebPage' , 'ProfilePage' ,'SearchResultsPage', 'VideoGallery' ];
 	}
 	else if('site-meta' ==  get_post_type($post_id) || is_front_page()){
		$post_meta_type = get_option('smd_website_blog_type') ?: 'no_type';
 		$creative_works_arr = ['WebSite', 'Blog', 'Course', 'Book' ];
 	}
  else{
    // all others types of post
 		$post_meta_type = get_post_meta($post_id, 'smd_post_type', true) ?: 'no_type';
 		$creative_works_arr = ['Article', 'AdvertiserContentArticle', 'BlogPosting',
 		'DiscussionForumPosting', 'LiveBlogPosting', 'Report', 'SatiricalArticle', 'SocialMediaPosting',
 		'TechArticle', 'Chapter', 'WebPage' ];
  }

 	return in_array($post_meta_type, $creative_works_arr);
 }

 /**
  * Overwrite with it the local option in all sites
  *
  * @since 1.4
  *
  * @param string $option_local_name The name of site option that you want to overwrite
  * @param string $option_value The value of the option to overwrite
  */
 function smd_net_overwrite_in_all_sites( $option_local_name, $option_value ){

   //Wordpress Database variable for database operations
   global $wpdb;

 	//Grabbing all the site IDs
   $siteids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

   //Going through the sites
   foreach ($siteids as $site_id) {
   	if (1 == $site_id){
   		continue;
   	}

   	switch_to_blog($site_id);

   	//updating local options obly if some option is selected
   	if ('0' !== $option_value){
   		update_option($option_local_name, $option_value);
   	}
   }

   restore_current_blog();
 }
