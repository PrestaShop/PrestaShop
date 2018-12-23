<?php
/**
 * TableBracketExpressionBuilder.php
 *
 * Builds the table expressions within the create definitions of CREATE TABLE.
 *
 * PHP version 5
 *
 * LICENSE:
 * Copyright (c) 2010-2014 Justin Swanhart and André Rothe
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @author    André Rothe <andre.rothe@phosco.info>
 * @copyright 2010-2014 Justin Swanhart and André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id: TableBracketExpressionBuilder.php 928 2014-01-08 13:01:57Z phosco@gmx.de $
 * 
 */

require_once dirname(__FILE__) . '/../exceptions/UnableToCreateSQLException.php';
require_once dirname(__FILE__) . '/ColumnDefinitionBuilder.php';
require_once dirname(__FILE__) . '/PrimaryKeyBuilder.php';
require_once dirname(__FILE__) . '/ForeignKeyBuilder.php';
require_once dirname(__FILE__) . '/CheckBuilder.php';
require_once dirname(__FILE__) . '/LikeExpressionBuilder.php';
require_once dirname(__FILE__) . '/../utils/ExpressionType.php';
/**
 * This class implements the builder for the table expressions 
 * within the create definitions of CREATE TABLE. 
 * You can overwrite all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class TableBracketExpressionBuilder {
    protected function buildColDef($parsed) {
        $builder = new ColumnDefinitionBuilder();

        return $builder->build($parsed);
    }

    protected function buildPrimaryKey($parsed) {
        $builder = new PrimaryKeyBuilder();

        return $builder->build($parsed);
    }

    protected function buildForeignKey($parsed) {
        $builder = new ForeignKeyBuilder();

        return $builder->build($parsed);
    }

    protected function buildCheck($parsed) {
        $builder = new CheckBuilder();

        return $builder->build($parsed);
    }

    protected function buildLikeExpression($parsed) {
        $builder = new LikeExpressionBuilder();

        return $builder->build($parsed);
    }

    public function build($parsed) {
        if ($parsed['expr_type'] !== ExpressionType::BRACKET_EXPRESSION) {
            return "";
        }
        $sql = "";
        foreach ($parsed['sub_tree'] as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->buildColDef($v);
            $sql .= $this->buildPrimaryKey($v);
            $sql .= $this->buildCheck($v);
            $sql .= $this->buildLikeExpression($v);
            $sql .= $this->buildForeignKey($v);

            if ($len == strlen($sql)) {
                throw new UnableToCreateSQLException('CREATE TABLE create-def expression subtree', $k, $v, 'expr_type');
            }

            $sql .= ", ";
        }

        $sql = " (" . substr($sql, 0, -2) . ")";

        return $sql;
    }
}
