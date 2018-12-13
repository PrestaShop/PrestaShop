<?php
/**
 * CreateStatement.php
 *
 * Builds the CREATE statement
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
 * @version   SVN: $Id: CreateStatementBuilder.php 930 2014-01-08 13:07:55Z phosco@gmx.de $
 * 
 */

require_once dirname(__FILE__) . '/LikeBuilder.php';
require_once dirname(__FILE__) . '/SelectStatementBuilder.php';
require_once dirname(__FILE__) . '/CreateBuilder.php';

/**
 * This class implements the builder for the whole Create statement. You can overwrite
 * all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class CreateStatementBuilder {

    protected function buildLIKE($parsed) {
        $builder = new LikeBuilder();
        return $builder->build($parsed);
    }

    protected function buildSelectStatement($parsed) {
        $builder = new SelectStatementBuilder();
        return $builder->build($parsed);
    }

    protected function buildCREATE($parsed) {
        $builder = new CreateBuilder();
        return $builder->build($parsed);
    }
    
    public function build($parsed) {
        $sql = $this->buildCREATE($parsed);
        if (isset($parsed['LIKE'])) {
            $sql .= " " . $this->buildLIKE($parsed['LIKE']);
        }
        if (isset($parsed['SELECT'])) {
            $sql .= " " . $this->buildSelectStatement($parsed);
        }
        return $sql;
    }
}
