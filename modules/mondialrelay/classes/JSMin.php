<?php
/**
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is a direct port of jsmin.c to PHP with a few PHP performance tweaks and
 * modifications to preserve some comments (see below). Also, rather than using
 * stdin/stdout, JSMin::minify() accepts a string as input and returns another
 * string as output.
 * 
 * Comments containing IE conditional compilation are preserved, as are multi-line
 * comments that begin with "/*!" (for documentation purposes). In the latter case
 * newlines are inserted around the comment to enhance readability.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @package JSMin
 * @author Ryan Grove <ryan@wonko.com> (PHP port)
 * @author Steve Clay <steve@mrclay.org> (modifications + cleanup)
 * @author Andrea Giammarchi <http://www.3site.eu> (spaceBeforeRegExp)
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @link http://code.google.com/p/jsmin-php/
 */

class JSMin {
    const ORD_LF            = 10;
    const ORD_SPACE         = 32;
    const ACTION_KEEP_A     = 1;
    const ACTION_DELETE_A   = 2;
    const ACTION_DELETE_A_B = 3;
    
    protected $a           = "\n";
    protected $b           = '';
    protected $input       = '';
    protected $inputIndex  = 0;
    protected $inputLength = 0;
    protected $lookAhead   = null;
    protected $output      = '';
    
    /**
     * Minify Javascript
     *
     * @param string $js Javascript to be minified
     * @return string
     */
    public static function minify($js)
    {
        $jsmin = new JSMin($js);
        return $jsmin->min();
    }
    
    /**
     * Setup process
     */
    public function __construct($input)
    {
        $this->input       = str_replace("\r\n", "\n", $input);
        $this->inputLength = strlen($this->input);
    }
    
    /**
     * Perform minification, return result
     */
    public function min()
    {
        if ($this->output !== '') { // min already run
            return $this->output;
        }
        $this->action(self::ACTION_DELETE_A_B);
        
        while ($this->a !== null) {
            // determine next command
            $command = self::ACTION_KEEP_A; // default
            if ($this->a === ' ') {
                if (! $this->isAlphaNum($this->b)) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif ($this->a === "\n") {
                if ($this->b === ' ') {
                    $command = self::ACTION_DELETE_A_B;
                } elseif (false === strpos('{[(+-', $this->b) 
                          && ! $this->isAlphaNum($this->b)) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif (! $this->isAlphaNum($this->a)) {
                if ($this->b === ' '
                    || ($this->b === "\n" 
                        && (false === strpos('}])+-"\'', $this->a)))) {
                    $command = self::ACTION_DELETE_A_B;
                }
            }
            $this->action($command);
        }
        $this->output = trim($this->output);
        return $this->output;
    }
    
    /**
     * ACTION_KEEP_A = Output A. Copy B to A. Get the next B.
     * ACTION_DELETE_A = Copy B to A. Get the next B.
     * ACTION_DELETE_A_B = Get the next B.
     */
    protected function action($command)
    {
        switch ($command) {
            case self::ACTION_KEEP_A:
                $this->output .= $this->a;
                // fallthrough
            case self::ACTION_DELETE_A:
                $this->a = $this->b;
                if ($this->a === "'" || $this->a === '"') { // string literal
                    $str = $this->a; // in case needed for exception
                    while (true) {
                        $this->output .= $this->a;
                        $this->a       = $this->get();
                        if ($this->a === $this->b) { // end quote
                            break;
                        }
                        if (ord($this->a) <= self::ORD_LF) {
                            throw new JSMin_UnterminatedStringException(
                                'Unterminated String: ' . var_export($str, true));
                        }
                        $str .= $this->a;
                        if ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a       = $this->get();
                            $str .= $this->a;
                        }
                    }
                }
                // fallthrough
            case self::ACTION_DELETE_A_B:
                $this->b = $this->next();
                if ($this->b === '/' && $this->isRegexpLiteral()) { // RegExp literal
                    $this->output .= $this->a . $this->b;
                    $pattern = '/'; // in case needed for exception
                    while (true) {
                        $this->a = $this->get();
                        $pattern .= $this->a;
                        if ($this->a === '/') { // end pattern
                            break; // while (true)
                        } elseif ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a       = $this->get();
                            $pattern      .= $this->a;
                        } elseif (ord($this->a) <= self::ORD_LF) {
                            throw new JSMin_UnterminatedRegExpException(
                                'Unterminated RegExp: '. var_export($pattern, true));
                        }
                        $this->output .= $this->a;
                    }
                    $this->b = $this->next();
                }
            // end case ACTION_DELETE_A_B
        }
    }
    
