<?php

namespace CTF\UserBundle\Util;

use CTF\UserBundle\Entity\User;
use Buzz\Browser;
use Buzz\Client\Curl;

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
            $user->getGender() !== null && $user->getLocation() !== null &&
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
        
        $response = $browser->get($request, $headers);
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
}
