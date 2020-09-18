<?php /*template name: Login page */?>
<?php 
global $wpdb;ob_start();
$response="";
if(isset($_GET['wpcrl_email_verification_token']))
{
	if (isset($_GET['wpcrl_email_verification_token']) && $_GET['email'] !='') 
	{
		$user = get_user_by('email', $_GET['email']);
		if ($user->ID)
			$stored_token = get_user_meta($user->ID, 'wpcrl_email_verification_token', true);
		if ($stored_token == $_GET['wpcrl_email_verification_token']) {
			// removing token on verification
			 delete_user_meta($user->ID, 'wpcrl_email_verification_token');
			 $response="Your account is activated. Please login.";
			?>
			<!--
			<script>window.location.href = "<? echo get_site_url().'/login'; ?>";</script>
			-->
			<?
		}
		else
		{
			$response="Activation token expired please contact admin.";
		}
	}
}

if(isset($_POST['login']))
{
	$credentials = array();
	$credentials['user_login'] = trim($_POST['user_email']);
	$credentials['user_password'] = trim($_POST['password']);
	$credentials2['user_email'] = trim($_POST['user_email']);
	$credentials2['user_password'] = trim($_POST['password']);
	$user_name = trim($_POST['user_email']);
	$user_password = trim($_POST['password']);
	$get_user_id = get_user_by('login',$user_name);
	$user = get_user_by('login', $credentials['user_login']);				
	$user2 = get_user_by('login', $credentials2['user_email']);				
	
	if (!isset($user) && !isset($user2)) 
	{
		$response="This username or email address does not exist in our records.";
	} 
	else 
	{
		$stored_token = get_user_meta($user->ID, 'wpcrl_email_verification_token', true);
		if ($stored_token == '') 
		{					
			$credentials2['user_email'] = trim($_POST['user_email']);
			$credentials2['user_password'] = trim($_POST['password']);							
			$valid = false;	
				if(is_wp_error($user)){                   
					$response = "Username or Email & Password Incorrect";
					$valid = false;	        
					if(is_wp_error($user2)){
						$response = "Username or Email & Password Incorrect";
							$valid = false;	
					}
						else
						{
							$response = "";
							$user = wp_signon($credentials2, false);								   
							$valid = true;
						}
				}
				else
				{
					$user = wp_signon($credentials, false);
					$valid = true;
				}
				if($valid)
				{
					//wp_set_auth_cookie($user->data->ID);
					wp_set_current_user($user->data->ID, $user->data->user_login);
					do_action('set_current_user');
					function getBrowser() 
				{ 
					$u_agent = $_SERVER['HTTP_USER_AGENT']; 
					$bname = 'Unknown';
					$platform = 'Unknown';
					$version= "";

					//First get the platform?
					if (preg_match('/linux/i', $u_agent)) {
						$platform = 'linux';
					}
					elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
						$platform = 'mac';
					}
					elseif (preg_match('/windows|win32/i', $u_agent)) {
						$platform = 'windows';
					}

					// Next get the name of the useragent yes seperately and for good reason
					if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
					{ 
						$bname = 'Internet Explorer'; 
						$ub = "MSIE"; 
					} 
					elseif(preg_match('/Firefox/i',$u_agent)) 
					{ 
						$bname = 'Mozilla Firefox'; 
						$ub = "Firefox"; 
					}
					elseif(preg_match('/OPR/i',$u_agent)) 
					{ 
						$bname = 'Opera'; 
						$ub = "Opera"; 
					} 
					elseif(preg_match('/Chrome/i',$u_agent)) 
					{ 
						$bname = 'Google Chrome'; 
						$ub = "Chrome"; 
					} 
					elseif(preg_match('/Safari/i',$u_agent)) 
					{ 
						$bname = 'Apple Safari'; 
						$ub = "Safari"; 
					} 
					elseif(preg_match('/Netscape/i',$u_agent)) 
					{ 
						$bname = 'Netscape'; 
						$ub = "Netscape"; 
					} 

					// finally get the correct version number
					$known = array('Version', $ub, 'other');
					$pattern = '#(?<browser>' . join('|', $known) .
					')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
					if (!preg_match_all($pattern, $u_agent, $matches)) {
						// we have no matching number just continue
					}

					// see how many we have
					$i = count($matches['browser']);
					if ($i != 1) {
						//we will have two since we are not using 'other' argument yet
						//see if version is before or after the name
						if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
							$version= $matches['version'][0];
						}
						else {
							$version= $matches['version'][1];
						}
					}
					else {
						$version= $matches['version'][0];
					}

					// check if we have a number
					if ($version==null || $version=="") {$version="?";}

					return array(
						'name'      => $bname,
						'platform'  => $platform,
						'ip'=>$_SERVER['REMOTE_ADDR'],
						'login_date'=>date('Y-m-d H:i:s')
						
					);
				} 
				$ua=getBrowser();
					$to=$user->user_email;
					$subject = get_option('blogname')." - successful login";
					$message = '<html>
						<style>
							.header {
								
								margin: auto;
								display: block;
								text-align: center;
								padding: 0px 0px 30px;
								vertical-align: middle;
								margin-bottom: 20px;
							}
							
							.footer {
								
								padding: 20px;
								text-align: center;
								
							}
						</style>
						<table style="width:600px; margin:auto; border: 1px solid #fff; background: -webkit-linear-gradient(top, #000, #106db0);background: -o-linear-gradient(top, #000, #106db0);background: -moz-linear-gradient(top, #000, #106db0);background: linear-gradient(top, #000, #106db0);" >
							<thead>
								<tr>
									<th class="header" style="color: #fff; padding-bottom: 30px;"><img src="https://ramlogics.com/football/wp-content/themes/twentytwenty/assets/img/logo.png" style="margin: 20px auto 0; display:block; max-height: 80px; " /></th>
								</tr>
							</thead>
							<tbody>
								<tr class="email text-center">
									<td style="color: #fff; padding-left: 30px;padding-right: 30px; text-align: center;">
										<h2 style="color: #fff; margin-bottom: 2px;">  Hello, '.$user->user_nicename.' </h2>
										<h3 style="color: #fff; margin-top: 4px;"> Login Activity </h3>
										</td> 
											
								</tr>
								<tr>
									<td style="color: #fff; padding-left: 30px;padding-right: 30px;">Login Devices Details </td>
								</tr>
					
					
									<tr>
									<td style="color: #fff; padding-left: 30px;padding-right: 30px;"><b>Browser Name:'.$ua['name'].'</b>  </td>
								</tr>
					
								<tr>
										<td style="color: #fff; padding-left: 30px;padding-right: 30px;"><b>IP Address: '.$ua['ip'].'</b> </td>
								</tr>
					
					
									<tr>
									<td style="color: #fff; padding-left: 30px;padding-right: 30px;"><b>Operating System: '.$ua['platform'].'</b>
										
									</td>
								</tr>
					
									<tr>
									<td style="color: #fff; padding-left: 30px;padding-right: 30px;">We will Always let You Know There is Any Activity on Your '.get_option('blogname').' Account </td>
								</tr>
								
								
								<tr>
									<td style="color: #fff; padding-left: 30px;padding-right: 30px;">
										
										<br>
									</td>
								</tr>
								<tr>
									<td style="color: #fff; padding-left: 30px;padding-right: 30px; padding-bottom:40px;">'.get_option('blogname').' Team
										<br> This is an automated message please don&rsquo;t reply
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td class="footer" style="text-align: center; color: #fff;  padding: 20px 0px;">
										<p>© Copyright 2020. All right reserved. by '.get_option('blogname').'.</p>
									</td>
								</tr>
							</tfoot>
						</table>
					
						</html>';
						$headers = 'From: Football Prediction League <noreply@footballpredictionleague.co.uk>'. "\r\n".'Content-type: text/html;';
				if(mail($to, $subject, $message, $headers)){
					
					header('Location: https://footballpredictionleague.co.uk/');
					exit();
				}
				else{ echo "Mail not send";}
				}
		} else {
			$response="Account Not Activate";
		}
	}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title>Football Prediction </title>
        <!-- favicon -->
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/fav_icom.png" type="image/x-icon">
         <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/fav_icom.png">
	     <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/fav_icom.png" sizes="32x32" />
	    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/fav_icom.png" sizes="192x192" />
	    <link rel="apple-touch-icon-precomposed" href="<?php echo get_template_directory_uri(); ?>/assets/img/fav_icom.png" />
	    <meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/assets/img/fav_icom.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="/wp-content/themes/go-child/assets/css/util.css">
	<link rel="stylesheet" type="text/css" href="/wp-content/themes/go-child/assets/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	<div class="limiter">
		<div class="container-login100" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/back.jpg');">
			<div class="wrap-login100">
				<form class="login100-form validate-form" action="" method="post">
					<div class="text-center">
						<a href="<?php echo site_url(); ?>">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo.png" alt="logo">
                        </a>
					</div>

					<span class="login100-form-title p-b-34 p-t-27">
						Log in
						 <p style="color:#fff; text-transform: full-size-kana; font-size: 17px;"><? if($response != '') echo $response; ?></p>
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Enter username">
						<input class="input100" type="text" name="user_email" placeholder="Email Address" required>
						
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<input class="input100" type="password" name="password" placeholder="Password" required>
						
					</div>

					<p class="text-right mt-2"> <a href="<?php echo site_url(); ?>/forget/" style="color:#fff;"> Forgot Your Password?</a></p>


					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit" name="login">
							Login
						</button>
					</div>

					<div class="text-center mt-5">
						<a class="txt1" href="#">
							<p class="mt-3 text-center">No account? <a href="<?php echo site_url(); ?>/register/" style="color:#fff;"> Sign Up Here </a></p>
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>


</body>
</html>