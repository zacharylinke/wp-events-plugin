<?php

class Upcoming_Events extends WP_Widget {

	public function __construct() {

		$test = "nothing";

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

	public function form( $instance ) {

		$widget_defaults = array(
		    'title'         =>   'Upcoming Events',
		    'number_events' =>   5
		);
 
		$instance  = wp_parse_args( (array) $instance, $widget_defaults );

		?>

		<p>
		    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'uep' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" class="widefat" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
		    <label for="<?php echo $this->get_field_id( 'number_events' ); ?>"><?php _e( 'Number of events to show', 'uep' ); ?></label>
		    <select id="<?php echo $this->get_field_id( 'number_events' ); ?>" name="<?php echo $this->get_field_name( 'number_events' ); ?>" class="widefat">
		        <?php for ( $i = 1; $i <= 10; $i++ ): ?>
		            <option value="<?php echo $i; ?>" <?php selected( $i, $instance['number_events'], true ); ?>><?php echo $i; ?></option>
		        <?php endfor; ?>
		    </select>
		</p>

		<?php

	}

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		 
		$instance['title'] = $new_instance['title'];
		$instance['number_events'] = $new_instance['number_events'];

		return $instance;

	}

	public function widget( $args, $instance ) {

		extract( $args );
    	$title = apply_filters( 'widget_title', $instance['title'] );

    	$meta_quer_args = array(
		    'relation'  =>   'OR',
		    array(
		        'key'       =>   'event-repeat',
		        'value'     =>   'repeat',
		        'compare'   =>   '='
		    ),
		    array(
		    	'key'		=>   'event-end-date',
		    	'value'		=>	 time(),
		    	'compare'	=>   '>='
		    )
		);

 
		$query_args = array(
		    'post_type'             =>   'event',
		    'posts_per_page'        =>   $instance['number_events'],
		    'post_status'           =>   'publish',
		    'ignore_sticky_posts'   =>   true,
		    'meta_key'              =>   'event-start-date',
		    'orderby'               =>   'meta_value_num',
		    'order'                 =>   'ASC',
		    'meta_query'            =>   $meta_quer_args
		);
		 
		$upcoming_events = new WP_Query( $query_args );

		//print_r($upcoming_events);
		// EVENTS ARRAY FOR DISPLAY
		$event_items = array();

		echo $before_widget;
			if ( $title ) {
			    echo $before_title . $title . $after_title;
			}
			?>
			 
			<ul class="uep_event_entries">
			    <?php while( $upcoming_events->have_posts() ): $upcoming_events->the_post();
			    	
			    	// GET ALL POST META
			        $event_start_date = get_post_meta( get_the_ID(), 'event-start-date', true );
			        $event_end_date = get_post_meta( get_the_ID(), 'event-end-date', true );
			        $event_venue = get_post_meta( get_the_ID(), 'event-venue', true );
			        $event_repeat = get_post_meta( get_the_ID(), 'event-repeat', true );
			        $manual_repeat_dates = get_post_meta( get_the_ID(), 'manual-repeat-dates', true); 

			       // echo $post->post_name;

			        // IF THIS POST HAS REPEAT DATES
			        if($event_repeat == 'repeat'){
			        	// CONVERT THE REPEAT DATE STRING TO ARRAY
			        	$repeat_dates = explode(',',$manual_repeat_dates);
			        	// LOOP THROUGH THE DATES
			        	foreach($repeat_dates as $date){
			        		//echo $date.'<br/>';
			        		// PUSH DATE AS KEY, POST ID AS VALUE
			        		$event_items[strtotime($date)] = get_the_ID(); 
			        	}



			        	// GET REPEAT DATES
			        	// convert repeat date strings to dates
			   			// loop through dates
			        	// create excerpt item for date
			        	// add to array 


			        	//echo '<h1>'.$event_repeat.'</h1>';
			        }else{
			        	$event_items[$event_start_date] = get_the_ID();
			        }


			    ?>

			    <!--    <li class="uep_event_entry">
			            <h4><a href="<?php the_permalink(); ?>" class="uep_event_title"><?php the_title(); ?></a> <span class="event_venue">at <?php echo $event_venue; ?></span></h4>
			            <?php the_excerpt(); ?>
			            <time class="uep_event_date"><?php echo date( 'F d, Y', $event_start_date ); ?> &ndash; <?php echo date( 'F d, Y', $event_end_date ); ?></time>
			        </li> -->
			    <?php endwhile; ?>

			    <?php 
			    	ksort($event_items);
			    	foreach($event_items as $key => $value){

			    ?>
			    		<li><h4><a href="<?php echo get_the_permalink($value); ?>"><?php echo get_the_title($value); ?></a><p><?php echo get_excerpt_by_id($value); ?></p><span><?php echo date('F d, Y', $key); ?></span></li>

			    <?php } ?>
			</ul>
			 
			<a href="<?php echo get_post_type_archive_link( 'event' ); ?>">View All Events</a>
			 
			<?php
			wp_reset_query();
			 
			echo $after_widget;

	}
	
}

function get_excerpt_by_id($post_id, $excerpt_length = 35){
	$the_post = get_post($post_id); //Gets post ID
	//check if the cutom post excerpt has content
	if(!empty($the_post->post_excerpt)){
		$the_excerpt = $the_post->post_excerpt;//make the custom post excerpt the excerpt 
	}else{
	$the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
	$the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
	$words = explode(' ', $the_excerpt, $excerpt_length + 1);
	if(count($words) > $excerpt_length) :
	array_pop($words);
	array_push($words, '…');
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