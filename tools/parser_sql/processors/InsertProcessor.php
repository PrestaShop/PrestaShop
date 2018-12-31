<?php
/**
 * InsertProcessor.php
 *
 * This file implements the processor for the INSERT statements.
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

require_once __DIR__ . '/AbstractProcessor.php';
require_once __DIR__ . '/ColumnListProcessor.php';
require_once __DIR__ . '/../utils/ExpressionType.php';

/**
 * 
 * This class processes the INSERT statements.
 * 
 * @author arothe
 * 
 */
class InsertProcessor extends AbstractProcessor {
    public function process($tokenList, $token_category = 'INSERT') {
        $table = "";
        $cols = array();

        $into = $tokenList['INTO'];
        foreach ($into as $token) {
            if ($this->isWhitespaceToken($token))
                continue;
            if ($table === "") {
                $table = $token;
            } elseif (empty($cols)) {
                $cols[] = $token;
            }
        }

        if (empty($cols)) {
            $cols = false;
        } else {
            $processor = new ColumnListProcessor();
            $cols = $processor->process($this->removeParenthesisFromStart($cols[0]));
        }

        unset($tokenList['INTO']);
        $tokenList[$token_category][0] = array('table' => $table, 'columns' => $cols, 'base_expr' => $table,
            'no_quotes' => $this->revokeQuotation($table),
        );

        return $tokenList;
    }
}
