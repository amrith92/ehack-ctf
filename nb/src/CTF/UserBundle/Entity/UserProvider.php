<?php

namespace CTF\UserBundle\Entity;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserManagerInterface;

class UserProvider extends FOSUBUserProvider {
    
    protected $entityManager;
    protected $userRepository;
    
    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param FOS\UserBundle\Model\UserManagerInterface $userManager
     * @param array $properties
     */
    public function __construct(EntityManager $em, UserManagerInterface $userManager, array $properties)
    {
        parent::__construct($userManager, $properties);
        
        $this->entityManager = $em;
        $this->userRepository = $em->getRepository('CTFUserBundle:User');
    }
    
    /**
     * {@inheritDoc}
     */
    public function connect($user, UserResponseInterface $response) {
        $attr = $response->getResponse();
        switch ($response->getResourceOwner()->getName()) {
            case 'google':
                $user = $this->userRepository->findOneByGoogleId($attr['id']);
                if ($user !== null) {
                    $user->setGoogleAccessToken($response->getAccessToken());
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }
            break;
            
            case 'facebook':
                $user = $this->userRepository->findOneByFacebookId($attr['id']);
                if ($user !== null) {
                    $user->setFacebookAccessToken($response->getAccessToken());
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }
            break;
            
            case 'twitter':
                $user = $this->userRepository->findOneByTwitterId($attr['id']);
                if ($user !== null) {
                    $user->setTwitterAccessToken($response->getAccessToken());
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }
            break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
        $attr = $response->getResponse();
        switch ($response->getResourceOwner()->getName()) {
            case 'google':
                if (!$user = $this->userRepository->findOneByGoogleId($attr['id'])) {
                    if (($user = $this->userRepository->findOneByEmail($attr['email'])) && $attr['verified_email']) {
                        $user->setGoogleId($attr['id']);
                        $user->setGoogleAccessToken($response->getAccessToken());
                        if (!$user->getFname()) {
                            $user->setFname($attr['given_name']);
                        }
                        if (!$user->getLname()) {
                            $user->setLname($attr['family_name']);
                        }
                    } else {
                        $user = new User();
                        $user->setUsername($this->userRepository->createUsernameByEmail($attr['email']));
                        $user->setEmail($attr['email']);
                        $user->setFname($attr['given_name']);
                        $user->setLname($attr['family_name']);
                        $user->setPassword('');
                        $user->setEnabled(true);
                        $user->setGoogleId($attr['id']);
                        $user->setGoogleAccessToken($response->getAccessToken());
                        if (isset($attr['gender'])) {
                            $user->setGender(ucfirst($attr['gender']));
                        }
                        $user->setLoginMode('google');
                        if (isset($attr['birthdate'])) {
                            $user->setDob(\DateTime::createFromFormat('Y-m-d', $attr['birthdate']));
                        }
                        if (isset($attr['phone_number'])) {
                            $user->setPhone($attr['phone_number']);
                        }
                        if (isset($attr['website'])) {
                            $user->setWebsite($attr['website']);
                        }
                        if (isset($attr['address'])) {
                            $address = \json_decode($attr['address']);
                            if (null !== $address) {
                                $user->setCity($address->{'locality'});
                                $user->setState($address->{'region'});
                            }
                        }
                        $user->addRole('ROLE_USER');
                        $this->entityManager->persist($user);
                    }
                }
                break;
            case 'facebook':
                if (!$user = $this->userRepository->findOneByFacebookId($attr['id'])) {
                    if (($user = $this->userRepository->findOneByEmail($attr['email'])) && $attr['verified']) {
                        $user->setFacebookId($attr['id']);
                        $user->setFacebookAccessToken($response->getAccessToken());
                        if (!$user->getFname()) {
                            $user->setFname($attr['first_name']);
                        }
                        if (!$user->getLname()) {
                            $user->setLname($attr['last_name']);
                        }
                    } else {
                        $user = new User();
                        $user->setUsername($attr['username']);
                        $user->setEmail($attr['email']);
                        $user->setFname($attr['first_name']);
                        $user->setLname($attr['last_name']);
                        if (isset($attr['picture']) && isset($attr['picture']['url'])) {
                            $user->setImageUrl($attr['picture']['url']);
                        }
                        if (isset($attr['location'])) {
                            $user->setCity(preg_split("/\s+(?=\S*+$)/", $attr['location']['name'])[0]);
                        }
                        $user->setDob(\DateTime::createFromFormat("m/d/Y", $attr['birthday']));
                        if (isset($attr['bio'])) {
                            $user->setWebsite($attr['bio']);
                        }
                        if (isset($attr['quotes'])) {
                            if ($user->getWebsite()) {
                                $user->setWebsite($user->getWebsite() . "\n" . $attr['quotes']);
                            } else {
                                $user->setWebsite($attr['quotes']);
                            }
                        }
                        $user->setGender(ucfirst($attr['gender']));
                        $user->setPassword('');
                        $user->setEnabled(true);
                        $user->setFacebookId($attr['id']);
                        $user->setFacebookAccessToken($response->getAccessToken());
                        $user->setLoginMode('facebook');
                        $user->addRole('ROLE_USER');
                        $this->entityManager->persist($user);
                    }
                }
                break;
            case 'twitter':
                if (!$user = $this->userRepository->findOneByTwitterId($attr['id'])) {
                    if (($user = $this->userRepository->findByUsername($attr['screen_name']))) {
                        $user->setTwitterId($attr['id']);
                        $user->setTwitterAccessToken($response->getAccessToken()['oauth_token']);
                    } else {
                        $user = new User();
                        {
                            list($fname, $lname) = preg_split("/\s+(?=\S*+$)/", $attr['name']);
                            $user->setFname($fname);
                            $user->setLname($lname);
                        }
                        $user->setUsername($attr['screen_name']);
                        $user->setEmail($attr['screen_name'] . '@twitter.com');
                        $user->setTwitterId($attr['id']);
                        $user->setTwitterAccessToken($response->getAccessToken()['oauth_token']);
                        $user->setPassword('');
                        $user->setEnabled(true);
                        $user->setAboutMe($attr['description']);
                        $user->setImageURL(str_replace('_normal', '_bigger', $attr['profile_image_url_https']));
                        $user->setCity(preg_split("/\s+(?=\S*+$)/", $attr['location'])[0]);
                        {
                            $entities = $attr['entities']['url'];
                            $urls = array();
                            foreach ($entities['urls'] as $u) {
                                $urls[] = $u['expanded_url'];
                            }
                            $user->setWebsite(implode("\n", $urls));
                        }
                        $user->setLoginMode('twitter');
                        $user->addRole('ROLE_USER');
                        $this->entityManager->persist($user);
                    }
                }
                break;
        }

        $this->entityManager->flush();

        return $user;
    }

}
