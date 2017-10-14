<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 14/10/2017
 * Time: 12:33
 */

/**
 * Class CallMeBack_Utils_StringUtils
 */
class CallMeBack_Utils_StringUtils {
    /**
     * Transform underscore string to camel case
     *
     * @param string $string
     *
     * @return mixed|string
     */
    public static function toCamelCase( $string = '' ) {
        // match underscores and the first letter after each of them,
        // replace the matched string with the uppercase version of the letter
        $string = preg_replace_callback(
            '/_([^_])/',
            function ( array $m ) {
                return ucfirst( $m[1] );
            },
            $string
        );

        return $string;
    }
}