<?php
/**
 * position-calculator.php.
 *
 * This file implements the calculator for the position elements of
 * the output of the PHPSQLParser.
 *
 * Copyright (c) 2010-2012, Justin Swanhart
 * with contributions by André Rothe <arothe@phosco.info, phosco@gmx.de>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 */
require_once dirname(__FILE__).'/../utils/PHPSQLParserConstants.php';
require_once dirname(__FILE__).'/../exceptions/UnableToCalculatePositionException.php';

/**
 * This class calculates the positions
 * of base_expr within the original SQL statement.
 *
 * @author arothe <andre.rothe@phosco.info>
 */
class PositionCalculator
{
    private static $_allowedOnOperator = array("\t", "\n", "\r", ' ', ',', '(', ')', '_', "'", '"');
    private static $_allowedOnOther = array("\t", "\n", "\r", ' ', ',', '(', ')', '<', '>', '*', '+', '-', '/', '|',
                                            '&', '=', '!', ';', );

    private function _printPos($text, $sql, $charPos, $key, $parsed, $backtracking)
    {
        if (!isset($_ENV['DEBUG'])) {
            return;
        }

        $spaces = '';
        $caller = debug_backtrace();
        $i = 1;
        while ('lookForBaseExpression' === $caller[$i]['function']) {
            $spaces .= '   ';
            ++$i;
        }
        $holdem = mb_substr($sql, 0, $charPos).'^'.mb_substr($sql, $charPos);
        echo $spaces.$text.' key:'.$key.'  parsed:'.$parsed.' back:'.serialize($backtracking).' '
            .$holdem."\n";
    }

    public function setPositionsWithinSQL($sql, $parsed)
    {
        $charPos = 0;
        $backtracking = array();
        $this->lookForBaseExpression($sql, $charPos, $parsed, 0, $backtracking);

        return $parsed;
    }

    private function findPositionWithinString($sql, $value, $expr_type)
    {
        $offset = 0;
        $ok = false;
        while (true) {
            $pos = mb_strpos($sql, $value, $offset);
            if (false === $pos) {
                break;
            }

            $before = '';
            if ($pos > 0) {
                $before = $sql[$pos - 1];
            }

            $after = '';
            if (isset($sql[$pos + mb_strlen($value)])) {
                $after = $sql[$pos + mb_strlen($value)];
            }

            // if we have an operator, it should be surrounded by
            // whitespace, comma, parenthesis, digit or letter, end_of_string
            // an operator should not be surrounded by another operator

            if ('operator' === $expr_type) {
                $ok = ('' === $before || in_array($before, self::$_allowedOnOperator, true))
                    || (mb_strtolower($before) >= 'a' && mb_strtolower($before) <= 'z') || ($before >= '0' && $before <= '9');
                $ok = $ok
                    && ('' === $after || in_array($after, self::$_allowedOnOperator, true)
                        || (mb_strtolower($after) >= 'a' && mb_strtolower($after) <= 'z') || ($after >= '0' && $after <= '9')
                        || ('?' === $after) || ('@' === $after));

                if (!$ok) {
                    $offset = $pos + 1;
                    continue;
                }

                break;
            }

            // in all other cases we accept
            // whitespace, comma, operators, parenthesis and end_of_string

            $ok = ('' === $before || in_array($before, self::$_allowedOnOther, true));
            $ok = $ok && ('' === $after || in_array($after, self::$_allowedOnOther, true));

            if ($ok) {
                break;
            }

            $offset = $pos + 1;
        }

        return $pos;
    }

