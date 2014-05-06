<?php
/**
* Plugin Name: Upcoming Events
* Plugin URI: http://zacharylinke.com
* Descritpion: A plugin to create events with repeating date ability
* Version: 1.0
* Author: Zachary Linke
* Author URI: http://zacharylinke.com
* License: GPL2
*/

// DEFINE PATH CONSTANTS
define( 'ROOT', plugins_url( '', __FILE__ ) );
define( 'IMAGES', ROOT . '/img/' );
define( 'STYLES', ROOT . '/css/' );
define( 'SCRIPTS', ROOT . '/js/' );

/*************************************
** Register and enque events scripts
*************************************/
function upe_register_enqueue_scripts() {

	
}
add_action('wp_enqueue_scripts', 'upe_register_enqueue_scripts');


/**********************************
** ADD 'EVENT' CUSTOM POST TYPE
**********************************/
function uep_custom_post_type() {
	// 'EVENT' POST TYPE DATA
	// Labels for events
	$labels = array(
	    'name'                  =>   __( 'Events', 'uep' ),
	    'singular_name'         =>   __( 'Event', 'uep' ),
	    'add_new_item'          =>   __( 'Add New Event', 'uep' ),
	    'all_items'             =>   __( 'All Events', 'uep' ),
	    'edit_item'             =>   __( 'Edit Event', 'uep' ),
	    'new_item'              =>   __( 'New Event', 'uep' ),
	    'view_item'             =>   __( 'View Event', 'uep' ),
	    'not_found'             =>   __( 'No Events Found', 'uep' ),
	    'not_found_in_trash'    =>   __( 'No Events Found in Trash', 'uep' )
	);
	 
	// Default fields for events
	$supports = array(
	    'title',
	    'editor',
	    'excerpt'
	);
	 
	// Arguments for events
	$args = array(
	    'label'         =>   __( 'Events', 'uep' ),
	    'labels'        =>   $labels,
	    'description'   =>   __( 'A list of upcoming events', 'uep' ),
	    'public'        =>   true,
	    'show_in_menu'  =>   true,
	    'menu_icon'     =>   IMAGES . 'event.svg',
	    'has_archive'   =>   true,
	    'rewrite'       =>   true,
	    'supports'      =>   $supports
	);

	// Register event post type
	register_post_type( 'event', $args );	
}
add_action( 'init', 'uep_custom_post_type' );

/***********************************
** ADD EVENT INFO METABOX
***********************************/
function uep_add_event_info_metabox() {
    add_meta_box(
        'uep-event-info-metabox',
        __( 'Event Info', 'uep' ),
        'uep_render_event_info_metabox',
        'event',
        'side',
        'core'
    );
}
add_action( 'add_meta_boxes', 'uep_add_event_info_metabox' );

/**************************************
** CREATE EVENT INFO METABOX CONTENT
**************************************/
function uep_render_event_info_metabox( $post ) {
 
    // generate a nonce field
    wp_nonce_field( basename( __FILE__ ), 'uep-event-info-nonce' );
 
    // get previously saved meta values (if any)
    $event_start_date = get_post_meta( $post->ID, 'event-start-date', true );
    $event_end_date = get_post_meta( $post->ID, 'event-end-date', true );
    $event_venue = get_post_meta( $post->ID, 'event-venue', true );
 
    // if there is previously saved value then retrieve it, else set it to the current time
    $event_start_date = ! empty( $event_start_date ) ? $event_start_date : time();
 
    //we assume that if the end date is not present, event ends on the same day
    $event_end_date = ! empty( $event_end_date ) ? $event_end_date : $event_start_date;

   ?>
   <!-- EVENT START DATE -->
    <label for="uep-event-start-date"><?php _e( 'Event Start Date:', 'uep' ); ?></label>
        <input class="widefat uep-event-date-input" id="uep-event-start-date" type="text" name="uep-event-start-date" placeholder="Format: February 18, 2014" value="<?php echo date( 'F d, Y', $event_start_date ); ?>" />
 	<!-- EVENT END DATE -->
	<label for="uep-event-end-date"><?php _e( 'Event End Date:', 'uep' ); ?></label>
        <input class="widefat uep-event-date-input" id="uep-event-end-date" type="text" name="uep-event-end-date" placeholder="Format: February 18, 2014" value="<?php echo date( 'F d, Y', $event_end_date ); ?>" />
 	<!-- EVENT VENUE -->
	<label for="uep-event-venue"><?php _e( 'Event Venue:', 'uep' ); ?></label>
        <input class="widefat" id="uep-event-venue" type="text" name="uep-event-venue" placeholder="eg. Times Square" value="<?php echo $event_venue; ?>" />
   	<br/><br/>
   	<!-- EVENT REPEAT? -->
 	<label for="uep-event-repeat"><?php _e( 'Repeating Event?', 'uep' ); ?></label>
        <input class="widefat" id="uep-event-repeat" type="checkbox" name="uep-event-repeat" />
    <br/><br/>				
    <!-- EVENT REPEAT DATA -->
  	<fieldset id="uep-event-repeat-days" style="display:none;">
        <label for="uep-event-repeat-amount">Amount of repeat dates</label>
        <input type="number" id="uep-event-repeat-amount" name="uep-event-repeat-amount" min="2" max="20" />

  		<!-- EVENT REPEAT DAYS --> 		
 		<!--<legend><?php _e( 'Repeating Days', 'uep' ); ?></legend>
        <input class="widefat" type="checkbox" name="uep-event-repeat-days" value="<?php _e('Monday', 'uep'); ?>" /><?php _e('Monday', 'uep'); ?>
        <input class="widefat" type="checkbox" name="uep-event-repeat-days" value="<?php _e('Tuesday', 'uep'); ?>" /><?php _e('Tuesday', 'uep'); ?>
        <input class="widefat" type="checkbox" name="uep-event-repeat-days" value="<?php _e('Wednesday', 'uep'); ?>" /><?php _e('Wednesday', 'uep'); ?>
        <input class="widefat" type="checkbox" name="uep-event-repeat-days" value="<?php _e('Thursday', 'uep'); ?>" /><?php _e('Thursday', 'uep'); ?>
        <input class="widefat" type="checkbox" name="uep-event-repeat-days" value="<?php _e('Friday', 'uep'); ?>" /><?php _e('Friday', 'uep'); ?>
        <input class="widefat" type="checkbox" name="uep-event-repeat-days" value="<?php _e('Saturday', 'uep'); ?>" /><?php _e('Saturday', 'uep'); ?>
        <input class="widefat" type="checkbox" name="uep-event-repeat-days" value="<?php _e('Sunday', 'uep'); ?>" /><?php _e('Sunday', 'uep'); ?>
        <br/><br/>-->
        <!-- EVENT END REPEAT -->
    	<label for="uep-event-end-repeat-date"><?php _e( 'Event End Repeat Date:', 'uep' ); ?></label>
        	<input class="widefat uep-event-date-input" id="uep-event-end-repeat-date" type="text" name="uep-event-end-repeat-date" placeholder="Format: February 18, 2014" value="<?php echo date( 'F d, Y', $event_end_date ); ?>" />

    </fieldset>
    <br/><br/>
  
 <br/>
 <?php }

