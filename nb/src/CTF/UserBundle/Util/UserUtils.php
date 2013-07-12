<?php

namespace CTF\UserBundle\Util;

use CTF\UserBundle\Entity\User;
use Buzz\Browser;
use Buzz\Client\Curl;
use Symfony\Component\Filesystem\Filesystem;

class UserUtils {
    /**
     * Checks whether the provided user has fully registered
     * 
     * @param \CTF\UserBundle\Entity\User $user
     * @return boolean
     */
    public static function hasFullyRegistered(User $user) {
        if ($user->getCity() !== null && $user->getDob() !== null &&
            $user->getFname() !== null && $user->getLname() !== null &&
            $user->getGender() !== null &&
            $user->getEmail() !== null  && $user->getOrg() !== null &&
            $user->getPassword() !== null && $user->getPhone() !== null &&
            $user->getState() !== null) {
            return true;
        }
        return false;
    }
    
    /**
     * Pre-populates the given user with required info, scraped from
     * their Google+ profile
     * 
     * @param \CTF\UserBundle\Entity\User $user
     * @return boolean
     */
    public function populateWithGooglePlus(User $user) {
        $browser = new Browser(new Curl());
        $request = 'https://www.googleapis.com/plus/v1/people/' . $user->getGoogleId();
        $headers = array(
            'Authorization' => 'OAuth ' . $user->getGoogleAccessToken()
        );
        
        try {
            $response = $browser->get($request, $headers);
        } catch (\Exception $e) {
            return false;
        }
        $modified = false;
        
        if (null !== $response) {
            $response = \json_decode($response->getContent());
            
            if (property_exists($response, 'birthday') && !$user->getDob()) {
                $user->setDob(DateTime::createFromFormat('Y-m-d', $response->{'birthday'}));
                $modified = true;
            }
            
            if (property_exists($response, 'gender') && !$user->getGender()) {
                $user->setGender(ucfirst($response->{'gender'}));
                $modified = true;
            }
            
            if (property_exists($response, 'aboutMe') || property_exists($response, 'tagline') && !$user->getAboutMe()) {
                if (property_exists($response, 'aboutMe'))
                    $user->setAboutMe($response->{'aboutMe'});
                else if (property_exists($response, 'tagline'))
                    $user->setAboutMe($response->{'tagline'});
                $modified = true;
            }
            
            if (property_exists($response, 'image') && !$user->getImageURL()) {
                $user->setImageURL(preg_replace('/\?sz=([0-9]+)/', '?sz=160', $response->image->url));
                $user->setThumbnail(preg_replace('/\?sz=([0-9]+)/', '?sz=80', $response->image->url));
            }
            
            if (property_exists($response, 'urls') && !$user->getWebsite()) {
                $arr = array();
                foreach ($response->{'urls'} as $url) {
                    if (property_exists($url, 'primary')) {
                        if (true == $url->primary) {
                            $arr[] = $url->value;
                        }
                    } else {
                        $arr[] = $url->value;
                    }
                }
                $user->setWebsite(implode("\n", $arr));
                $modified = true;
            }
            
            if (property_exists($response, 'currentLocation') && !$user->getCity()) {
                $user->setCity($response->{'currentLocation'});
                $modified = true;
            }
        }
        
        return $modified;
    }
    
