<?php
/**
* Plugin Name: WP-OBS Free Sidebar Widget
* Plugin URI: http://wp-obs.com
* Description: WP-OBS Sidebar Widget is a free add-on that will only work with already installed and activated WP-OBS PRO. Free trial can be downloaded at:<a href="http://wp-online-booking-system.com"> here</a>
* Author: WP-OBS
* Version: 1.0.0.0
* Author URI: http://wp-obs.com
* Copyright 2013 WP-OBS (email: support@wp-obs.com)
*/

add_action( 'widgets_init', function(){
     register_widget( 'WPOBS_Widget' );
     add_action( 'admin_enqueue_scripts', 'wp_obs_widget_enqueue_color_picker' );		
});


function wp_obs_widget_enqueue_color_picker( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' ); 
    
    wp_enqueue_script(
		'wp-obs-widget',
		plugins_url( '/wp-obs-widget.js' , __FILE__ )

	);
}


class WPOBS_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'wpobs_widget',
			'WP OBS Widget',
			array( 
				'description' => __( 'A widget that displays the WP OBS available services.', 'text_domain' ), 
				
			),
			array(
				'width' => 251
			)		
		);
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		if(!file_exists(WP_PLUGIN_DIR."/wp-obs/php/classes.php") || !is_plugin_active('wp-obs/wp-obs.php')){
			echo 'This widget must have the WP-OBS Appointments Booking System Plugin Installed and Activated in order to work. To download a free trial visit the WP-OBS website: <a href="http://www.wp-obs.com" target="_blank">www.wp-obs.com</a>.';
		}else{
			include_once(WP_PLUGIN_DIR."/wp-obs/php/classes.php");
			$wpOBSServices = new WPOBSService('', 'public');			
			$wpOBSServices = $wpOBSServices->getServices();
			
			extract( $args );
			
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) ){
				echo $before_title;
				if($instance['link'] != ''){
					if($instance['title_color'] != ''){
						$title_color = 'color:'.$instance['title_color'].';';
					}	
					if($instance['title_font_size'] != ''){
						$title_font_size = 'font-size:'.$instance['title_font_size'].'px;';
					}	
					echo '<a href="'.$instance['booking_page'].'" style="'.$title_color.$title_font_size.'">'.$title.'</a>';	
				}else{
					if($instance['title_color'] != ''){
						$title_color = 'color:'.$instance['title_color'].';';
					}	
					if($instance['title_font_size'] != ''){
						$title_font_size = 'font-size:'.$instance['title_font_size'].'px;';
					}
				 	echo '<span style="'.$title_color.$title_font_size.'">'.$title.'</span>';
				} 
				echo $after_title;
			}
			if($instance['book_now'] == ''){
				echo '<ul>';
				
				if($instance['link_color'] != ''){
					$link_color = 'color:'.$instance['link_color'].';';
				}	
				if($instance['link_font_size'] != ''){
					$link_font_size = 'font-size:'.$instance['link_font_size'].'px;';
				}
				
				if($instance['all_services'] != ''){ //display all services				
					foreach($wpOBSServices as $k=>$v){
						echo '<li><a href="#'.$v->getId().'" style="'.$link_color.$link_font_size.'" onClick="frontend2(this);" class="wp_obs_booking_front">'.$v->getName().'</a></li>';
					}
				}else{ //display only selected services
					foreach($wpOBSServices as $k=>$v){
						if($instance['service_'.$v->getId()] != ''){
							echo '<li><a href="#'.$v->getId().'" style="'.$link_color.$link_font_size.'" onClick="frontend2(this);" class="wp_obs_booking_front">'.$v->getName().'</a></li>';
						}
					}
				}
				
				echo '</ul>';
			}else{
				echo '<p><a href="#" onClick="frontend2();" class="wp_obs_booking_front">'.html_entity_decode($instance['book_now_text']).'</a></p>';
			}
			echo $after_widget;
		}
	}

 	public function form( $instance ) {
 		// outputs the options form on admin
		if(!file_exists(WP_PLUGIN_DIR."/wp-obs/php/classes.php") || !is_plugin_active('wp-obs/wp-obs.php')){
			echo 'This widget must have the WP-OBS Appointments Booking System Plugin Installed and Activated in order to work. To download a free trial visit the WP-OBS website: <a href="http://www.wp-obs.com" target="_blank">www.wp-obs.com</a>.';
		}else{
			include_once(WP_PLUGIN_DIR."/wp-obs/php/classes.php");
			$wpOBSServices = new WPOBSService('', 'public');			
			$wpOBSServices = $wpOBSServices->getServices();
				
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}
			else {
				$title = __( 'Service list', 'text_domain' );
			}
			if($instance['link'] != ''){
				$link_checked = 'checked="checked"';
			}else{
				$link_checked = '';
			}
			if ( isset( $instance[ 'booking_page' ] ) ) {
				$booking_page = $instance[ 'booking_page' ];
			}
			else {
				$booking_page = __( '', 'text_domain' );
			}
			
			if($instance['all_services'] != ''){
				$all_services = 'checked="checked"';
			}else{
				$all_services = '';
			}						
			$services_checked = '';
			foreach($wpOBSServices as $k=>$v){
				if($instance['service_'.$v->getId()] != ''){
					$services_checked[$v->getId()] = 'checked="checked"';
				}else{
					$services_checked[$v->getId()] = '';
				}
			}
			if($instance['book_now'] != ''){
				$book_now = 'checked="checked"';
			}else{
				$book_now = '';
			}
			if ( isset( $instance[ 'book_now_text' ] ) ) {
				$book_now_text = $instance[ 'book_now_text' ];
			}
			else {
				$book_now_text = __( esc_html('<img src="'.get_bloginfo('wpurl').'/wp-content/plugins/wp-obs/images_booklink/book_online_hover.png" />'), 'text_domain' );
			}
			
			if ( isset( $instance[ 'title_color' ] ) ) {
				$title_color = $instance[ 'title_color' ];
			}
			else {
				$title_color = __( '', 'text_domain' );
			}
			
			if ( isset( $instance[ 'title_font_size' ] ) ) {
				$title_font_size = $instance[ 'title_font_size' ];
			}
			
			if ( isset( $instance[ 'link_color' ] ) ) {
				$link_color = $instance[ 'link_color' ];
			}
			else {
				$link_color = __( '', 'text_domain' );
			}
			
			if ( isset( $instance[ 'link_font_size' ] ) ) {
				$link_font_size = $instance[ 'link_font_size' ];
			}
			
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<input id="<?php echo $this->get_field_id( 'link' ); ?>" class="checkbox" type="checkbox" name="<?php echo $this->get_field_name( 'link' ); ?>" <?php echo $link_checked; ?>>
				<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Link widget title to booking page' ); ?></label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'booking_page' ); ?>"><?php _e( 'Booking page URL(optional):' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'booking_page' ); ?>" name="<?php echo $this->get_field_name( 'booking_page' ); ?>" type="text" value="<?php echo esc_attr( $booking_page ); ?>" />
			</p>
			<p><?php _e( 'Choose services to display:' ); ?></p>
			<p>
				<input id="<?php echo $this->get_field_id( 'all_services' ); ?>" class="checkbox wp-obs-all-services" type="checkbox" name="<?php echo $this->get_field_name( 'all_services' ); ?>" <?php echo $all_services; ?>>
				<label for="<?php echo $this->get_field_id( 'all_services' ); ?>"><?php _e( 'Display all services' ); ?></label>
			</p>
			<p>&nbsp;</p>
			<div style="overflow:auto;height:135px;">
			<?php
				foreach($wpOBSServices as $k=>$v):
			?>
			<p>
				<input id="<?php echo $this->get_field_id( 'service_'.$v->getId() ); ?>" class="checkbox wp-obs-service" type="checkbox" name="<?php echo $this->get_field_name( 'service_'.$v->getId() ); ?>" <?php echo $services_checked[$v->getId()]; ?>>
				<label for="<?php echo $this->get_field_id( 'service_'.$v->getId() ); ?>"><?php _e( $v->getName() ); ?></label>
			</p>
			<?php
				endforeach;
			?>
			</div>
			<br>
			<p>
				<input id="<?php echo $this->get_field_id( 'book_now' ); ?>" class="checkbox wp-obs-book-now" type="checkbox" name="<?php echo $this->get_field_name( 'book_now' ); ?>" <?php echo $book_now; ?>>
				<label for="<?php echo $this->get_field_id( 'book_now' ); ?>"><?php _e( 'Display book now button (HTML is allowed)' ); ?></label>
			</p>
			<p>
				<textarea class="widefat" cols="" rows="5" id="<?php echo $this->get_field_id( 'book_now_text' ); ?>" name="<?php echo $this->get_field_name( 'book_now_text' ); ?>"><?php echo esc_attr( $book_now_text ); ?></textarea>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'title_color' ); ?>"><?php _e( 'Title color:' ); ?></label><br> 
				<input class="widefat wp-obs-title-color" id="<?php echo $this->get_field_id( 'title_color' ); ?>" name="<?php echo $this->get_field_name( 'title_color' ); ?>" type="text" value="<?php echo esc_attr( $title_color ); ?>" />
			</p			
			<p>
				<label for="<?php echo $this->get_field_id( 'title_font_size' ); ?>"><?php _e( 'Title font size:' ); ?></label><br> 
				<select class="widefat"  id="<?php echo $this->get_field_id( 'title_font_size' ); ?>" name="<?php echo $this->get_field_name( 'title_font_size' ); ?>">
					<?php 
						for($i = 8; $i <= 42; $i++): 
						if($i == $title_font_size):
					?>
						<option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?>px</option>
					<?php else: ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?>px</option>
					<?php endif;endfor; ?>
				</select> 				
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'link_color' ); ?>"><?php _e( 'Service links color:' ); ?></label><br>
				<input class="widefat wp-obs-link-color" id="<?php echo $this->get_field_id( 'link_color' ); ?>" name="<?php echo $this->get_field_name( 'link_color' ); ?>" type="text" value="<?php echo esc_attr( $link_color ); ?>" />
			</p>						
			<p>
				<label for="<?php echo $this->get_field_id( 'link_font_size' ); ?>"><?php _e( 'Link font size:' ); ?></label><br> 
				<select class="widefat"  id="<?php echo $this->get_field_id( 'link_font_size' ); ?>" name="<?php echo $this->get_field_name( 'link_font_size' ); ?>">
					<?php 
						for($i = 8; $i <= 42; $i++): 
						if($i == $link_font_size):
					?>
						<option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?>px</option>
					<?php else: ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?>px</option>
					<?php endif;endfor; ?>
				</select> 				
			</p>
			<?php	
		}		
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['link'] = ( !empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : '';
		$instance['booking_page'] = ( !empty( $new_instance['booking_page'] ) ) ? strip_tags( esc_url_raw($new_instance['booking_page']) ) : '';
		$instance['all_services'] = ( !empty( $new_instance['all_services'] ) ) ? strip_tags( $new_instance['all_services'] ) : '';
		
		if(file_exists(WP_PLUGIN_DIR."/wp-obs/php/classes.php")){
			include_once(WP_PLUGIN_DIR."/wp-obs/php/classes.php");
			$wpOBSServices = new WPOBSService('', 'public');			
			$wpOBSServices = $wpOBSServices->getServices();
			
			foreach($wpOBSServices as $k=>$v){
				echo '|'.$instance['service_'.$v->getId()].'|';
				$instance['service_'.$v->getId()] = ( !empty( $new_instance['service_'.$v->getId()] ) ) ? strip_tags( $new_instance['service_'.$v->getId()] ) : '';
			}
		}
		
		$instance['book_now'] = ( !empty( $new_instance['book_now'] ) ) ? strip_tags( $new_instance['book_now'] ) : '';
		$instance['book_now_text'] = ( !empty( $new_instance['book_now_text'] ) ) ? strip_tags( esc_html($new_instance['book_now_text']) ) : '';
		
		$instance['title_color'] = ( !empty( $new_instance['title_color'] ) ) ? strip_tags( $new_instance['title_color'] ) : '';
		$instance['title_font_size'] = ( !empty( $new_instance['title_font_size'] ) ) ? strip_tags( $new_instance['title_font_size'] ) : '';
		$instance['link_color'] = ( !empty( $new_instance['link_color'] ) ) ? strip_tags( $new_instance['link_color'] ) : '';
		$instance['link_font_size'] = ( !empty( $new_instance['link_font_size'] ) ) ? strip_tags( $new_instance['link_font_size'] ) : '';
		
		return $instance;
	}

}


?>