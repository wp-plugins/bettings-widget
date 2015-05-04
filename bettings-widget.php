<?php
/**
 * Plugin Name: Bettin.gs widget
 * Plugin URI: http://bettin.gs/about
 * Description: Show your stats from Bettin.gs
 * Version: 1.0
 * Author: Samuel Ericson
 * Author URI: http://samuelericson.com
 *
 * Based on Justin Tadlock's widget tutorial
 * (http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28)
 * 
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'bettings_load_widgets' );

/**
 * Register our widget.
 * 'Bettings_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function bettings_load_widgets() {
	register_widget( 'Bettings_Widget' );
}

/**
 * Bettings Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.
 *
 * @since 0.1
 */
class Bettings_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Bettings_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bettings', 'description' => __('Shows your stats from your Bettin.gs account.', 'bettings') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'bettings-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'bettings-widget', __('Bettin.gs Widget', 'bettings'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$alias = $instance['alias'];
		$show_link = isset( $instance['show_link'] ) ? $instance['show_link'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */
		if ( $alias ) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://bettin.gs/api/alltimestats/'.$alias);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$body = curl_exec($ch);
			$stats = json_decode($body, true);
			
			echo __('<table width="100%">', 'bettings');
			echo __('<tbody>', 'bettings_widget_domain');
			echo __('<tr><td width="40%">ROI</td><td>Profit</td></tr>', 'bettings');
			echo __('<tr><td style="padding-bottom: 0.5em"><strong>'.($stats[roi]).'</strong></td><td  style="padding-bottom: 0.5em"><strong>'.$stats[profit].'</strong></td></tr>', 'bettings');
			echo __('<tr><td>Placed bets</td><td>Won bets</td></tr>', 'bettings');
			echo __('<tr><td  style="padding-bottom: 0.5em"><strong>'.$stats[numberOfBets].'</strong></td><td  style="padding-bottom: 0.5em"><strong>'.$stats[numberOfWonBets].' ('.$stats[wonPercent].'%)</strong></td></tr>', 'bettings');
			echo __('<tr><td width="40%">Top sport</td><td>Top bookie</td></tr>', 'bettings');
			echo __('<tr><td><strong>'.($stats[popularSport]).'</strong></td><td><strong>'.$stats[popularBookie].'</strong></td></tr>', 'bettings');
			echo __('</tbody>', 'bettings');
			echo __('</table>', 'bettings');
		}

		/* If show link was selected, display the link to Bettin.gs. */
		if ( $show_link )
			printf( '<p>Full stats &rarr; <a href="http://bettin.gs/'.$alias.'">Bettin.gs/'.$alias.'</a></p>', 'bettings' );

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['alias'] = strip_tags( $new_instance['alias'] );

		/* No need to strip tags for show_link. */
		$instance['show_link'] = $new_instance['show_link'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('My Bettin.gs stats', 'bettings'), 'alias' => __('', 'bettings'), 'show_link' => 'on' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Your Alias: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'alias' ); ?>"><?php _e('Your Bettin.gs alias:', 'bettings'); ?></label>
			<input id="<?php echo $this->get_field_id( 'alias' ); ?>" name="<?php echo $this->get_field_name( 'alias' ); ?>" value="<?php echo $instance['alias']; ?>" style="width:100%;" />
		</p>

		<!-- Show Link? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_link'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_link' ); ?>" name="<?php echo $this->get_field_name( 'show_link' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_link' ); ?>"><?php _e('Show link to Bettin.gs', 'bettings'); ?></label>
		</p>

	<?php
	}
}

?>