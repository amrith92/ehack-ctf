<?php

namespace CTF\UserBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CTF\UserBundle\Entity\Countries;

class CountryToCodeTransformer implements DataTransformerInterface
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
     * @param Country|null $value
     * @return string
     */
    public function transform($value) {
        if (null == $value) {
            return "";
        }
        
        return $value->getIsoCode2();
    }

    /**
     * 
     * @param string $value
     * @return Countries
     * @throws TransformationFailedException
     */
    public function reverseTransform($value) {
        if (!$value) {
            return null;
        }
        
        $country = $this->om->getRepository('CTFUserBundle:Countries')->findOneBy(array(
            'iso_code_2' => $value
        ));
        
        if (null === $country) {
            throw new TransformationFailedException(sprintf(
                'A country with thee code "%s" does not exist!',
                $value
            ));
        }
        
        return $country;
    }
}
