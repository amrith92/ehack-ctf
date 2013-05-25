<?php

require 'src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '447681865324434',
  'secret' => '60becc1be2a92474fbea8f23a45a64cf',
));

$user = $facebook->getUser();

if ($user) {
  try {
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

if (!$user) {
  $loginUrl = $facebook->getLoginUrl(array(
 'scope' => 'email,user_birthday'
));
}

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>1st Process</title>
  </head>
  <body>
    

    <?php if (!$user): ?>
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
    <?php endif ?>

<?php if ($user): ?>
    <h1> 2nd process </h1>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

      <!-- <pre><?php print_r($user_profile); ?></pre> -->
<?php endif ?>

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
      <input type="submit" />
    </form>


    

<!-- 
<?php if ($user): ?>
      <h3>PHP Session</h3>
     <pre><?php print_r($_SESSION); ?></pre> 
    <?php endif ?> -->



    
  </body>
</html>
