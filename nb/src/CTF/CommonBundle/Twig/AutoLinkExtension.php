<?php

namespace CTF\CommonBundle\Twig;

class AutoLinkExtension extends \Twig_Extension {
    
    public function getFilters() {
        return array(
            'autolink' => new \Twig_Filter_Method($this, 'autolink', array('is_safe' => array('html'))),
        );
    }
    
    public function getName() {
        return "autolink_twig_extension";
    }
    
    static public function autolink($string) {
        $regexp = "/(<a.*?>)?(http|https)(:\/\/)?(\w+\.)?(\w+)\.(\w+[\/\d\w\-\_\#\@\%\&\!]*)(<\/a.*?>)?/i";
        $anchorMarkup = '<a href="%s" target="_blank" >%s</a>';

        preg_match_all($regexp, $string, $matches, \PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (empty($match[1]) && empty($match[7])) {
                $replace = sprintf($anchorMarkup, $match[0], $match[0]);
                $string = str_replace($match[0], $replace, $string);
            }
        }

        return $string;
    }
}
