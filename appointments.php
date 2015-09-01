<?php
if (!class_exists('FrontPage'))
{
	class FrontPage
	{
		function FrontPage()
		{
			add_action('wp', array(&$this, 'FrontPage_init'), 1);
			add_shortcode('book_appointment', array(&$this, 'book_appointment_func'));
			add_shortcode('time_slots', array(&$this, 'time_slots_func'));
		}
		function FrontPage_init()
		{
			if (isset($_REQUEST['apage']) && $_REQUEST['apage'] == "appointments") {
				add_filter('the_title', array(&$this, 'FrontPage_title'));
				add_filter('the_content', array(&$this, 'FrontPage_content'));
				add_action('template_redirect', array(&$this, 'FrontPage_template'));
			}
		}
		function FrontPage_template()
		{
			include(TEMPLATEPATH . "/page.php");
			exit;
		}
		function FrontPage_title($title)
		{
			remove_filter('the_title', array(&$this, 'FrontPage_title'));
			return "FrontPage";
		}
		function FrontPage_content()
		{
			remove_filter('the_content', array(&$this, 'FrontPage_content'));
			$return = '<div class="FrontPageContainer">';
			if (isset($_REQUEST['action']) && !empty($_REQUEST['action']))
			{
				
			} else {
				$return .= '';
			}
			$return .= '</div>';
			return $return;
		}
		
		function time_slots_func($atts, $content = "")
		{
			/* ---------------------/.Begin Set Shortcode Attributes--------------------- */
			$defaults = array(
				'Title' => __('Shortcode Form'),
			);
			//Extract Shortcode Attributes
			$opts = shortcode_atts($defaults, $atts, 'time_slots');
			extract($opts);
			/* ---------------------/.End Set Shortcode Attributes--------------------- */
			
			$content .= '<div class="">';
			$content .= '';
			$content .= '</div>';
			
			return do_shortcode($content);
		}
		
		function book_appointment_func($atts, $content = "")
		{
			/* ---------------------/.Begin Set Shortcode Attributes--------------------- */
			$defaults = array(
				'Title' => __('Shortcode1 Form'),
			);
			//Extract Shortcode Attributes
			$opts = shortcode_atts($defaults, $atts, 'book_appointment');
			extract($opts);
			/* ---------------------/.End Set Shortcode Attributes--------------------- */
			
			$content .= '<div class="">';
			$content .= '';
			$content .= '</div>';
			
			return do_shortcode($content);
		}
	}
	
	global $FrontPage;
	$FrontPage = new FrontPage();
}