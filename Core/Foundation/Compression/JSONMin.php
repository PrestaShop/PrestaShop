<?php
/**
 * php-json-minify
 * @package JSONMin
 * @version 0.2.6
 * @link https://github.com/T1st3/php-json-minify
 * @author T1st3 <https://github.com/T1st3>
 * @license https://github.com/T1st3/php-json-minify/blob/master/LICENSE MIT
 * @copyright Copyright (c) 2014, T1st3
 *
 *
 * Based on JSON.minify (https://github.com/getify/JSON.minify) by Kyle Simspon (https://github.com/getify)
 * JSON.minify is released under MIT license.
 *
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Compression;

/**
 * The JSONMin class
 * @author T1st3 <https://github.com/T1st3>
 * @since 0.1.0
 */
class JSONMin
{
    /**
     * The original JSON string
     * @var string $original_json The original JSON string
     * @since 0.1.0
     */
    protected $original_json = '';

    /**
     * The minified JSON string
     * @var string $minified_json The minified JSON string
     * @since 0.1.0
     */
    protected $minified_json = '';

    /**
     * Constructor
     * @name __construct
     * @param string $json Some JSON to minify
     * @since 0.1.0
     */
    public function __construct($json)
    {
        $this->original_json = $json;
        return $this;
    }

    /**
     * Get the minified JSON
     * @name getMin
     * @since 0.1.0
     * @return string Minified JSON string
     */
    public function getMin()
    {
        $this->minified_json = $this::minify($this->original_json);
        return $this->minified_json;
    }

    /**
     * Print the minified JSON
     * @name printMin
     * @since 0.1.0
     * @return object the JSONMin object
     */
    public function printMin()
    {
        echo $this->getMin();
        return $this;
    }

    /**
     * Static minify function
     * @name minify
     * @param string $json Some JSON to minify
     * @since 0.1.0
     * @return string Minified JSON string
     * @static
     */
    public static function minify($json)
    {
        $tokenizer = "/\"|(\/\*)|(\*\/)|(\/\/)|\n|\r/";
        $in_string = false;
        $in_multiline_comment = false;
        $in_singleline_comment = false;
        $tmp = null;
        $tmp2 = null;
        $new_str = array();
        $ns = 0;
        $from = 0;
        $lc = null;
        $rc = null;
        $lastIndex = 0;
        while (preg_match($tokenizer, $json, $tmp, PREG_OFFSET_CAPTURE, $lastIndex)) {
            $tmp = $tmp[0];
            $lastIndex = $tmp[1] + strlen($tmp[0]);
            $lc = substr($json, 0, $lastIndex - strlen($tmp[0]));
            $rc = substr($json, $lastIndex);
            if (!$in_multiline_comment && !$in_singleline_comment) {
                $tmp2 = substr($lc, $from);
                if (!$in_string) {
                    $tmp2 = preg_replace("/(\n|\r|\s)*/", "", $tmp2);
                }
                $new_str[] = $tmp2;
            }
            $from = $lastIndex;
            if ($tmp[0] == "\"" && !$in_multiline_comment && !$in_singleline_comment) {
                preg_match("/(\\\\)*$/", $lc, $tmp2);
                // start of string with ", or unescaped " character found to end string
                if (!$in_string || !$tmp2 || (strlen($tmp2[0]) % 2) == 0) {
                    $in_string = !$in_string;
                }
                $from--; // include " character in next catch
                $rc = substr($json, $from);
            } elseif ($tmp[0] == "/*" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
                $in_multiline_comment = true;
            } elseif ($tmp[0] == "*/" && !$in_string && $in_multiline_comment && !$in_singleline_comment) {
                $in_multiline_comment = false;
            } elseif ($tmp[0] == "//" && !$in_string && !$in_multiline_comment && !$in_singleline_comment) {
                $in_singleline_comment = true;
            } elseif (($tmp[0] == "\n" || $tmp[0] == "\r") && !$in_string && !$in_multiline_comment
                && $in_singleline_comment) {
                $in_singleline_comment = false;
            } elseif (!$in_multiline_comment && !$in_singleline_comment && !(preg_match("/\n|\r|\s/", $tmp[0]))) {
                $new_str[] = $tmp[0];
            }
        }
        $new_str[] = $rc;
        return implode("", $new_str);
    }
}
