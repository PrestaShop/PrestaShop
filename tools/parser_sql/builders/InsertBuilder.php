<?php
/**
 * InsertBuilder.php
 *
 * Builds the [INSERT] statement part.
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
 * @version   SVN: $Id: InsertBuilder.php 830 2013-12-18 09:35:42Z phosco@gmx.de $
 * 
 */

require_once dirname(__FILE__) . '/../exceptions/UnableToCreateSQLException.php';
require_once dirname(__FILE__) . '/ColumnReferenceBuilder.php';

/**
 * This class implements the builder for the [INSERT] statement parts. 
 * You can overwrite all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class InsertBuilder {

    protected function buildColRef($parsed) {
        $builder = new ColumnReferenceBuilder();
        return $builder->build($parsed);
    }

    public function build($parsed) {
        $sql = "INSERT INTO " . $parsed['table'];

        if ($parsed['columns'] === false) {
            return $sql;
        }

        $columns = "";
        foreach ($parsed['columns'] as $k => $v) {
            $len = strlen($columns);
            $columns .= $this->buildColRef($v);

            if ($len == strlen($columns)) {
                throw new UnableToCreateSQLException('INSERT[columns]', $k, $v, 'expr_type');
            }

            $columns .= ",";
        }

        if ($columns !== "") {
            $columns = " (" . substr($columns, 0, -1) . ")";
        }

        $sql .= $columns;
        return $sql;
    }
    
}
?>
