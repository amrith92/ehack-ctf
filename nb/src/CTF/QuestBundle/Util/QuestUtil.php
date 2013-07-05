<?php

namespace CTF\QuestBundle\Util;

class QuestUtil {
    
    public static $ATTEMPTING = "ATTEMPTING";
    public static $CORRECT = "CORRECT";
    public static $WRONG = "WRONG";
    
    public static function transformPlaceholders($content, $user) {
        // TODO - Done ///
        // Transform question-content placeholders to actual state-dependent values
        // Valid placeholders are: (currently)
        //  1. username
        //  2. phone
        //////////////////
        if (\preg_match('/\{\{[\s]*[\d\w]+[\s]*\}\}/', $content)) {
            $content = \preg_replace('/\{\{[\s]*username[\s]*\}\}/', $user->getFullName(), $content);
            $content = \preg_replace('/\{\{[\s]*phone[\s]*\}\}/', $user->getPhone(), $content);
        }
        
        return $content;
    }
}
