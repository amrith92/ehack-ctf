<?php

namespace CTF\ReferralBundle\Util;

use CTF\ReferralBundle\Util\Encryption\AES;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RefCryptor {
    
    /**
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function encrypt($message) {
        $key = $this->container->getParameter('key');
        $iv = $this->container->getParameter('iv');
        $coder = new AES($key, $iv);
        
        $enc = $coder->encrypt($message);
        $enc = \urlencode(\base64_encode($enc));
        return $enc;
    }
    
    public function decrypt($text) {
        $key = $this->container->getParameter('key');
        $iv = $this->container->getParameter('iv');
        $coder = new AES($key, $iv);
        
        $dec = $coder->decrypt(\base64_decode($text));
        return $dec;
    }
}
