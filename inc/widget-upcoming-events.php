<?php

include('inc/EventPosts.class.php');

/**
 * 
 * @package Upcoming Events
 */
class Upcoming_Events extends WP_Widget {


	/**
	 * Initiate Events widget extending WordPress WP_Widget parent class.
	 * 
	 * @param array $widget_ops Set class and description for events widget.
	 *  
	 *
	 */
	public function __construct() {

		$widget_ops = array(
	        'class'         =>   'uep_upcoming_events',
	        'description'   =>   __( 'A widget to display a list of upcoming events', 'uep' )
	    );
	 
	    parent::__construct(
	        'uep_upcoming_events',          //base id
	        __( 'Upcoming Events', 'uep' ), //title
	        $widget_ops
	    );

	}

	/**
	 * Builds event widget form inputs for admin.
	 * 
	 * @param array $instance Settings of widget instance set in dashboard.
	 * @param array $widget_defaults Default widget settings
	 * 
	 */
	public function form( $instance ) {

		$widget_defaults = array(
		    'title'         =>   'Upcoming Events',
		    'number_events' =>   5,
		    'excerpt_link_text' => 'Read More',
		    'excerpt_word_length' => 25
		);
 
		$instance  = wp_parse_args( (array) $instance, $widget_defaults );

		?>

		<p>
		    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'uep' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
		    <label for="<?php echo $this->get_field_id( 'number_events' ); ?>"><?php _e( 'Number of events to show', 'uep' ); ?></label>
		    <input type="number" min="1" max="20" step="1" id="<?php echo $this->get_field_id( 'number_events' ); ?>" name="<?php echo $this->get_field_name( 'number_events' ); ?>" class="widefat number-events_widget_admin" value="5" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_link_text' ); ?>"><?php _e('Excerpt Link Text'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'excerpt_link_text'); ?>" name="<?php echo $this->get_field_name( 'excerpt_link_text'); ?>" class="widefat" value="<?php echo esc_attr( $instance['excerpt_link_text'] ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_word_length' ); ?>"><?php _e('Excerpt Word Length'); ?></label>
			<input type="number" min="10" max="100" step="1" id="<?php echo $this->get_field_id( 'excerpt_word_length'); ?>" name="<?php echo $this->get_field_name( 'excerpt_word_length'); ?>" class="widefat excerpt-length_widget_admin" value="<?php echo esc_attr( $instance['excerpt_word_length'] ); ?>">
		</p>

		<?php

	}

	/**
	 * Updates UEP widget instance settings.
	 * 
	 * @param array $new_instance New settings for widget instance.
	 * @param array $old_instance Old settings for widget instance.
	 * @return array $instance Updated settings for widget instance.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		 
		$instance['title'] = $new_instance['title'];
		$instance['number_events'] = $new_instance['number_events'];
		$instance['excerpt_link_text'] = $new_instance['excerpt_link_text'];
		$instance['excerpt_word_length'] = $new_instance['excerpt_word_length'];

		return $instance;

	}

	/**
	 * Description
	 * @param array $args 
	 * @param array $instance 
	 * @return type
	 */

	public function widget( $args, $instance ) {

		extract( $args );
    	$title = apply_filters( 'widget_title', $instance['title'] );
	

		echo $before_widget;
			if ( $title ) {
			    echo $before_title . $title . $after_title;
			}

			// Get new Event Posts object
			$get_events = new EventPosts();

		?>

		<ul class="uep_event_entries">
		</ul>
		
		<?php
			// Loop through the event posts method
			foreach ($get_events->get_display_posts($instance['number_events'], true) as $key => $value) {				
		?>		
			   
	    		<li class="uep_event_entry">
	    			<h4><a class="uep_event_title" href="<?php echo get_the_permalink($value); ?>"><?php echo get_the_title($value); ?></a></h4>
	    			<time class="uep_event_date"><?php echo date('F d, Y', $key); ?></time>
	    			<p><?php echo get_excerpt_by_id($value, $instance); ?></p>
	    		</li>

		<?php } ?>

		</ul>
		 
		<a href="<?php echo get_post_type_archive_link( 'event' ); ?>">View All Events</a>
		 
		<?php
		wp_reset_query();
		 
		echo $after_widget;

	}
	
}

/**
 * Gets a post excerpt based on the passed id. Also accepts widget instance settings.
 * 
 * @param int $post_id The post ID to retrieve the excerpt from.
 * @param array $instance Widget instance settings; 'excerpt_word_length' and 'excerpt_link_text'
 * @return string $the_excerpt Returns the post excerpt and a link to the post.
 */
function get_excerpt_by_id($post_id, $instance){
	$the_post = get_post($post_id); //Gets post ID
	//check if the cutom post excerpt has content
	if(!empty($the_post->post_excerpt)){
		$the_excerpt = $the_post->post_excerpt;//make the custom post excerpt the excerpt 
	}else{
	$the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
	$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
	$words = explode(' ', $the_excerpt, $instance['excerpt_word_length'] + 1);
	if(count($words) > $instance['excerpt_word_length']) :
	array_pop($words);
	array_push($words, '<a class="uep_excerpt_link" href="'.get_permalink($post_id).'">'.$instance['excerpt_link_text'].'</a>');
	$the_excerpt = implode(' ', $words);
	endif;	
	}
	$the_excerpt = '<p>' . $the_excerpt . '</p>';
	return $the_excerpt;
}

function uep_register_widget(){
	register_widget( 'Upcoming_Events' );
}
add_action( 'widgets_init', 'uep_register_widget' );