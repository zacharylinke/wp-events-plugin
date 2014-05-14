<?php

//error_reporting('E_ALL');

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
		$auto_repeat_dates = array();

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
			        $manual_repeat_dates = get_post_meta( get_the_ID(), 'manual-repeat-dates', true );
			        $event_repeat_type = get_post_meta( get_the_ID(), 'event-repeat-type', true );
			        $event_repeat_days = get_post_meta( get_the_ID(), 'event-repeat-days', true);
			        $end_repeat_date = get_post_meta( get_the_ID(), 'end-repeat-date', true);

			       

			        // IF THIS POST HAS REPEAT DATES
			        if($event_repeat == 'repeat'){

			        	// MANUAL REPEAT TYPE
			        	if($event_repeat_type == 'manual'){
				        	// CONVERT THE REPEAT DATE STRING TO ARRAY
				        	$manual_repeat_dates = explode(',',$manual_repeat_dates);

			        		// LOOP THROUGH THE DATES
				        	foreach($manual_repeat_dates as $date){
				        		
				        		// PUSH DATE AS KEY, POST ID AS VALUE
				        		$event_items[strtotime($date)] = get_the_ID();
				        		
				        	}

				        // AUTO REPEAT TYPE	
				        }else{
				        	// START DATE DAY
				        	$start_date_data = getdate( $event_start_date );

				        	// ARRAY TO HOLD DAY INCREMENTS
				        	$repeat_days_wday = array();

				        	// LOOP THROUGH THE ASSIGNED REPEAT DAYS
				        	// if the assigned day is present, then add it's increment to array
				        	foreach($event_repeat_days as $day){
				        		switch($day){

				        			case 'Monday':
				        				$repeat_days_wday[] = 'P1D';
				        				break;

				        			case 'Tuesday':
				        				$repeat_days_wday[] = 'P2D';
				        				break;

				        			case 'Wednesday':
				        				$repeat_days_wday[] = 'P3D';
				        				break;

				        			case 'Thursday':
				        				$repeat_days_wday[] = 'P4D';
				        				break;

				        			case 'Friday':
				        				$repeat_days_wday[] = 'P5D';
				        				break;

				        			case 'Saturday':
				        				$repeat_days_wday[] = 'P6D';
				        				break;

				        			case 'Sunday':
				        				$repeat_days_wday[] = 'P7D';
				        				break;						

				        		}
				        	}

				        	// CREATE DATE TIME OBJECT FOR EVENT START DATE
				        	$start_date_time = new DateTime(date('Y-m-d',$event_start_date));				        	
				        	

				        	// LOOP THROUGH REPEATING DAY INCREMENTS
				        	foreach($repeat_days_wday as $new_day){
				        		//echo $new_day;
				        		// TEMP DATE TO HOLD MODIFIED DATE VALUE
				        		$temp_date = $start_date_time;
				        		// CREATE NEW MODIFIED DATE
				        		$temp_date->add(new DateInterval($new_day));				        	
				        		// ADD MODIFIED DATE TO ARRAY
				        		$auto_repeat_dates[] = $event_items[strtotime($temp_date->format('Y-m-d'))] = get_the_ID();

				        	}

				        	// LOOP THROUGH THE DATES
				        	/*foreach($auto_repeat_dates as $date){
				        		
				        		// PUSH DATE AS KEY, POST ID AS VALUE
				        		$event_items[strtotime($date)] = get_the_ID();
				        		
				        	}*/
				        	
				        } // close "repeat type" check

			        

			        // NO REPEAT, JUST GET THE START DATE ONLY FOR THIS EVENT
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

			    	// SORT THE EVENT ITEMS ASCENDING BY KEY(TIMESTAMP)
			    	ksort($event_items);
			    	// PRINT MARKUP FOR EACH EVENT ITEM
			    	foreach($event_items as $key => $value){

			    ?>
			    		<li class="uep_event_entry"><h4><a class="uep_event_title" href="<?php echo get_the_permalink($value); ?>"><?php echo get_the_title($value); ?></a><p><?php echo get_excerpt_by_id($value); ?></p><time class="uep_event_date"><?php echo date('F d, Y', $key); ?></time></li>

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