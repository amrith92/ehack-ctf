<?php

namespace CTF\CommonBundle\Twig;

class ListifyExtension extends \Twig_Extension {
    
    public function getFilters() {
        return array(
            'listify' => new \Twig_Filter_Method($this, 'listify', array('is_safe' => array('html'))),
        );
    }
    
    public function getName() {
        return "listify_twig_extension";
    }
    
    static public function listify($string) {
        $arr = \preg_split('/[ ]|[\n\r]{1}|[\n]|[\r]/i', $string);
        
        foreach ($arr as $v) {
            if ('' != $v && $v != "\n" && $v != "\r" && $v != "\r\n" && $v != "\n\r") {
                $data[] = '<li>' . $v . '</li>';
            }
        }
        
        $string = '<ul>' . \implode(' ', $data) . '</ul>';

        return $string;
    }
}
