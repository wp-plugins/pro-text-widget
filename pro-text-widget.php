<?php
/*
Plugin Name: Pro Text Widget
Plugin URI: http://wordpress.org/plugins/pro-text-widget/
Description: Pro Text Widget.You have choice to text widget show only specific Post/category/Page.
Version: 1.1
Author: Shambhu Prasad Patnaik
Author URI:http://socialcms.wordpress.com/
*/
class Pro_Text_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_text', 'description' => __('Pro Arbitrary text or HTML.'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('pro_text_widget', __('Pro Text Widget'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
    	$text_show = empty( $instance['text_show'] ) ? 'post' : $instance['text_show'];
    	$text_ids = empty( $instance['text_ids'] ) ? '' : $instance['text_ids'];
		$show_widget =false;
		$t_ids =explode(",",$text_ids);

        switch($text_show)
		{
         case 'post' :
			if(is_single())
			{
			 $post_id =get_the_ID();
			 if($text_ids=='')
          	  $show_widget =true;
			 elseif(in_array($post_id,$t_ids))
			  $show_widget =true;
		    }
		    break;
         case 'page' :
			if(is_page())
			{
			  $page_id =get_the_ID();
              if($text_ids=='')
          	  $show_widget =true;
			 elseif(in_array($page_id,$t_ids))
			  $show_widget =true;
		    }
			break;
         case 'category' :
			if(is_category())
			{
			 $cur_cat_id = get_cat_id( single_cat_title("",false) );			 
   		     if($text_ids=='')
          	  $show_widget =true;
			 elseif(in_array($cur_cat_id,$t_ids))
			  $show_widget =true;
		    }
			break;
		}
		if($show_widget):
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $after_widget;
		endif;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text_show'] = strip_tags($new_instance['text_show']);
		$instance['text_ids'] = strip_tags($new_instance['text_ids']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text_show = (isset($instance['text_show'])?strip_tags($instance['text_show']):'post');
		$text_ids = strip_tags($instance['text_ids']);
		$text = esc_textarea($instance['text']);

?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p>Display Only In  :
		 <input class="widefat" id="<?php echo $this->get_field_id('text_post'); ?>" name="<?php echo $this->get_field_name('text_show'); ?>" type="radio" value="post" <?php checked($text_show,'post' ); ?> /><label for="<?php echo $this->get_field_id('text_post'); ?>"><?php _e( 'Post' ); ?></label>
		 <input class="widefat" id="<?php echo $this->get_field_id('text_page'); ?>" name="<?php echo $this->get_field_name('text_show'); ?>" type="radio" value="page" <?php checked( $text_show ,'page'); ?> /><label for="<?php echo $this->get_field_id('text_page'); ?>"><?php _e( 'Page' ); ?></label>
		 <input class="widefat" id="<?php echo $this->get_field_id('text_category'); ?>" name="<?php echo $this->get_field_name('text_show'); ?>" type="radio" value="category" <?php checked( $text_show ,'category'); ?> /><label for="<?php echo $this->get_field_id('text_category'); ?>"><?php _e( 'Category'); ?></label>

		<p><label for="<?php echo $this->get_field_id('text_ids'); ?>"><?php _e( 'Display IDs :' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('text_ids'); ?>" name="<?php echo $this->get_field_name('text_ids'); ?>" type="text" value="<?php echo $text_ids; ?>" />
		
		<br>Enter a comma seperated ID.<br>ex : <code>2,3</code> &nbsp;&nbsp;(This widget will display  only  your given ids) if blank then show all the pages(radio) in you selected.</p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
<?php
	}
} // class Pro_Text_Widget

// register Pro_Text_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "Pro_Text_Widget" );' ) );
register_deactivation_hook(__FILE__, 'Pro_Text_Widget_deactivate');
if(!function_exists("Pro_Text_Widget_deactivate")):
function Pro_Text_Widget_deactivate()
{
 unregister_widget('Pro_Text_Widget');
}
endif;
?>