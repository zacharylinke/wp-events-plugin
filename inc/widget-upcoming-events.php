<?php
class Upcoming_Events extends WP_Widget {

	public function __construct() {

	}

	public function form( $instance ) {

	}

	public function update( $new_instance, $old_instance ) {

	}

	public function widget( $args, $instance ) {
		
	}

	function uep_register_widget(){
		register_widget( 'Upcoming Events' );
	}
	add_action( 'widgets_init', 'uep_register_widget' );
}