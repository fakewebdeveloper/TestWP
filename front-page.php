<?php
if (!class_exists('FrontPage'))
{
	class FrontPage
	{
		function FrontPage()
		{
			add_action('wp', array('FrontPage', 'FrontPage_init'), 1);
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
	}
}