    private function lookForBaseExpression($sql, &$charPos, &$parsed, $key, &$backtracking)
    {
        if (!is_numeric($key)) {
            if (('UNION' === $key || 'UNION ALL' === $key)
                || ('expr_type' === $key && ExpressionType::EXPRESSION === $parsed)
                || ('expr_type' === $key && ExpressionType::SUBQUERY === $parsed)
                || ('expr_type' === $key && ExpressionType::BRACKET_EXPRESSION === $parsed)
                || ('expr_type' === $key && ExpressionType::TABLE_EXPRESSION === $parsed)
                || ('expr_type' === $key && ExpressionType::RECORD === $parsed)
                || ('expr_type' === $key && ExpressionType::IN_LIST === $parsed)
                || ('expr_type' === $key && ExpressionType::MATCH_ARGUMENTS === $parsed)
                || ('expr_type' === $key && ExpressionType::TABLE === $parsed)
                || ('expr_type' === $key && ExpressionType::TEMPORARY_TABLE === $parsed)
                || ('expr_type' === $key && ExpressionType::COLUMN_TYPE === $parsed)
                || ('expr_type' === $key && ExpressionType::COLDEF === $parsed)
                || ('expr_type' === $key && ExpressionType::PRIMARY_KEY === $parsed)
                || ('expr_type' === $key && ExpressionType::CONSTRAINT === $parsed)
                || ('expr_type' === $key && ExpressionType::COLUMN_LIST === $parsed)
                || ('expr_type' === $key && ExpressionType::CHECK === $parsed)
                || ('expr_type' === $key && ExpressionType::COLLATE === $parsed)
                || ('expr_type' === $key && ExpressionType::LIKE === $parsed)
                || ('expr_type' === $key && ExpressionType::INDEX === $parsed)
                || ('select-option' === $key && false !== $parsed) || ('alias' === $key && false !== $parsed)) {
                // we hold the current position and come back after the next base_expr
                // we do this, because the next base_expr contains the complete expression/subquery/record
                // and we have to look into it too
                $backtracking[] = $charPos;
            } elseif (('ref_clause' === $key || 'columns' === $key) && false !== $parsed) {
                // we hold the current position and come back after n base_expr(s)
                // there is an array of sub-elements before (!) the base_expr clause of the current element
                // so we go through the sub-elements and must come at the end
                $backtracking[] = $charPos;
                for ($i = 1; $i < count($parsed); ++$i) {
                    $backtracking[] = false; // backtracking only after n base_expr!
                }
            } elseif (('sub_tree' === $key && false !== $parsed) || ('options' === $key && false !== $parsed)) {
                // we prevent wrong backtracking on subtrees (too much array_pop())
                // there is an array of sub-elements after(!) the base_expr clause of the current element
                // so we go through the sub-elements and must not come back at the end
                for ($i = 1; $i < count($parsed); ++$i) {
                    $backtracking[] = false;
                }
            } elseif (('TABLE' === $key) || ('create-def' === $key && false !== $parsed)) {
                // do nothing
            } else {
                // move the current pos after the keyword
                // SELECT, WHERE, INSERT etc.
                if (PHPSQLParserConstants::isReserved($key)) {
                    $charPos = mb_stripos($sql, $key, $charPos);
                    $charPos += mb_strlen($key);
                }
            }
        }

        if (!is_array($parsed)) {
            return;
        }

        foreach ($parsed as $key => $value) {
            if ('base_expr' === $key) {
                //$this->_printPos("0", $sql, $charPos, $key, $value, $backtracking);

                $subject = mb_substr($sql, $charPos);
                $pos = $this->findPositionWithinString(
                    $subject,
                    $value,
                    isset($parsed['expr_type']) ? $parsed['expr_type'] : 'alias'
                );
                if (false === $pos) {
                    throw new UnableToCalculatePositionException($value, $subject);
                }

                $parsed['position'] = $charPos + $pos;
                $charPos += $pos + mb_strlen($value);

                //$this->_printPos("1", $sql, $charPos, $key, $value, $backtracking);

                $oldPos = array_pop($backtracking);
                if (isset($oldPos) && false !== $oldPos) {
                    $charPos = $oldPos;
                }

                //$this->_printPos("2", $sql, $charPos, $key, $value, $backtracking);
            } else {
                $this->lookForBaseExpression($sql, $charPos, $parsed[$key], $key, $backtracking);
            }
        }
    }
}