    protected function isRegexpLiteral()
    {
        if (false !== strpos("\n{;(,=:[!&|?", $this->a)) { // we aren't dividing
            return true;
        }
        if (' ' === $this->a) {
            $length = strlen($this->output);
            if ($length < 2) { // weird edge case
                return true;
            }
            // you can't divide a keyword
            if (preg_match('/(?:case|else|in|return|typeof)$/', $this->output, $m)) {
                if ($this->output === $m[0]) { // odd but could happen
                    return true;
                }
                // make sure it's a keyword, not end of an identifier
                $charBeforeKeyword = substr($this->output, $length - strlen($m[0]) - 1, 1);
                if (! $this->isAlphaNum($charBeforeKeyword)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Get next char. Convert ctrl char to space.
     */
    protected function get()
    {
        $c = $this->lookAhead;
        $this->lookAhead = null;
        if ($c === null) {
            if ($this->inputIndex < $this->inputLength) {
                $c = $this->input[$this->inputIndex];
                $this->inputIndex += 1;
            } else {
                return null;
            }
        }
        if ($c === "\r" || $c === "\n") {
            return "\n";
        }
        if (ord($c) < self::ORD_SPACE) { // control char
            return ' ';
        }
        return $c;
    }
    
    /**
     * Get next char. If is ctrl character, translate to a space or newline.
     */
    protected function peek()
    {
        $this->lookAhead = $this->get();
        return $this->lookAhead;
    }
    
    /**
     * Is $c a letter, digit, underscore, dollar sign, escape, or non-ASCII?
     */
    protected function isAlphaNum($c)
    {
        return (preg_match('/^[0-9a-zA-Z_\\$\\\\]$/', $c) || ord($c) > 126);
    }
    
    protected function singleLineComment()
    {
        $comment = '';
        while (true) {
            $get = $this->get();
            $comment .= $get;
            if (ord($get) <= self::ORD_LF) { // EOL reached
                // if IE conditional comment
                if (preg_match('/^\\/@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                    return "/{$comment}";
                }
                return $get;
            }
        }
    }
    
    protected function multipleLineComment()
    {
        $this->get();
        $comment = '';
        while (true) {
            $get = $this->get();
            if ($get === '*') {
                if ($this->peek() === '/') { // end of comment reached
                    $this->get();
                    // if comment preserved by YUI Compressor
                    if (0 === strpos($comment, '!')) {
                        return "\n/*" . substr($comment, 1) . "*/\n";
                    }
                    // if IE conditional comment
                    if (preg_match('/^@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                        return "/*{$comment}*/";
                    }
                    return ' ';
                }
            } elseif ($get === null) {
                throw new JSMin_UnterminatedCommentException('Unterminated Comment: ' . var_export('/*' . $comment, true));
            }
            $comment .= $get;
        }
    }
    
    /**
     * Get the next character, skipping over comments.
     * Some comments may be preserved.
     */
    protected function next()
    {
        $get = $this->get();
        if ($get !== '/') {
            return $get;
        }
        switch ($this->peek()) {
            case '/': return $this->singleLineComment();
            case '*': return $this->multipleLineComment();
            default: return $get;
        }
    }
}

class JSMin_UnterminatedStringException extends Exception {}
class JSMin_UnterminatedCommentException extends Exception {}
class JSMin_UnterminatedRegExpException extends Exception {}
