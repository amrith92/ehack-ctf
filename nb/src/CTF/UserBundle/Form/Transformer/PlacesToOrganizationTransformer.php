<?php

namespace CTF\UserBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CTF\UserBundle\Entity\Organization;

class PlacesToOrganizationTransformer implements DataTransformerInterface
{
    private $om;
    
    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     */
    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }
    
    /**
     * 
     * @param Organization|null $value
     * @return string
     */
    public function transform($value) {
        if (null == $value) {
            return "";
        }
        
        return $value->getName();
    }

    /**
     * 
     * @param string $value
     * @return Organization
     */
    public function reverseTransform($value) {
        if (!$value) {
            return null;
        }
        
        $organization = $this->om->getRepository('CTFUserBundle:Organization')->findOneBy(array(
            'name' => $value
        ));
        
        if (null === $organization) {
            $organization = new Organization();
            $organization->setName($value);
            $this->om->persist($organization);
            $this->om->flush();
        }
        
        return $organization;
    }
}
