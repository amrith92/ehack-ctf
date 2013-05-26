<?php

// Facebook data

require 'src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '447681865324434',
  'secret' => '60becc1be2a92474fbea8f23a45a64cf',
));

$fuser = $facebook->getUser();

if ($fuser) {
  try {
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $fuser = null;
  }
}

if (!$fuser) {
  $loginUrl = $facebook->getLoginUrl(array(
 'scope' => 'email,user_birthday'
));
}


//Google data

$google_client_id 		= '503586554333.apps.googleusercontent.com';
$google_client_secret 	= 'Sic42Tlewekry8a_0CLiQbW2';
$google_redirect_url 	= 'http://localhost/pagoda/ehack/login/';
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


//Redirect user to google authentication page for code, if code is empty.
//Code is required to aquire Access Token from google
//Once we have access token, assign token to session variable
//and we can redirect user back to page and login.
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

//HTML page start
echo '<html xmlns="http://www.w3.org/1999/xhtml">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<title>Login with Google</title>';
echo '</head>';
echo '<body>';

if(isset($authUrl) && !$fuser) //user is not logged in, show login button
{
	echo '<a class="login" href="'.$authUrl.'"><img src="images/google-login-button.png" /></a>';
	echo '<a href="<?php echo $loginUrl; ?>">Login with Facebook</a>';
} 
else // user logged in 
{
	if ($fuser) {  // User logged in through facebook 

?> 
    <h1> 2nd process </h1>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <!-- <pre><?php print_r($user_profile); ?></pre> -->
<?php } ?>

<?php
if(!isset($authUrl)) {

		echo '<h1> 2nd process </h1>';

	
	echo '<br /><a href="'.$profile_url.'" target="_blank"><img src="'.$profile_image_url.'?sz=50" /></a>';
	


}

}

/* Notes :

$user_profile -> Facebook user data
$user -> Google user data

*/


//Merging both data as $user_profile;

$user_profile = $user;

if(isset($user['id']))
{
$user_profile['gid'] = $user['id'];
$user_profile['id'] = null;
}
?>

  <form>
      <label> Name :  </label> 
      <input name="Name" type="text" value="<?php echo isset($user_profile['name'])?$user_profile['name']:''; ?>" />
<br>
      <label> gender :  </label> 
      <input name="gender" type="text" value="<?php echo isset($user_profile['gender'])?$user_profile['gender']:''; ?>" />
<br>
      <label> email :  </label> 
      <input name="email" type="text" value="<?php echo isset($user_profile['email'])?$user_profile['email']:''; ?>" />
<br>
      <label> birthday :  </label> 
      <input name="birthday" type="text" value="<?php echo isset($user_profile['birthday'])?$user_profile['birthday']:''; ?>" />
<br>
      <label> bio :  </label> 
      <input name="bio" type="text" value="<?php echo isset($user_profile['bio'])?$user_profile['bio']:''; ?>" />
<br>
      <label> Location :  </label> 
      <input name="Location" type="text" value="<?php echo isset($user_profile['location']['name'])?$user_profile['location']['name']:''; ?>" />
<br>
      <label> Facebook id :  </label> 
      <input name="Fid" type="text" value="<?php echo isset($user_profile['id'])?$user_profile['id']:''; ?>" />
<br>
		<label> Google id :  </label> 
      <input name="gid" type="text" value="<?php echo isset($user_profile['gid'])?$user_profile['gid']:''; ?>" />
<br>
      <input type="submit" />
    </form>

</body></html>
