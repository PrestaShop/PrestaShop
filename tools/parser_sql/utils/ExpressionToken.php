<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


require_once dirname(__FILE__) . '/ExpressionType.php';

class ExpressionToken {

    private $subTree;
    private $expression;
    private $key;
    private $token;
    private $tokenType;
    private $trim;
    private $upper;
    private $noQuotes;

    public function __construct($key = "", $token = "") {
        $this->subTree = false;
        $this->expression = "";
        $this->key = $key;
        $this->token = $token;
        $this->tokenType = false;
        $this->trim = trim($token);
        $this->upper = strtoupper($this->trim);
        $this->noQuotes = null;
    }

    // TODO: we could replace it with a constructor new ExpressionToken(this, "*")
    public function addToken($string) {
        $this->token .= $string;
    }

    public function isEnclosedWithinParenthesis() {
        return ($this->upper[0] === '(' && substr($this->upper, -1) === ')');
    }

    public function setSubTree($tree) {
        $this->subTree = $tree;
    }

    public function getSubTree() {
        return $this->subTree;
    }

    public function getUpper($idx = false) {
        return $idx !== false ? $this->upper[$idx] : $this->upper;
    }

    public function getTrim($idx = false) {
        return $idx !== false ? $this->trim[$idx] : $this->trim;
    }

    public function getToken($idx = false) {
        return $idx !== false ? $this->token[$idx] : $this->token;
    }

    public function setNoQuotes($token, $qchars = '`') {
        $this->noQuotes = ($token === null) ? null : $this->revokeQuotation($token, $qchars);
    }
    
    public function setTokenType($type) {
        $this->tokenType = $type;
    }

    public function endsWith($needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        $start = $length * -1;
        return (substr($this->token, $start) === $needle);
    }

    public function isWhitespaceToken() {
        return ($this->trim === "");
    }

    public function isCommaToken() {
        return ($this->trim === ",");
    }

    public function isVariableToken() {
        return $this->upper[0] === '@';
    }

    public function isSubQueryToken() {
        return preg_match("/^\\(\\s*SELECT/i", $this->trim);
    }

    public function isExpression() {
        return $this->tokenType === ExpressionType::EXPRESSION;
    }

    public function isBracketExpression() {
        return $this->tokenType === ExpressionType::BRACKET_EXPRESSION;
    }

    public function isOperator() {
        return $this->tokenType === ExpressionType::OPERATOR;
    }

    public function isInList() {
        return $this->tokenType === ExpressionType::IN_LIST;
    }

    public function isFunction() {
        return $this->tokenType === ExpressionType::SIMPLE_FUNCTION;
    }

    public function isUnspecified() {
        return ($this->tokenType === false);
    }

    public function isVariable() {
        return $this->tokenType === ExpressionType::GLOBAL_VARIABLE || $this->tokenType === ExpressionType::LOCAL_VARIABLE || $this->tokenType === ExpressionType::USER_VARIABLE;
    }
    
    public function isAggregateFunction() {
        return $this->tokenType === ExpressionType::AGGREGATE_FUNCTION;
    }

    public function isColumnReference() {
        return $this->tokenType === ExpressionType::COLREF;
    }

    public function isConstant() {
        return $this->tokenType === ExpressionType::CONSTANT;
    }

    public function isSign() {
        return $this->tokenType === ExpressionType::SIGN;
    }

    public function isSubQuery() {
        return $this->tokenType === ExpressionType::SUBQUERY;
    }

    private function revokeQuotation($token, $qchars = '`') {
        $result = trim($token);
        for ($i = 0; $i < strlen($qchars); $i++) {
            $quote = $qchars[$i];
            if (($result[0] === $quote) && ($result[strlen($result) - 1] === $quote)) {
                $result = substr($result, 1, -1);
                return trim(str_replace($quote.$quote, $quote, $result));
            }
        }
        return $token;
    }
    
    public function toArray() {
        $result = array();
        $result['expr_type'] = $this->tokenType;
        $result['base_expr'] = $this->token;
        if (!empty($this->noQuotes)) {
            $result['no_quotes'] = $this->noQuotes;   
        }
        $result['sub_tree'] = $this->subTree;
        return $result;
    }
}
