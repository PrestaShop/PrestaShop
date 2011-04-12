<?php
/**
 * Class Minify_HTML  
 * @package Minify
 */

/**
 * Compress HTML
 *
 * This is a heavy regex-based removal of whitespace, unnecessary comments and
 * tokens. IE conditional comments are preserved. There are also options to have
 * STYLE and SCRIPT blocks compressed by callback functions.
 *
 * A test suite is available.
 *
 * @package Minify
 * @author Stephen Clay <steve@mrclay.org>
 */
class Minify_HTML {

    /**
     * Defines which class to call as part of callbacks, change this
     * if you extend Minify_HTML
     * @var string
     */
    protected static $className = 'Minify_HTML';

    /**
     * "Minify" an HTML page
     *
     * @param string $html
     *
     * @param array $options
     *
     * 'cssMinifier' : (optional) callback function to process content of STYLE
     * elements.
     *
     * 'jsMinifier' : (optional) callback function to process content of SCRIPT
     * elements. Note: the type attribute is ignored.
     *
     * 'xhtml' : (optional boolean) should content be treated as XHTML1.0? If
     * unset, minify will sniff for an XHTML doctype.
     *
     * @return string
     */
    public static function minify($html, $options = array()) {
       
        if (isset($options['cssMinifier'])) {
            self::$_cssMinifier = $options['cssMinifier'];
        }
        if (isset($options['jsMinifier'])) {
            self::$_jsMinifier = $options['jsMinifier'];
        }
       
        $html = str_replace("\r\n", "\n", trim($html));
       
        self::$_isXhtml = (
            isset($options['xhtml'])
                ? (bool)$options['xhtml']
                : (false !== strpos($html, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML'))
        );
       
        self::$_replacementHash = 'MINIFYHTML' . md5(time());
        self::$_placeholders = array();
       
        // replace SCRIPTs (and minify) with placeholders
        $html = preg_replace_callback(
            '/\\s*(<script\\b[^>]*?>)([\\s\\S]*?)<\\/script>\\s*/i'
            ,array(self::$className, '_removeScriptCB')
            ,$html);
       
        // replace STYLEs (and minify) with placeholders
        $html = preg_replace_callback(
            '/\\s*(<style\\b[^>]*?>)([\\s\\S]*?)<\\/style>\\s*/i'
            ,array(self::$className, '_removeStyleCB')
            ,$html);
       
        // remove HTML comments (not containing IE conditional comments).
        $html = preg_replace_callback(
            '/<!--([\\s\\S]*?)-->/'
            ,array(self::$className, '_commentCB')
            ,$html);
       
        // replace PREs with placeholders
        $html = preg_replace_callback('/\\s*(<pre\\b[^>]*?>[\\s\\S]*?<\\/pre>)\\s*/i'
            ,array(self::$className, '_removePreCB')
            , $html);
       
        // replace TEXTAREAs with placeholders
        $html = preg_replace_callback(
            '/\\s*(<textarea\\b[^>]*?>[\\s\\S]*?<\\/textarea>)\\s*/i'
            ,array(self::$className, '_removeTaCB')
            , $html);
       
        // trim each line.
        // @todo take into account attribute values that span multiple lines.
        $html = preg_replace('/^\\s+|\\s+$/m', '', $html);
       
        // remove ws around block/undisplayed elements
        $html = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
            .'|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
            .'|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
            .'|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
            .'|ul)\\b[^>]*>)/i', '$1', $html);
       
        // remove ws outside of all elements
        $html = preg_replace_callback(
            '/>([^<]+)</'
            ,array(self::$className, '_outsideTagCB')
            ,$html);
       
        // use newlines before 1st attribute in open tags (to limit line lengths)
        //$html = preg_replace('/(<[a-z\\-]+)\\s+([^>]+>)/i', "$1\n$2", $html);
       
        // fill placeholders
        $html = str_replace(
            array_keys(self::$_placeholders)
            ,array_values(self::$_placeholders)
            ,$html
        );
        self::$_placeholders = array();
       
        self::$_cssMinifier = self::$_jsMinifier = null;
        return $html;
    }
   
    protected static function _commentCB($m)
    {
        return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<!['))
            ? $m[0]
            : '';
    }
   
    protected static function _reservePlace($content)
    {
        $placeholder = '%' . self::$_replacementHash . count(self::$_placeholders) . '%';
        self::$_placeholders[$placeholder] = $content;
        return $placeholder;
    }

    protected static $_isXhtml = false;
    protected static $_replacementHash = null;
    protected static $_placeholders = array();
    protected static $_cssMinifier = null;
    protected static $_jsMinifier = null;

    protected static function _outsideTagCB($m)
    {
        return '>' . preg_replace('/^\\s+|\\s+$/', ' ', $m[1]) . '<';
    }
   
    protected static function _removePreCB($m)
    {
        return self::_reservePlace($m[1]);
    }
   
    protected static function _removeTaCB($m)
    {
        return self::_reservePlace($m[1]);
    }

    protected static function _removeStyleCB($m)
    {
        $openStyle = $m[1];
        $css = $m[2];
        // remove HTML comments
        $css = preg_replace('/(?:^\\s*<!--|-->\\s*$)/', '', $css);
       
        // remove CDATA section markers
        $css = self::_removeCdata($css);
       
        // minify
        $minifier = self::$_cssMinifier
            ? self::$_cssMinifier
            : 'trim';
        $css = call_user_func($minifier, $css);
       
        return self::_reservePlace(self::_needsCdata($css)
            ? "{$openStyle}/*<![CDATA[*/{$css}/*]]>*/</style>"
            : "{$openStyle}{$css}</style>"
        );
    }

    protected static function _removeScriptCB($m)
    {
        $openScript = $m[1];
        $js = $m[2];
       
        // remove HTML comments (and ending "//" if present)
        $js = preg_replace('/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $js);
           
        // remove CDATA section markers
        $js = self::_removeCdata($js);
       
        // minify
        $minifier = self::$_jsMinifier
            ? self::$_jsMinifier
            : 'trim';
        $js = call_user_func($minifier, $js);
       
        return self::_reservePlace(self::_needsCdata($js)
            ? "{$openScript}/*<![CDATA[*/{$js}/*]]>*/</script>"
            : "{$openScript}{$js}</script>"
        );
    }


    protected static function _removeCdata($str)
    {
        return (false !== strpos($str, '<![CDATA['))
            ? str_replace(array('<![CDATA[', ']]>'), '', $str)
            : $str;
    }
   
    protected static function _needsCdata($str)
    {
        return (self::$_isXhtml && preg_match('/(?:[<&]|\\-\\-|\\]\\]>)/', $str));
    }
}

