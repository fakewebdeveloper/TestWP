<?php
if (!class_exists('FrontPage'))
{
	class FrontPage
	{
		function FrontPage()
		{
			add_action('wp', array(&$this, 'FrontPage_init'), 1);
			add_shortcode('shortcode1', array(&$this, 'shortcode1_func'));
		}
		function FrontPage_init()
		{
			if (isset($_REQUEST['apage']) && $_REQUEST['apage'] == "appointment") {
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
		function FrontPage_title()
		{
			return "FrontPage";
		}
		function FrontPage_content()
		{
			$return = '<div class="FrontPageContainer">';
			if (isset($_REQUEST['action']) && !empty($_REQUEST['action']))
			{
				
			} else {
				$return .= '';
			}
			$return .= '</div>';
			return $return;
		}
		function shortcode1_func($atts, $content = "")
		{
			/* ---------------------/.Begin Set Shortcode Attributes--------------------- */
			$defaults = array(
				'Title' => __('Shortcode1 Form'),
			);
			//Extract Shortcode Attributes
			$opts = shortcode_atts($defaults, $atts, 'shortcode1');
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
