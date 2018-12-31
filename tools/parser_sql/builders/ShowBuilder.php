<?php
/**
 * ShowBuilder.php
 *
 * Builds the SHOW statement.
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
 * @version   SVN: $Id: ShowBuilder.php 830 2013-12-18 09:35:42Z phosco@gmx.de $
 * 
 */

require_once __DIR__ . '/../exceptions/UnableToCreateSQLException.php';
require_once __DIR__ . '/ReservedBuilder.php';
require_once __DIR__ . '/ConstantBuilder.php';
require_once __DIR__ . '/EngineBuilder.php';
require_once __DIR__ . '/FunctionBuilder.php';
require_once __DIR__ . '/ProcedureBuilder.php';
require_once __DIR__ . '/DatabaseBuilder.php';
require_once __DIR__ . '/TableBuilder.php';

/**
 * This class implements the builder for the SHOW statement. 
 * You can overwrite all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class ShowBuilder {
    protected function buildTable($parsed, $delim) {
        $builder = new TableBuilder();

        return $builder->build($parsed, $delim);
    }

    protected function buildFunction($parsed) {
        $builder = new FunctionBuilder();

        return $builder->build($parsed);
    }

    protected function buildProcedure($parsed) {
        $builder = new ProcedureBuilder();

        return $builder->build($parsed);
    }

    protected function buildDatabase($parsed) {
        $builder = new DatabaseBuilder();

        return $builder->build($parsed);
    }

    protected function buildEngine($parsed) {
        $builder = new EngineBuilder();

        return $builder->build($parsed);
    }

    protected function buildConstant($parsed) {
        $builder = new ConstantBuilder();

        return $builder->build($parsed);
    }

    protected function buildReserved($parsed) {
        $builder = new ReservedBuilder();

        return $builder->build($parsed);
    }

    public function build($parsed) {
        $show = $parsed['SHOW'];
        $sql = "";
        foreach ($show as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->buildReserved($v);
            $sql .= $this->buildConstant($v);
            $sql .= $this->buildEngine($v);
            $sql .= $this->buildDatabase($v);
            $sql .= $this->buildProcedure($v);
            $sql .= $this->buildFunction($v);
            $sql .= $this->buildTable($v, 0);

            if ($len == strlen($sql)) {
                throw new UnableToCreateSQLException('SHOW', $k, $v, 'expr_type');
            }

            $sql .= " ";
        }

        $sql = substr($sql, 0, -1);

        return "SHOW " . $sql;
    }
}
