<?php
/*
Plugin Name: Image Automatic Alt Text and Caption
Plugin URI: http://wordpress.org/extend/plugins/image-automatic-alt-text-and-caption/
Author: natekinkead
Author URI: https://wpforthewin.com
Description: Automatically generates alt text and caption for new image uploads. 
Version: 1.0.0
License: GPLv3
*/

add_action( 'add_attachment', 'wpftw_set_image_meta_upon_upload' );

function wpftw_set_image_meta_upon_upload( $post_ID ) {
	// Check if uploaded file is an image, else do nothing
	if ( wp_attachment_is_image( $post_ID ) ) {
		$my_image_title = get_post( $post_ID )->post_title;
		// Clean up the title: remove hyphens, underscores & extra spaces:
		$my_image_title = preg_replace( '%\s*[-_\s]+\s*%', ' ',	$my_image_title );
		// Clean up the title: capitalize first letter of every word:
		$my_image_title = ucwords( $my_image_title );
		// Create an array with the image meta (Title, Caption, Description) to be updated
		// Note: comment out the Excerpt/Caption or Content/Description lines if not needed
		$my_image_meta = array(
			// Specify the image (ID) to be updated
			'ID' => $post_ID,
			// Set image Title to sanitized title
			'post_title' => $my_image_title,
			// Set image Caption (Excerpt) to sanitized title
			'post_excerpt' => $my_image_title,
			// Set image Description (Content) to sanitized title
			'post_content' => $my_image_title,
		);

		// Set the image Alt-Text
		update_post_meta( $post_ID, '_wp_attachment_image_alt',	$my_image_title );
		// Set the image meta (e.g. Title, Excerpt, Content)
		wp_update_post( $my_image_meta );
	}
}

add_filter('image_send_to_editor', 'wpftw_auto_alt_fix_1', 10, 2);

function wpftw_auto_alt_fix_1($html, $id) {
	return str_replace('alt=""','alt="'.get_the_title($id).'"',$html);
}

add_filter('wp_get_attachment_image_attributes', 'wpftw_auto_alt_fix_2', 10, 2);

function wpftw_auto_alt_fix_2($attributes, $attachment){
	if ( !isset( $attributes['alt'] ) || '' === $attributes['alt'] ) {
		$attributes['alt']=get_the_title($attachment->ID);
	}
	return $attributes;
}