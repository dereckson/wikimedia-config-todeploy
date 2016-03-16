<?php

namespace Wikimedia\Deployments\ToDeploy;

class TextUtils {
    /**
     * Finds a portion of text included between $before and $after strings
     *
     * @param string $text   The text where to find the substring
     * @param string $before The string at the left  of the text to be grabbed
     * @param string $after  The string at the right of the text to be grabbed [facultative]
     *
     * @return string The text found between $before and $after
     */
    static function grab ($text, $before, $after = null) {
        $pos1 = strpos($text, $before);
        if ($pos1 === false) {
            return false;
        } else {
            $pos1 += strlen($before);
        }

        if ($after === null) {
            return substr($text, $pos1);
        }

        $pos2 = strpos($text, $after, $pos1 + 1);
        if ($pos2 === false) {
            return false;
        }

        return substr($text, $pos1, $pos2 - $pos1);
    }
}
