<?php
/**
 * LikeExpressionBuilder.php
 *
 * Builds the LIKE keyword within parenthesis.
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
 *
 * @version   SVN: $Id: LikeExpressionBuilder.php 906 2014-01-07 14:38:08Z phosco@gmx.de $
 */

require_once dirname(__FILE__) . '/../utils/ExpressionType.php';
require_once dirname(__FILE__) . '/TableBuilder.php';
require_once dirname(__FILE__) . '/ReservedBuilder.php';

/**
 * This class implements the builder for the (LIKE) keyword within a 
 * CREATE TABLE statement. There are difference to LIKE (without parenthesis), 
 * the latter is a top-level element of the output array.
 * You can overwrite all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 */
class LikeExpressionBuilder {

    protected function buildTable($parsed, $index) {
        $builder = new TableBuilder();
        return $builder->build($parsed, $index);
    }

    protected function buildReserved($parsed) {
        $builder = new ReservedBuilder();
        return $builder->build($parsed);
    }

    public function build($parsed) {
        if ($parsed['expr_type'] !== ExpressionType::LIKE) {
            return "";
        }
        $sql = "";
        foreach ($parsed['sub_tree'] as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->buildReserved($v);
            $sql .= $this->buildTable($v, 0);

            if ($len == strlen($sql)) {
                throw new UnableToCreateSQLException('CREATE TABLE create-def (like) subtree', $k, $v, 'expr_type');
            }

            $sql .= " ";
        }
        return substr($sql, 0, -1);
    }
}
