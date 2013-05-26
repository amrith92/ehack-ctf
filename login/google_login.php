<?php
//Google data

$google_client_id 		= '503586554333.apps.googleusercontent.com';
$google_client_secret 	= 'Sic42Tlewekry8a_0CLiQbW2';
$google_redirect_url 	= 'http://localhost/pagoda/ehack/login/google_login.php';
$google_developer_key 	= '503586554333@developer.gserviceaccount.com';


require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';


$gClient = new Google_Client();
$gClient->setApplicationName('ehack');
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);

$google_oauthV2 = new Google_Oauth2Service($gClient);
session_start();

if (isset($_GET['code'])) 
{ 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
	return;
}


if (isset($_SESSION['token'])) 
{ 
		$gClient->setAccessToken($_SESSION['token']);
}


if ($gClient->getAccessToken()) 
{
	  //Get user details if user is logged in
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	  $_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
	//get google login url
	$authUrl = $gClient->createAuthUrl();
}

?>

<html>
  <head>
    <title>Registration</title>
  </head>
  <body>

<?php
if(!isset($authUrl)) {

		echo '<h1> Registration </h1>';

	
	echo '<br /><a href="'.$profile_url.'" target="_blank"><img src="'.$profile_image_url.'?sz=50" /></a>';
	


}
?>


 <form>
      <label> Name :  </label> 
      <input name="Name" type="text" value="<?php echo isset($user['name'])?$user['name']:''; ?>" />
<br>
      <label> gender :  </label> 
      <input name="gender" type="text" value="<?php echo isset($user['gender'])?$user['gender']:''; ?>" />
<br>
      <label> email :  </label> 
      <input name="email" type="text" value="<?php echo isset($user['email'])?$user['email']:''; ?>" />
<br>
      <label> birthday :  </label> 
      <input name="birthday" type="text" value="<?php echo isset($user['birthday'])?$user['birthday']:''; ?>" />
<br>
      <label> bio :  </label> 
      <input name="bio" type="text" value="<?php echo isset($user['bio'])?$user['bio']:''; ?>" />
<br>
      <label> Location :  </label> 
      <input name="Location" type="text" value="<?php echo isset($user['location']['name'])?$user['location']['name']:''; ?>" />
<br>
      <label> Google id :  </label> 
      <input name="gid" type="text" value="<?php echo isset($user['id'])?$user['id']:''; ?>" />
<br>
      <input type="submit" />
    </form>

   </body>
   </html>