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
            /*case 'facebook':
                if (!$user = $this->userRepository->findOneByFacebookId($attr['id'])) {
                    if (($user = $this->userRepository->findOneByEmail($attr['email'])) && $attr['verified']) {
                        $user->setFacebookId($attr['id']);
                        if (!$user->getFirstname()) {
                            $user->setFirstname($attr['first_name']);
                        }
                        if (!$user->getLastname()) {
                            $user->setLastname($attr['last_name']);
                        }
                        $user->setFacebookUsername($attr['username']);
                    } else {
                        $user = new User();
                        $user->setUsername($this->userRepository->createUsernameByEmail($attr['email']));
                        $user->setEmail($attr['email']);
                        $user->setFirstname($attr['first_name']);
                        $user->setLastname($attr['last_name']);
                        $user->setPassword('');
                        $user->setEnabled(true);
                        $user->setFacebookId($attr['id']);
                        $user->setFacebookUsername($attr['username']);
                        $user->addRole('ROLE_USER');
                        $this->entityManager->persist($user);
                    }
                }
                break;*/
        }

        $this->entityManager->flush();

        return $user;
    }

}
