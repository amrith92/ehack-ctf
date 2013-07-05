<?php

namespace CTF\ReferralBundle\Util\Encryption;

/**
 * \CTF\ReferralBundle\Util\Encryption\PadCrypt
 * 
 * This class can be used to pad strings with the following methods:
 * ANSI X.923, ISO 10126, PKCS7, Zero Padding, and Bit Padding
 * 
 * The methods are implemented as documented at:
 * http://en.wikipedia.org/wiki/Padding_(cryptography)
 *
 * @author Strategy Star Inc., amrith92
 * @website http://www.strategystar.net
 */
class PadCrypt {

    public static function pad_ISO_10126($data, $block_size) {
        $padding = $block_size - (\strlen($data) % $block_size);

        for ($x = 1; $x < $padding; $x++) {
            mt_srand();
            $data .= \chr(mt_rand(0, 255));
        }

        return $data . \chr($padding);
    }

    public static function unpad_ISO_10126($data) {
        $length = \ord(\substr($data, -1));
        return \substr($data, 0, \strlen($data) - $length);
    }

    public static function pad_ANSI_X923($data, $block_size) {
        $padding = $block_size - (\strlen($data) % $block_size);
        return $data . \str_repeat(\chr(0), $padding - 1) . \chr($padding);
    }

    public static function unpad_ANSI_X923($data) {
        $length = \ord(\substr($data, -1));
        $padding_position = \strlen($data) - $length;
        $padding = \substr($data, $padding_position, -1);

        for ($x = 0; $x < $length; $x++) {
            if (\ord(\substr($padding, $x, 1)) != 0) {
                return $data;
            }
        }

        return \substr($data, 0, $padding_position);
    }

    public static function pad_PKCS7($data, $block_size) {
        $padding = $block_size - (\strlen($data) % $block_size);
        $pattern = \chr($padding);
        return $data . \str_repeat($pattern, $padding);
    }

    public static function unpad_PKCS7($data) {
        $pattern = \substr($data, -1);
        $length = \ord($pattern);
        $padding = \str_repeat($pattern, $length);
        $pattern_pos = \strlen($data) - $length;

        if (\substr($data, $pattern_pos) == $padding) {
            return \substr($data, 0, $pattern_pos);
        }

        return $data;
    }

    public static function pad_BIT($data, $block_size) {
        $length = $block_size - (\strlen($data) % $block_size) - 1;
        return $data . "\x80" . \str_repeat("\x00", $length);
    }

    public static function unpad_BIT($data) {
        if (\substr(\rtrim($data, "\x00"), -1) == "\x80") {
            return \substr(\rtrim($data, "\x00"), 0, -1);
        }

        return $data;
    }

    public static function pad_ZERO($data, $block_size) {
        $length = $block_size - (\strlen($data) % $block_size);
        return $data . \str_repeat("\x00", $length);
    }

    public static function unpad_ZERO($data) {
        return \rtrim($data, "\x00");
    }

}