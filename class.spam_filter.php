<?php
if (!class_exists('Spam_Filter'))
{
	class Spam_Filter
	{
		const nonce_action = 'form_spam_filter';
		const nonce_name = 'nonce_check';
		const nonce_start_time = 'form_filter_st';
		const nonce_keyboard_press = 'form_filter_kp';

		var $nonce_fields;

		function Spam_Filter()
		{
			global $wp, $wpdb;

			add_shortcode('spam_filters_form', array(&$this, 'test_form'));

			add_shortcode('spam_filters', array(&$this, 'spam_filters_func'));
			add_filter('validate_spam_filter_fields', array(&$this, 'check_spam_filter_fields'), 1);
		}

		function check_spam_filter_fields($validate = TRUE)
		{
			global $wp, $wpdb;
			$validateNonce = $validateReferer = $in_time = $is_user_keyboard = FALSE;
			if(isset($_REQUEST) && isset($_REQUEST[self::nonce_name]))
			{
				$nonce = $this->verifyNonceField();
				if($nonce!==FALSE) {
					$validateNonce = TRUE;
				}
				$referer = $this->validateReferer();
				if($referer['pass']==TRUE && $referer['hasReferrer']==TRUE) {
					$validateReferer = TRUE;
				}
				//Check Form Submission Time.
				$in_time = $this->validateTimedFormSubmission();
				//Check Keyboard Use
				$is_user_keyboard = $this->validateUsedKeyboard();

			}
			if($validateNonce && $validateReferer && $in_time && $is_user_keyboard)
			{
				$validate = TRUE;
			} else {
				$validate = FALSE;
			}

			return $validate;
		}

		function spam_filters_func($atts, $content = "")
		{
			$defaults = array(
				'var' => '',
			);
			//Extract Shortcode Attributes
			$opts = shortcode_atts( $defaults, $atts, 'spam_filters' );
			extract( $opts );

			$content .= $this->add_form_fields();
			$content .= '<script type="text/javascript">';
			$content .= 'var keysPressed=0;window.onload=function(){var getInputElementsByClassName=function(c){var r=new Array();var j=0;var o=document.getElementsByTagName("input");for(i=0;i<o.length;i++){if(o[i].className==c){r[j]=o[i];j++;}}
	return r;}
	var startTimeFields=getInputElementsByClassName("stime");for(var i=0;i<startTimeFields.length;i++){startTimeFields[i].setAttribute("name","'.self::nonce_start_time.'");}
	var elKeyPressed=getInputElementsByClassName("kpress");document.onkeydown=function(event){keysPressed++;for(var i=0;i<elKeyPressed.length;i++){elKeyPressed[i].setAttribute("name","'.self::nonce_keyboard_press.'");elKeyPressed[i].value=keysPressed;}};}';
			$content .= '</script>';

			return do_shortcode($content);
		}

		function add_form_fields()
		{
			$this->nonce_fields = '<input type="hidden" name="" class="kpress" value="" />';
			$this->nonce_fields .= '<input type="hidden" name="" class="stime" value="'. (time()+14921) .'" />';
			if( function_exists('wp_nonce_field') )
			{
				$this->nonce_fields .= wp_nonce_field( self::nonce_action, self::nonce_name, FALSE, FALSE );
			}
			return $this->nonce_fields;
		}

		function validateTimedFormSubmission($formContents=array())
		{
			$in_time = FALSE;
			if(empty($formContents[self::nonce_start_time])) {
				$formContents[self::nonce_start_time] = $_REQUEST[self::nonce_start_time];
			}
			if(isset($formContents[self::nonce_start_time]))
			{
				$displayTime = $formContents[self::nonce_start_time] - 14921;
				$submitTime = time();
				$fillOutTime = $submitTime - $displayTime;
				//Less than 5 seconds
				if ($fillOutTime < 5) {
					$in_time = FALSE;
				} else {
					$in_time = TRUE;
				}
			}
			return $in_time;
		}

		function validateUsedKeyboard($formContents=array())
		{
			$is_user_keyboard = FALSE;
			if(empty($formContents[self::nonce_keyboard_press]))
			{
				$formContents[self::nonce_keyboard_press] = $_REQUEST[self::nonce_keyboard_press];
			}
			if(isset($formContents[self::nonce_keyboard_press]))
			{
				if (is_numeric($formContents[self::nonce_keyboard_press]) !== false)
				{
					$is_user_keyboard = TRUE;
				}
			}
			return $is_user_keyboard;
		}

		function verifyNonceField($nonce_value='')
		{
			$return = '';
			if(empty($nonce_value))
			{
				$nonce_value = $_REQUEST[self::nonce_name];
			}
			if( function_exists('wp_verify_nonce') )
			{
				$nonce = wp_verify_nonce($nonce_value, self::nonce_action);
				switch ($nonce)
				{
					case 1:
						$return = __('Nonce is less than 12 hours old');
						break;

					case 2:
						$return = __('Nonce is between 12 and 24 hours old');
						break;

					default:
						$return = FALSE;
				}
			}
			return $return;
		}

		function validateReferer()
		{
			if (isset($_SERVER['HTTPS'])) {
				$protocol = "https://";
			} else {
				$protocol = "http://";
			}
			$absurl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
			$absurlParsed = parse_url($absurl);
			$result["pass"] = false;
			$result["hasReferrer"] = false;
			$httpReferer = $_SERVER['HTTP_REFERER'];
			if (isset($httpReferer))
			{
				$refererParsed = parse_url($httpReferer);
				if (isset($refererParsed['host']))
				{
					$result["hasReferrer"] = true;
					$absUrlRegex = '/' . strtolower($absurlParsed['host']) . '/';
					$isRefererValid = preg_match($absUrlRegex, strtolower($refererParsed['host']));
					if ($isRefererValid == 1)
					{
						$result["pass"] = true;
					}
				} else {
					$result["status"] = "Absolute URL: " . $absurl . " Referer: " . $httpReferer;
				}
			} else {
				$result["status"] = "Absolute URL: " . $absurl . " Referer: " . $httpReferer;
			}
			return $result;
		}

		function test_form()
		{
			global $wpdb;
			if( isset($_POST) && !empty($_POST) )
			{
				$validate = apply_filters('validate_spam_filter_fields', TRUE);
				if($validate)
				{
					$data = maybe_serialize($_POST);
				} else {
					$data = 'Spam Submit';
				}
				var_dump($data);
			}
			?>
			<form action="" method="POST">
				<table>
					<tr>
						<td>Name</td>
						<td><input type="text" name="test_name" value=""></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><input type="email" name="test_email" value=""></td>
					</tr>
					<tr>
						<td>Gender</td>
						<td>
							<input type="radio" class="iradio" name="test_gender" value="male"> Male<br/>
							<input type="radio" class="iradio" name="test_gender" value="female"> Female
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" value="Submit">
						</td>
					</tr>
				</table>
				<?php echo do_shortcode('[spam_filters]');?>
			</form>
			<?php
		}
	}
}
global $Spam_Filter;
$Spam_Filter = new Spam_Filter();
