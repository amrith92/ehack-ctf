<?php

namespace CTF\UserBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CTF\CommonBundle\DBAL\Point;

class TextToPointTransformer implements DataTransformerInterface
{
    /**
     * 
     * @param Point|null $value
     * @return string
     */
    public function transform($value) {
        if (null == $value) {
            return "";
        }
        
        return $value->getLatitude() . ',' . $value->getLongitude();
    }

    /**
     * 
     * @param string $value
     * @return Point
     */
    public function reverseTransform($value) {
        if (!$value) {
            return null;
        }
        
        list($lat, $lng) = explode(',', $value);
        $point = new Point($lat, $lng);
        
        return $point;
    }
}
