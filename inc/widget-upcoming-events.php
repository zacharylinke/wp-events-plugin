<?php

//error_reporting('E_ALL');

class Upcoming_Events extends WP_Widget {

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

	public function form( $instance ) {

		$widget_defaults = array(
		    'title'         =>   'Upcoming Events',
		    'number_events' =>   5,
		    'excerpt_link_text' => 'Read More'
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

		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_link_text' ); ?>"><?php _e('Excerpt Link Text'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'excerpt_link_text'); ?>" name="<?php echo $this->get_field_name( 'excerpt_link_text'); ?>" class="widefat" value="<?php echo esc_attr( $instance['excerpt_link_text'] ); ?>">
		</p>

		<?php

	}

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		 
		$instance['title'] = $new_instance['title'];
		$instance['number_events'] = $new_instance['number_events'];
		$instance['excerpt_link_text'] = $new_instance['excerpt_link_text'];

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
		    'posts_per_page'        =>   -1,
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
			        $event_repeat_days = get_post_meta( get_the_ID(), 'event-repeat-days', true );
			        $end_repeat_date = get_post_meta( get_the_ID(), 'end-repeat-date', true );
			        $repeat_frequency = get_post_meta( get_the_ID(), 'repeat-frequency', true );
			        $increment_current = '+1 week';		

			        // SET FREQUENCY FILTER
					switch($repeat_frequency){

						case 'weekly':
							$increment_current = '+1 week';
							break;

						case 'bi-weekly':
							$increment_current = '+2 week';
							break;

						case 'monthly':
							$increment_current = '+1 month';
							break;
					}

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

				        	// CREATE DATE TIME OBJECT FOR EVENT START DATE
				        	$start_date_time = new DateTime(date('Y-m-d',$event_start_date));
				        	$repeat_end_date_time = new DateTime(date('Y-m-d',$end_repeat_date));				        	

				        	// ASSIGN START DATE TO CURRENT DATE
				        	$current_date = $start_date_time;				        	

				        	// ARRAY TO HOLD DAY INCREMENTS
				        	$repeat_days_wday = array();				        

				        	// WHILE THE CURRENT DATE IS LESS THAN THE REPEAT END DATE
				        	while($current_date < $repeat_end_date_time) {

				        		// LOOP THROUGH THE ASSIGNED REPEAT DAYS FOR THIS EVENT				        		
				        		foreach($event_repeat_days as $day){

			        				// CLONE CURRENT DATE AS ADD_DATE
					        		$add_date = clone $current_date;
					        		// INCREMENT DATE TO NEXT DAY VALUE
					        		$add_date->modify("+1 {$day}");
					        		// PUSH DATE AND POST ID TO EVENT ARRAY
					        		$event_items[strtotime($add_date->format('Y-m-d'))] = get_the_ID();
						        						        	
						        }
						        // INCREMENT CURRENT DATE BY 1 WEEK
						        $current_date->modify($increment_current);
				        	}			        	
				        } // close "repeat type" check			        

			        // NO REPEAT, JUST GET THE START DATE ONLY FOR THIS EVENT
			        }else{
			        	$event_items[$event_start_date] = get_the_ID();
			        }
			    ?>

			    <?php endwhile; ?>

			    <?php		
			    	// TODAY'S DATE
			    	$today_date = new DateTime('now');
			    	// CONVERT TO YESTERDAY'S DATE	
			    	$today_date->sub(new DateInterval('P1D'));
			    	// CONVERT TO STRTOTIME
			    	$today_date = strtotime($today_date->format('Y-m-d'));
			    	// HOLD EVENT COUNT
			    	$event_count = 0;

			    	// SORT THE EVENT ITEMS ASCENDING BY KEY(TIMESTAMP)
			    	ksort($event_items);
			    	// PRINT MARKUP FOR EACH EVENT ITEM
			    	foreach($event_items as $key => $value){
			    		// ADD ONE TO EVENT COUNT
			    		$event_count ++;

			    		// IF THE EVENT IS TODAY OR LATER && EVENT COUNT IS LESS THAN EVENT COUNT SETTING, THEN SHOW THE EVENT
			    		if($key > $today_date && $event_count <= $instance['number_events']){

			    ?>
			    		<li class="uep_event_entry">
			    			<h4><a class="uep_event_title" href="<?php echo get_the_permalink($value); ?>"><?php echo get_the_title($value); ?></a></h4>
			    			<time class="uep_event_date"><?php echo date('F d, Y', $key); ?></time>
			    			<p><?php echo get_excerpt_by_id($value, $instance); ?></p>
			    		</li>

			    <?php } } ?>
			</ul>
			 
			<a href="<?php echo get_post_type_archive_link( 'event' ); ?>">View All Events</a>
			 
			<?php
			wp_reset_query();
			 
			echo $after_widget;

	}
	
}

function get_excerpt_by_id($post_id, $instance, $excerpt_length = 35){
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