    /**
     * Pre-populates the given user with required info, scraped
     * from their Facebook profile
     * 
     * @param \CTF\UserBundle\Entity\User $user
     * @return boolean
     */
    public function populateWithFacebook(User $user) {
        $browser = new Browser(new Curl());
        $request = 'https://graph.facebook.com/' . $user->getFacebookId() .
                '?access_token=' . $user->getFacebookAccessToken() .
                '&fields=bio,birthday,email,gender,first_name,last_name,location,website,picture.height(160).width(160)';
        
        try {
            $response = $browser->get($request);
        } catch (\Exception $e) {
            return false;
        }
        $modified = false;
        
        if (null !== $response) {
            $response = \json_decode($response->getContent());
            
            if (property_exists($response, 'birthday') && !$user->getDob()) {
                $user->setDob(\DateTime::createFromFormat('m/d/Y', $response->birthday));
                
                $modified = true;
            }
            
            if (property_exists($response, 'email') && !$user->getEmail()) {
                $user->setEmail($response->email);
                
                $modified = true;
            }
            
            if (property_exists($response, 'gender') && !$user->getGender()) {
                $user->setGender(ucfirst($response->gender));
                
                $modified = true;
            }
            
            if (property_exists($response, 'first_name') && !$user->getFname()) {
                $user->setFname($response->first_name);
                
                $modified == true;
            }
            
            if (property_exists($response, 'last_name') && !$user->getLname()) {
                $user->setLname($response->last_name);
                
                $modified == true;
            }
            
            if (property_exists($response, 'location') && !$user->getCity()) {
                $user->setCity(preg_split("/\s+(?=\S*+$)/", $response->location->name)[0]);
                
                $modified = true;
            }
            
            if (property_exists($response, 'website') && !$user->getWebsite()) {
                $user->setWebsite($response->website);
                
                $modified = true;
            }
            
            if (property_exists($response, 'bio') && !$user->getAboutMe()) {
                $user->setAboutMe($response->bio);
                
                $modified = true;
            }
            
            if (property_exists($response, 'picture') && !$user->getImageURL()) {
                $user->setImageUrl($response->picture->data->url);
                $user->setThumbnail($response->picture->data->url);
                /*$thumbbrowser = new Browser(new Curl());
                $imgdata = $thumbbrowser->get($response->picture->data->url);
                $dir = __DIR__ . '/../../../../web/uploads/thumb';
                
                if(!\file_exists($dir)) {
                    $fs = new Filesystem();

                    try {
                        $fs->mkdir($dir);
                    } catch (Exception $e) {
                    }
                }
                
                $img = \imagecreatefromstring($imgdata);
                $thumb = \imagecreatetruecolor(80, 80);
                $imsize = \getimagesize($img);
                
                \imagecopyresampled($thumb, $img, 0, 0, 0, 0, 80, 80, $imsize[0], $imsize[1]);
                
                \ob_start();
                \imagejpeg($thumb);
                $image = \ob_get_contents();
                \ob_end_clean();
                
                $fp = \fopen($dir . '/' . md5($user->getId() . $user->getEmailCanonical()) . '.jpg', 'w');
                \fwrite($fp, $image);
                \fclose($fp);
                */
                
                $modified = true;
            }
        }
        
        return $modified;
    }
    
    /**
     * Pre-populates the given user with required info, scraped
     * from their Twitter profile
     * 
     * @param \CTF\UserBundle\Entity\User $user
     * @return boolean
     */
    public function populateWithTwitter(User $user) {
        $browser = new Browser(new Curl());
        $request = 'https://api.twitter.com/1.1/users/show.json?user_id=' . $user->getTwitterId();
        $headers = array(
            'Authorization' => 'OAuth ' . $user->getTwitterAccessToken()
        );
        
        try {
            $response = $browser->get($request, $headers);
        } catch (\Exception $e) {
            return false;
        }
        $modified = false;
        if (null !== $response) {
            $response = \json_decode($response->getContent());
            
            if (property_exists($response, 'name') && !$user->getFname() && !$user->getLname()) {
                list($fname, $lname) = preg_split("/\s+(?=\S*+$)/", $response->name);
                
                $user->setFname($fname);
                $user->setLname($lname);
                $modified = true;
            }
            
            if (property_exists($response, 'profile_image_url') && !$user->getImageURL()) {
                $user->setImageUrl(str_replace('_normal', '_bigger', $response->profile_image_url));
                $user->setThumbnail(str_replace('_normal', '_bigger', $response->profile_image_url));
                
                $modified = true;
            }
            
            if (property_exists($response, 'description') && !$user->getAboutMe()) {
                $user->setAboutMe($response->description);
                $modified = true;
            }
            
            if (property_exists($response, 'screen_name') && !$user->getUsername()) {
                $user->setUsername($response->screen_name);
                if (!$user->getEmail()) {
                    $user->setEmail($response->screen_name . '@twitter.com');
                }
                $modified = true;
            }
            
            if (property_exists($response, 'entities')) {
                if (property_exists($response->entities, 'url')) {
                    $urls = array();
                    foreach ($response->entities->url->urls as $u) {
                        $urls[] = $u->expanded_url;
                    }
                    $user->setWebsite(implode("\n", $urls));
                    $modified = true;
                }
            }
        }
        
        return $modified;
    }
}