/***********************************************
** ENQUE SCRIPT AND STYLE FOR CALENDAR PICKER UI 
***********************************************/
function uep_admin_script_style( $hook ) {
    global $post_type;
 
    if ( ( 'post.php' == $hook || 'post-new.php' == $hook ) && ( 'event' == $post_type ) ) {

        // STEPPER UI
    	wp_enqueue_script(
            'stepper',
            SCRIPTS.'stepper/jquery.fs.stepper.min.js',
            'jquery',
             false,
            false
        );
		wp_enqueue_style(
            'stepper',
            SCRIPTS.'stepper/jquery.fs.stepper.css',
            false,
            false,
            'all'
        );
		
        // CALENDAR UI
        wp_enqueue_script(
            'upcoming-events',
            SCRIPTS . 'script.js',
            array( 'jquery', 'jquery-ui-datepicker' ),
            '1.0',
            true
        ); 
        wp_enqueue_style(
            'jquery-ui-calendar',
            STYLES . 'jquery-ui-1.10.4.custom.min.css',
            false,
            '1.10.4',
            'all'
        );

        // MAIN STYLES
        wp_enqueue_style(
            'styles',
            STYLES . 'style.css',
            false,
            false,
            'all'
        );
    }
}
add_action( 'admin_enqueue_scripts', 'uep_admin_script_style' );

/*******************
** SAVE EVENT INFO
*******************/
function uep_save_event_info( $post_id ) {
 
    // checking if the post being saved is an 'event',
    // if not, then return
    if ( 'event' != $_POST['post_type'] ) {
        return;
    }
 
    // checking for the 'save' status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST['uep-event-info-nonce'] ) && ( wp_verify_nonce( $_POST['uep-event-info-nonce'], basename( __FILE__ ) ) ) ) ? true : false;
 
    // exit depending on the save status or if the nonce is not valid
    if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
        return;
    }
 
    // checking for the values and performing necessary actions
    if ( isset( $_POST['uep-event-start-date'] ) ) {
        update_post_meta( $post_id, 'event-start-date', strtotime( $_POST['uep-event-start-date'] ) );
    }
 
    if ( isset( $_POST['uep-event-end-date'] ) ) {
        update_post_meta( $post_id, 'event-end-date', strtotime( $_POST['uep-event-end-date'] ) );
    }
 
    if ( isset( $_POST['uep-event-venue'] ) ) {
        update_post_meta( $post_id, 'event-venue', sanitize_text_field( $_POST['uep-event-venue'] ) );
    }
}
add_action( 'save_post', 'uep_save_event_info' );

/*******************************************
** CREATE EVENT DATA COLUMNS IN EVENTS ADMIN
*******************************************/
function uep_custom_columns_head( $defaults ) {
    unset( $defaults['date'] );
 
    $defaults['event_start_date'] = __( 'Start Date', 'uep' );
    $defaults['event_end_date'] = __( 'End Date', 'uep' );
    $defaults['event_venue'] = __( 'Venue', 'uep' );
 
    return $defaults;
}
add_filter( 'manage_edit-event_columns', 'uep_custom_columns_head', 10 );

/********************************************
** SHOW EVENT DATA IN EVENTS ADMIN
********************************************/
function uep_custom_columns_content( $column_name, $post_id ) {
 
    if ( 'event_start_date' == $column_name ) {
        $start_date = get_post_meta( $post_id, 'event-start-date', true );
        echo date( 'F d, Y', $start_date );
    }
 
    if ( 'event_end_date' == $column_name ) {
        $end_date = get_post_meta( $post_id, 'event-end-date', true );
        echo date( 'F d, Y', $end_date );
    }
 
    if ( 'event_venue' == $column_name ) {
        $venue = get_post_meta( $post_id, 'event-venue', true );
        echo $venue;
    }
}
add_action( 'manage_event_posts_custom_column', 'uep_custom_columns_content', 10, 2 );



