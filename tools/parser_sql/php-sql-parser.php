<?php 

if(!defined('HAVE_PHP_SQL_PARSER')) {
	class parserSql {
		var $reserved = array();
		var $functions = array();
		function __construct($sql = false) {
			#LOAD THE LIST OF RESERVED WORDS
			$this->load_reserved_words();
			if($sql) $this->parse($sql);
		}
	
		function parse($sql) {
			$sql = trim($sql);
	
			#lex the SQL statement
			$in = $this->split_sql($sql);
		
			#sometimes the parser needs to skip ahead until a particular 
			#token is found
			$skip_until = false;
			
			#this is the output tree which is being parsed
			$out = array();
	
			#This is the last type of union used (UNION or UNION ALL)
			#indicates a) presence of at least one union in this query
			#          b) the type of union if this is the first or last query
			$union = false;
	
			#Sometimes a "query" consists of more than one query (like a UNION query)
			#this array holds all the queries
			$queries=array();
	
			#This is the highest level lexical analysis.  This is the part of the
			#code which finds UNION and UNION ALL query parts
			foreach($in as $key => $token) {
				$token=trim($token);
			
				if($skip_until) {
					if($token) {
						if(strtoupper($token) == $skip_until) {
							$skip_until = false;
							continue;
						}
					} else { 
						continue;
					}
				}
	
				if(strtoupper($token) == "UNION") {
					$union = 'UNION';
					for($i=$key+1;$i<count($in);++$i) {
						if(trim($in[$i]) == '') continue;
						if(strtoupper($in[$i]) == 'ALL')  {
							$skip_until = 'ALL';
							$union = 'UNION ALL';
							continue ;
						} else {
							break;
						}
					}
	
					$queries[$union][] = $out;
					$out = array();
					
				} else { 
					$out[]=$token;
				}
	
			}
	
			if(!empty($out)) {
				if ($union) {
					$queries[$union][] = $out;
				} else {
					$queries[] = $out;
				}
			}
			
	
			/*MySQL supports a special form of UNION:
			(select ...)
			union
			(select ...)
	
			This block handles this query syntax.  Only one such subquery
			is supported in each UNION block.  (select)(select)union(select) is not legal.
			The extra queries will be silently ignored.
			*/
			$union_types = array('UNION','UNION ALL');
			foreach($union_types as $union_type) {
				if(!empty($queries[$union_type])) {
					foreach($queries[$union_type] as $i => $tok_list) { 
						foreach($tok_list as $z => $tok) {
							$tok = trim($tok);
							if(!$tok) continue;
							if(preg_match('/^\\(\\s*select\\s*/i', $tok)) {
								$queries[$union_type][$i] = $this->parse(substr($tok,1,-1));
								break;
							} else {
								$queries[$union_type][$i] = $this->process_sql($queries[$union_type][$i]);
								break;
							}
						}
					}
				}
			}
	
			
			/* If there was no UNION or UNION ALL in the query, then the query is 
			stored at $queries[0].
			*/
			if(!empty($queries[0])) {
				$queries[0] = $this->process_sql($queries[0]);
				
			}
	
			if(count($queries) == 1 && !$union) {
				$queries = $queries[0];
			}
	
			$this->parsed = $queries;
			return $this->parsed;
		}
	
		#This function counts open and close parenthesis and
		#returns their location.  This might be faster as a regex
		private function count_paren($token,$chars=array('(',')')) {
			$len = strlen($token);
			$open=array();
			$close=array();
			for($i=0;$i<$len;++$i){
				if($token[$i] == $chars[0]) {
					$open[] = $i;
				} elseif($token[$i] == $chars[1]) {
					$close[] = $i;
				}
				
			}
			return array('open' => $open, 'close' => $close, 'balanced' =>( count($close) - count($open)));
		}
	
		#This function counts open and close parenthesis and
		#returns their location.  This might be faster as a regex
		private function count_backtick($token) {
			$len = strlen($token);
			$cnt=0;
			for($i=0;$i<$len;++$i){
				if($token[$i] == '`') ++$cnt;
			}
			return $cnt;
		}
	
		#This is the lexer
		#this function splits up a SQL statement into easy to "parse"
		#tokens for the SQL processor
		private function split_sql($sql) {
	
				if(!is_string($sql)) {
					echo "SQL:\n";
					print_r($sql);
					exit;
				}
	
				$sql = str_replace(array('\\\'','\\"',"\r\n","\n","()"),array("''",'""'," "," "," "), $sql);
	                        $regex=<<<EOREGEX
/(`(?:[^`]|``)`|[@A-Za-z0-9_.`-]+(?:\(\s*\)){0,1})
|(\+|-|\*|\/|!=|<>|>|<|&&|\|\||=|\^)
|(\(.*?\))   # Match FUNCTION(...) OR BAREWORDS
|('(?:[^']|'')*'+)
|("(?:[^"]|"")*"+)
|([^ ,]+)
/ix
EOREGEX
;
	
		        $tokens = preg_split($regex, $sql,-1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			$token_count = count($tokens);
	
			/* The above regex has one problem, because the parenthetical match is not greedy.
			   Thus, when matching grouped expresions such as ( (a and b) or c) the 
			   tokenizer will produce "( (a and b)", " ", "or", " " , "c,")" 
		
			   This block detects the number of open/close parens in the given token.  If the parens are balanced
			   (balanced == 0) then we don't need to do anything.
			
			   otherwise, we need to balance the expression.
		        */
			$reset = false;
			for($i=0;$i<$token_count;++$i) {
	
				if(empty($tokens[$i])) continue;
				
				$token = $tokens[$i];
				$trim = trim($token);
				if($trim) {
					if($trim[0] != '(' 
						&& substr($trim,-1) == ')') {
						$trim=trim(substr($trim,0,
								strpos($trim,'(')));
					}
					$tokens[$i]=$trim;
					$token=$trim;
				}
	
				if($token && $token[0] == '(') {
					$info = $this->count_paren($token);
					if($info['balanced'] == 0) {
						continue;
					}
	
					#we need to find this many closing parens
					$needed = abs($info['balanced']);
					$n = $i;
					while($needed > 0 && $n <$token_count-1) {
						++$n;
						#echo "LOOKING FORWARD TO $n [ " . $tokens[$n] . "]\n";
						$token2 = $tokens[$n];
						$info2 = $this->count_paren($token2);
						$closes = count($info2['close']);
						if($closes != $needed) {
							$tokens[$i] .= $tokens[$n];
							unset($tokens[$n]);
							$reset = true;
							$info2 = $this->count_paren($tokens[$i]);
							$needed = abs($info2['balanced']);
						#	echo "CLOSES LESS THAN NEEDED (still need $needed)\n";
						} else {
							/*get the string pos of the last close paren we need*/
							$pos = $info2['close'][count($info2['close'])-1];
							$str1 = $str2 = "";
							if($pos == 0) { 
								$str1 = ')';
							} else {
								$str1 = substr($tokens[$n],0,$pos) . ')';
								$str2 = substr($tokens[$n],$pos+1);
							}
							#echo "CLOSES FOUND AT $n, offset:$pos  [$str1] [$str2]\n";
							if(strlen($str2) > 0) { 
								$tokens[$n] = $str2;
							} else {
								unset($tokens[$n]);
								$reset = true;
							}
							$tokens[$i] .= $str1;
							$info2 = $this->count_paren($tokens[$i]);
							$needed = abs($info2['balanced']);
	
						}
					}
				}
			}
	
			#the same problem appears with backticks :(
	
			/* reset the array if we deleted any tokens above */
		        if ($reset) $tokens = array_values($tokens);
	
			$token_count=count($tokens);
			for($i=0;$i<$token_count;++$i) {
				if(empty($tokens[$i])) continue;
				$token=$tokens[$i];
				$needed=true;
				$reset=false;
				if($needed && $token && strpos($token,'`') !== false) {
					$info = $this->count_backtick($token);
					if($info %2 == 0) { #even number of backticks means we are balanced
						continue;
					}
					$needed=1;
	
					$n = $i;
					while($needed && $n <$token_count-1) {
						$reset=true;
						#echo "BACKTICK COUNT[$i]: $info old: {$tokens[$i]}, new: ($token)\n";
						++$n;
						$token .= $tokens[$n];
						unset($tokens[$n]);
						$needed = $this->count_backtick($token) % 2;
					}
				}
				if($reset) $tokens[$i] = $token;
				
			}
			/* reset the array if we deleted any tokens above */
			$tokens = array_values($tokens);
	
			return $tokens;
	
		}
		
		/* This function breaks up the SQL statement into logical sections.
		   Some sections are then further handled by specialized functions.
		*/
		private function process_sql(&$tokens,$start_at = 0, $stop_at = false) {
			$prev_category = "";
			$start = microtime(true);
			$token_category = "";
		
			$skip_next=false;
			$token_count = count($tokens);
		
			if(!$stop_at) {
				$stop_at = $token_count;
			}
	
			$out = false;
		
			for($token_number = $start_at;$token_number<$stop_at;++$token_number) {
				$token = trim($tokens[$token_number]);
				if($token && $token[0] == '(' && $token_category == "") {
					$token_category = 'SELECT';
				}
		
		        /* If it isn't obvious, when $skip_next is set, then we ignore the next real
				token, that is we ignore whitespace.  
		                */
				if($skip_next) {
					#whitespace does not count as a next token
					if($token == "") {
						continue;
					}
				
		            #to skip the token we replace it with whitespace	
					$new_token = "";
					$skip_next = false;
				}
		
				$upper = strtoupper($token);
				switch($upper) {
		
					/* Tokens that get their own sections. These keywords have subclauses. */
					case 'SELECT':	
					case 'ORDER':
					case 'LIMIT':
					case 'SET':
					case 'DUPLICATE':
					case 'VALUES':
					case 'GROUP':
					case 'ORDER':
					case 'HAVING':
					case 'INTO':
					case 'WHERE':	
					case 'RENAME':
					case 'CALL':
					case 'PROCEDURE':
					case 'FUNCTION':
					case 'DATABASE':
					case 'SERVER':
					case 'LOGFILE':
					case 'DEFINER':
					case 'RETURNS':
					case 'EVENT':
					case 'TABLESPACE':
					case 'VIEW':
					case 'TRIGGER':
					case 'DATA':
					case 'DO':
					case 'PASSWORD':
					case 'USER':
					case 'PLUGIN':
					case 'FROM':
					case 'FLUSH':
					case 'KILL':
					case 'RESET':
					case 'START':
					case 'STOP':
					case 'PURGE':
					case 'EXECUTE':
					case 'PREPARE':
					case 'DEALLOCATE':
						if($token == 'DEALLOCATE') {
							$skip_next = true;
						}
						/* this FROM is different from FROM in other DML (not join related) */
						if($token_category == 'PREPARE' && $upper == 'FROM') {
							continue 2;
						} 
						
						$token_category = $upper;
						#$join_type = 'JOIN';
						if($upper == 'FROM' && $token_category == 'FROM') {
							/* DO NOTHING*/
						} else {
							continue 2;
	
						}
					break;
		
					/* These tokens get their own section, but have no subclauses. 
		                           These tokens identify the statement but have no specific subclauses of their own. */
					case 'DELETE':		
					case 'ALTER':		
					case 'INSERT':		
					case 'REPLACE':		
					case 'TRUNCATE':		
					case 'CREATE':		
					case 'TRUNCATE':
					case 'OPTIMIZE':
					case 'GRANT':
					case 'REVOKE':
					case 'SHOW':
					case 'HANDLER':
					case 'LOAD':
					case 'ROLLBACK':
					case 'SAVEPOINT':
					case 'UNLOCK':
					case 'INSTALL':
					case 'UNINSTALL':
					case 'ANALZYE':
					case 'BACKUP':
					case 'CHECK':
					case 'CHECKSUM':
					case 'REPAIR':
					case 'RESTORE':
					case 'CACHE':
					case 'DESCRIBE':
					case 'EXPLAIN':
					case 'USE':
					case 'HELP':
						$token_category = $upper; /* set the category in case these get subclauses
		 							     in a future version of MySQL */
						$out[$upper][0] = $upper;
						continue 2;
					break;
		
					/* This is either LOCK TABLES or SELECT ... LOCK IN SHARE MODE*/
					case 'LOCK':
						if($token_category == "") {
							$token_category = $upper; 
							$out[$upper][0] = $upper; 
						} else {
							$token = 'LOCK IN SHARE MODE';
							$skip_next=true;
							$out['OPTIONS'][] = $token;
						}
						continue 2;
					break;
		
					case 'USING':
						/* USING in FROM clause is different from USING w/ prepared statement*/
						if($token_category == 'EXECUTE') {
							$token_category=$upper;
							continue 2;
						}  
						if($token_category == 'FROM' && !empty($out['DELETE'])) {
							$token_category=$upper;
							continue 2;
						}  
					break;
						
					/* DROP TABLE is different from ALTER TABLE DROP ... */
					case 'DROP':		
						if($token_category != 'ALTER') {
							$token_category = $upper; 
							$out[$upper][0] = $upper;
							continue 2;
						}
					break;
		
					case 'FOR':
						$skip_next=true;
						$out['OPTIONS'][] = 'FOR UPDATE';
						continue 2;
					break;
		
		
					case 'UPDATE':
						if($token_category == "" ) {
							$token_category = $upper;
							continue 2;
							
						}
						if($token_category == 'DUPLICATE') {
							continue 2;
						}
						break;
					break;
		
					case 'START':
						$token = "BEGIN";
						$out[$upper][0] = $upper; 
						$skip_next = true;
					break;
		
					/* These tokens are ignored. */
					case 'BY':
					case 'ALL':
					case 'SHARE':
					case 'MODE':
					case 'TO':
						
					case ';':
						continue 2;
						break;
		
					case 'KEY':
						if($token_category == 'DUPLICATE') {
							continue 2;
						}
					break; 
		
					/* These tokens set particular options for the statement.  They never stand alone.*/
					case 'DISTINCTROW': 
						$token='DISTINCT';
					case 'DISTINCT':
					case 'HIGH_PRIORITY':
					case 'LOW_PRIORITY':
					case 'DELAYED':
					case 'IGNORE':
					case 'FORCE':
					case 'STRAIGHT_JOIN':
					case 'SQL_SMALL_RESULT':
					case 'SQL_BIG_RESULT':
					case 'QUICK':
					case 'SQL_BUFFER_RESULT':
					case 'SQL_CACHE':
					case 'SQL_NO_CACHE':
					case 'SQL_CALC_FOUND_ROWS':
						$out['OPTIONS'][] = $upper;
						continue 2;
					break;
		
					case 'WITH':
						if($token_category == 'GROUP') {
							$skip_next=true;
							$out['OPTIONS'][] = 'WITH ROLLUP';
							continue 2;
						}
					break;
		
		
					case 'AS':
					break;
		
					case '':
					case ',':
					case ';':
						break;
					
					default:
						break;	
				}
		
				if($prev_category == $token_category) { 
					$out[$token_category][] = $token;
				}
		
				$prev_category = $token_category;
			}
	
			if(!$out) return false;
	
		
			#process the SELECT clause
			if(!empty($out['SELECT'])) $out['SELECT'] = $this->process_select($out['SELECT']);	
	
			if(!empty($out['FROM']))   $out['FROM'] = $this->process_from($out['FROM']);	
			if(!empty($out['USING']))   $out['USING'] = $this->process_from($out['USING']);	
			if(!empty($out['UPDATE']))  $out['UPDATE'] = $this->process_from($out['UPDATE']);
	
			if(!empty($out['GROUP']))  $out['GROUP'] = $this->process_group($out['GROUP'], $out['SELECT']);	
			if(!empty($out['ORDER']))  $out['ORDER'] = $this->process_group($out['ORDER'], $out['SELECT']);	
	
			if(!empty($out['LIMIT']))  $out['LIMIT'] = $this->process_limit($out['LIMIT']);
	
			if(!empty($out['WHERE']))  $out['WHERE'] = $this->process_expr_list($out['WHERE']);
			if(!empty($out['HAVING']))  $out['HAVING'] = $this->process_expr_list($out['HAVING']);
			if(!empty($out['SET']))  $out['SET'] = $this->process_set_list($out['SET']);
			if(!empty($out['DUPLICATE'])) {
				$out['ON DUPLICATE KEY UPDATE'] = $this->process_set_list($out['DUPLICATE']);
				unset($out['DUPLICATE']);
			}
			if(!empty($out['INSERT']))  $out = $this->process_insert($out);
			if(!empty($out['REPLACE']))  $out = $this->process_insert($out,'REPLACE');
			if(!empty($out['DELETE']))  $out = $this->process_delete($out);
		
			return $out;
		
		}
	
		/* A SET list is simply a list of key = value expressions separated by comma (,).
		This function produces a list of the key/value expressions.
		*/
		private function process_set_list($tokens) {
			$column="";
			$expression="";
			foreach($tokens as $token) {
				$token=trim($token);
				if(!$column) { 
					if($token === false || empty($token)) continue;
					$column .= $token;
					continue;
				}
	
				if($token == '=') continue;
	
				if($token == ',') {
					$expr[] = array('column' => trim($column), 'expr' => trim($expression));
					$expression = $column = "";
					continue;
				}	
	
				$expression .= $token;
			}
			if($expression) {
				$expr[] = array('column' => trim($column), 'expr' => trim($expression));
			}
	
			return $expr;
		}
		
		/* This function processes the LIMIT section.
		   start,end are set.  If only end is provided in the query
		   then start is set to 0.
		*/
		private function process_limit($tokens) {
			$start = 0;
			$end = 0;
		
			if($pos = array_search(',',$tokens)) {
				for($i=0;$i<$pos;++$i) {
					if($tokens[$i] != '') {
						$start = $tokens[$i];
						break;
					}
				}
				$pos = $pos + 1;
		
			} else {
				$pos = 0;
			}
		
			for($i=$pos;$i<count($tokens);++$i) {
				if($tokens[$i] != '') {
					$end = $tokens[$i];
					break;
				}
			}
					
			return array('start' => $start, 'end' => $end);		
		}
		
		/* This function processes the SELECT section.  It splits the clauses at the commas.
		   Each clause is then processed by process_select_expr() and the results are added to
		   the expression list.
		
		   Finally, at the end, the epxression list is returned.
		*/
		private function process_select(&$tokens) {
			$expression = "";
			$expr = array();
			foreach($tokens as $token) {
				if($token == ',') {
					$expr[] = $this->process_select_expr(trim($expression));
					$expression = "";
				} else {
					if($token === "" || $token===false) $token=" ";
					$expression .= $token ;
				}
		  	}
			if($expression) $expr[] = $this->process_select_expr(trim($expression));
			return $expr;
		}
		
		/* This fuction processes each SELECT clause.  We determine what (if any) alias
		   is provided, and we set the type of expression.
		*/
		private function process_select_expr($expression) {
			$capture = false;
			$alias = "";
			$base_expression = $expression;
			$upper = trim(strtoupper($expression));
			#if necessary, unpack the expression
			if($upper[0] == '(') {
				#$expression = substr($expression,1,-1);
				$base_expression = $expression;
			}
	
			$tokens = $this->split_sql($expression);
			$token_count = count($tokens);
	
			/* Determine if there is an explicit alias after the AS clause.
			If AS is found, then the next non-whitespace token is captured as the alias.
			The tokens after (and including) the AS are removed.
			*/
			$base_expr = "";
			$stripped=array();
			$capture=false;
			$alias = "";
			$processed=false;
			for($i=0;$i<$token_count;++$i) {
				$token = strtoupper($tokens[$i]);
				if(trim($token)) {
					$stripped[] = $tokens[$i];
				}
	
				if($token == 'AS') {
					unset($tokens[$i]);
					$capture = true;
					continue;
				} 
	
				if($capture) {
					if(trim($token)) {
						$alias .= $tokens[$i];
					}
					unset($tokens[$i]);
					continue;
				}
				$base_expr .= $tokens[$i];
			}
	
			$stripped = $this->process_expr_list($stripped);
			$last = array_pop($stripped);
			if(!$alias && $last['expr_type'] == 'colref') {
				$prev = array_pop($stripped);			
				if($prev['expr_type'] == 'operator' || 
				   $prev['expr_type'] == 'const' ||
				   $prev['expr_type'] == 'function' ||
				   $prev['expr_type'] == 'expression' ||
				   #$prev['expr_type'] == 'aggregate_function' || 
				   $prev['expr_type'] == 'subquery' ||	
				   $prev['expr_type'] == 'colref') {
					$alias = $last['base_expr'];
	
					#remove the last token
					array_pop($tokens);
	
					$base_expr = join("", $tokens);
	
					
				}
			}
	
			if(!$alias) {
				$base_expr=join("", $tokens);
				$alias = $base_expr;
			}
		
			/* Properly escape the alias if it is not escaped */
		        if ($alias[0] != '`') {
					$alias = '`' . str_replace('`','``',$alias) . '`';
			}
			$processed = false;
			$type='expression';

			if(substr(trim($base_expr),0,1) == '(') {
				$base_expr = substr($expression,1,-1);
				if(preg_match('/^sel/i', $base_expr)) {
					$type='subquery';
					$processed = $this->parse($base_expr);
				}
			}
			if(!$processed) {
				$processed = $this->process_expr_list($tokens);
			}
	
			if(count($processed) == 1) {
				$type = $processed[0]['expr_type'];
				$processed = false;
			}
	
			return array('expr_type'=>$type,'alias' => $alias, 'base_expr' => $base_expr, 'sub_tree' => $processed);
		
		}
	
			
		private function process_from(&$tokens) {
	
			$expression = "";
			$expr = array();
			$token_count=0;
			$table = "";
			$alias = "";
		
			$skip_next=false;
			$i=0;
			$join_type = '';
			$ref_type="";
			$ref_expr="";
			$base_expr="";
			$sub_tree = false;
			$subquery = "";

			$first_join=true;
			$modifier="";
			$saved_join_type="";
			
			foreach($tokens as $token) {
				$base_expr = false;
				$upper = strtoupper(trim($token));
		
				if($skip_next && $token) {
					$token_count++;
					$skip_next = false;
					continue;
				} else {
					if($skip_next) {
						continue;
					}
				}
				
				if(preg_match("/^\\s*\\(\\s*select/i",$token)) {
					$type = 'subquery';
					$table = "DEPENDENT-SUBQUERY";
					$sub_tree = $this->parse(trim($token,'() '));
					$subquery = $token;
				}
		
				switch($upper) {
					case 'OUTER':
					case 'LEFT':
					case 'RIGHT':
					case 'NATURAL':
					case 'CROSS':						
					case ',':
					case 'JOIN':
						break;
								
					default:
						$expression .= $token == '' ? " " : $token;
						if($ref_type) {
							$ref_expr .= $token == '' ? " " : $token;
						}
					break;
				}
		
				switch($upper) {
					case 'AS':
						$token_count++;
						$n=1;
						$alias = "";
						while($alias == "") {
							$alias = trim($tokens[$i+$n]);
							++$n;
						} 
		
						continue;
					break;
		
					case 'INDEX':
						if($token_category == 'CREATE') {
							$token_category = $upper;
							continue 2;
						}
		
					break;
	
					case 'USING':
					case 'ON':
						$ref_type = $upper;
						$ref_expr = "";
		
					case 'CROSS':
					case 'USE':
					case 'FORCE':
					case 'IGNORE':
					case 'INNER':
					case 'OUTER':
					#	$expression .= $token;
						$token_count++;
						continue;
					break;
		
						
					
					case 'FOR':
						$token_count++;
						$skip_next = true;
						continue;
					break;

					case 'LEFT':
					case 'RIGHT':
					case 'STRAIGHT_JOIN':
						$join_type=$saved_join_type;
						
						$modifier = $upper . " ";
						break;

					
					case ',':
						$modifier = 'CROSS';

					case 'JOIN':

						if($first_join) {
							$join_type = 'JOIN';
							$saved_join_type = ($modifier ? $modifier : 'JOIN');
						}  else {
							$new_join_type = ($modifier ? $modifier : 'JOIN');
							$join_type = $saved_join_type;
							$saved_join_type = $new_join_type;
							unset($new_join_type);
						}

						$first_join = false;
						
						if(!trim($alias)) $alias = $table;

						if($subquery) {
							$sub_tree = $this->parse(trim($subquery,'()'));
							$base_expr=$subquery;
						}

						if(substr(trim($table),0,1) == '(') {
							$base_expr=trim($table,'() ');
							$join_type = 'JOIN';
							$sub_tree = $this->process_from($this->split_sql($base_expr));
							$alias="";
						} 


						if($join_type == "") $join_type='JOIN';
						$expr[] = array('table'=>$table, 'alias'=>$alias,'join_type'=>$join_type,'ref_type'=> $ref_type,'ref_clause'=>trim($ref_expr,'() '), 'base_expr' => $base_expr, 'sub_tree' => $sub_tree);
						$modifier = "";
						#$join_type=$saved_join_type;


						$token_count = 0;
						$table = $alias = $expression = $base_expr = $ref_type = $ref_expr = "";
						$sub_tree=false;
						$subquery = "";
	
					break;

		
					default:
						if($token === false || empty($token) || $token === "") continue;
		
						if($token_count == 0 ) { 
							if(!$table) {	
								$table = $token ;
							}
						} else if($token_count == 1) {
							$alias = $token;	
						}
						$token_count++;
					break;	
				}
				++$i;
		  	}
			if(substr(trim($table),0,1) == '(') {
				$base_expr=trim($table,'() ');
				$join_type = 'JOIN';
				$sub_tree = $this->process_from($this->split_sql($base_expr));
				$alias = "";
			} else {
				if(!trim($alias)) $alias = $table;
			}
			if($join_type == "") $saved_join_type='JOIN';

			$expr[] = array('table'=>$table, 'alias'=>$alias,'join_type'=>$saved_join_type,'ref_type'=> $ref_type,'ref_clause'=> trim($ref_expr,'() '), 'base_expr' => $base_expr, 'sub_tree' => $sub_tree);
	
	
			return $expr;
		}
		
		private function process_group(&$tokens, &$select) {
		
			$out=array();
			$expression = "";
			$direction="ASC";
		        $type = "expression";
			if(!$tokens) return false;
		
			foreach($tokens as $token) {
				switch(strtoupper($token)) {
					case ',':
						$expression = trim($expression);
						if($expression[0] != '`' || substr($expression,-1) != '`') {
							$escaped = str_replace('`','``',$expression);
						} else {
							$escaped = $expression;
						}
						$escaped = '`' . $escaped . '`';
			
						if(is_numeric(trim($expression))) {
							$type = 'pos';	
						} else {
		
						  	#search to see if the expression matches an alias
							foreach($select as $clause) {
								if($clause['alias'] == $escaped) {
									$type = 'alias';
								}	
							}
		
							if(!$type) $type = "expression";
						}
		
						$out[]=array('type'=>$type,'base_expr'=>$expression,'direction'=>$direction);
						$escaped = "";
						$expression = "";
						$direction = "ASC";
						$type = "";
					break;
					
					case 'ASC':
						$direction = "ASC";
					break;
		
					case 'DESC':
						$direction = "DESC";
					break;
		
					default:
						$expression .= $token == '' ? ' ' : $token;
		
		
				}
			}
			if($expression) {
					$expression = trim($expression);
					if($expression[0] != '`' || substr($expression,-1) != '`') {
						$escaped = str_replace('`','``',$expression);
					} else {
						$escaped = $expression;
					}
					$escaped = '`' . $escaped . '`';
			
					if(is_numeric(trim($expression))) {
						$type = 'pos';	
					} else {
		
						#search to see if the expression matches an alias
						if(!$type && $select) {
							foreach($select as $clause) {
								if(!is_array($clause)) continue;
								if($clause['alias'] == $escaped) {
									$type = 'alias';
								}	
							}
						} else {
							$type="expression";
						}
	
						if(!$type) $type = "expression";
					}
				
					$out[]=array('type'=>$type,'base_expr'=>$expression,'direction'=>$direction);
			}
		
			return $out;
		}
		
		/* Some sections are just lists of expressions, like the WHERE and HAVING clauses.  This function
	           processes these sections.  Recursive.
		*/
		private function process_expr_list($tokens) {
			$expr = "";
			$type = "";
			$prev_token = "";
			$skip_next = false;
			$sub_expr = "";
	
			$in_lists = array();
			foreach($tokens as $key => $token) {
		
				if(strlen(trim($token))==0) continue;
				if($skip_next) {
					$skip_next = false;
					continue;
				}
				
				$processed = false;
				$upper = strtoupper(trim($token));
				if(trim($token)) $token=trim($token);
	
				/* is it a subquery?*/
				if(preg_match("/^\\s*\\(\\s*SELECT/i", $token)) {
					$type = 'subquery';
					#tokenize and parse the subquery.
					#we remove the enclosing parenthesis for the tokenizer
					$processed = $this->parse(trim($token,' ()'));
	
	
				/* is it an inlist */
				} elseif( $upper[0] == '(' && substr($upper,-1) == ')' ) {
					if($prev_token == 'IN') {
						$type = "in-list";
						$processed = $this->split_sql(substr($token,1,-1));
						$list = array();
						foreach($processed as $v) {
							if($v == ',') continue;
							$list[]=$v;
						}
						$processed = $list;
						unset($list);
						$prev_token = "";
						
	                               }
	                               elseif($prev_token == 'AGAINST') {
	                                       $type = "match-arguments";
	                                       $list = $this->split_sql(substr($token,1,-1));
	                                       if(count($list) > 1){
	                                               $match_mode = implode('',array_slice($list,1));
	                                               $processed = array($list[0], $match_mode);
	                                       }
	                                       else
	                                               $processed = $list[0];
	                                       $prev_token = "";
					}
	
				/* it is either an operator, a colref or a constant */
				} else {
					switch($upper) {
					case 'AND':
					case '&&':
					case 'BETWEEN':
					case 'AND':
					case 'BINARY':
					case '&':
					case '~':
					case '|':
					case '^':
					case 'CASE':
					case 'WHEN':
					case 'END':
					case 'DIV':
					case '/':
					case '<=>':
					case '=':
					case '>=':
					case '>':
					case 'IS':
					case 'NOT':
					case 'NULL':
					case '<<':
					case '<=':
					case '<':
					case 'LIKE':
					case '-':
					case '%':
					case '!=':
					case '<>':
					case 'REGEXP':
					case '!':
					case '||':
					case 'OR':
					case '+':
					case '>>':
					case 'RLIKE':
					case 'SOUNDS':
					case '*':
					case '-':
					case 'XOR':
					case 'IN':
							$processed = false;
							$type = "operator";
							break;
					default: 
						switch($token[0]) {
							case "'":
							case '"':
									$type = 'const';
									break;
							case '`':
									$type = 'colref';
									break;
								
							default:
								if(is_numeric($token)) {
									$type = 'const';
								} else {
									$type = 'colref';
								}
							break;
					
						}	
						#$processed = $token;
						$processed = false;
					}
				}
				/* is a reserved word? */
				if(($type != 'operator' && $type != 'in-list' && $type != 'sub_expr') && in_array($upper, $this->reserved)) {
					$token = $upper;
					if(!in_array($upper,$this->functions)) {
						$type = 'reserved';	
					} else {
						switch($token) {
							case 'AVG':
							case 'SUM':
							case 'COUNT':
							case 'MIN':
							case 'MAX':
							case 'STDDEV':
							case 'STDDEV_SAMP':
							case 'STDDEV_POP':
							case 'VARIANCE':
							case 'VAR_SAMP':
							case 'VAR_POP':
							case 'GROUP_CONCAT':
							case 'BIT_AND':
							case 'BIT_OR':
							case 'BIT_XOR':
								$type = 'aggregate_function';
								if(!empty($tokens[$key+1])) $sub_expr = $tokens[$key+1];
								#$skip_next=true;
							break;
	
							default:
								$type = 'function';
								if(!empty($tokens[$key+1])) $sub_expr = $tokens[$key+1]; else $sub_expr="()";
								#$skip_next=true;
	
					
							break;
						}
					}
				}
	
				if(!$type) {
					if($upper[0] == '(') {
						$local_expr = substr(trim($token),1,-1);
					} else {
						$local_expr = $token;
					}
	                                $processed = $this->process_expr_list($this->split_sql($local_expr));
					$type = 'expression';
					
					if(count($processed) == 1) {
						$type = $processed[0]['expr_type'];
						$base_expr  = $processed[0]['base_expr'];
						$processed = $processed[0]['sub_tree'];
					} 
	
				}
	
				$sub_expr=trim($sub_expr);
				$sub_expr = "";
	
				$expr[] = array( 'expr_type' => $type, 'base_expr' => $token, 'sub_tree' => $processed);
				$prev_token = $upper;
				$expr_type = "";
				$type = "";
			}
			if($sub_expr) {
				$processed['sub_tree'] = $this->process_expr_list($this->split_sql(substr($sub_expr,1,-1)));
			}
			
			if(!is_array($processed)) {
	
				print_r($processed);
				$processed = false;
			}
	
			if($expr_type) {
				$expr[] = array( 'expr_type' => $type, 'base_expr' => $token, 'sub_tree' => $processed);
			}
			$mod = false;
	
	/*
	
			for($i=0;$i<count($expr);++$i){
				if($expr[$i]['expr_type'] == 'function' ||
				   $expr[$i]['expr_type'] == 'aggregate_function') {
					if(!empty($expr[$i+1])) {
						$expr[$i]['sub_tree']=$expr[$i+1]['sub_tree'];
						unset($expr[$i+1]);
						$mod = 1;
						++$i;  // BAD FORM TO MODIFY THE LOOP COUNTER
					}
				} 
					
			}
	
	*/
	
			if($mod) $expr=array_values($expr);
	
	
			return $expr;
		} 
	
		private function process_update($tokens) {
	
		}
	
		private function process_delete($tokens) {
			$tables = array();
			$del = $tokens['DELETE'];
	
			foreach($tokens['DELETE'] as $expression) {
				if ($expression != 'DELETE' && trim($expression,' .*') != "" && $expression != ',') {
					$tables[] = trim($expression,'.* ');	
				}
			}
	
			if(empty($tables)) {
				foreach($tokens['FROM'] as $table) {
					$tables[] = $table['table'];
				}
			}
	
			$tokens['DELETE'] = array('TABLES' => $tables);
	
			return $tokens;
		}
	
		function process_insert($tokens, $token_category = 'INSERT') {
			$table = "";
			$cols = "";
			
			if(isset($tokens['INTO']))
			{
				$into = $tokens['INTO'];
				foreach($into as $token) {
					if(!trim($token)) continue;
					if(!$table) {
						$table = $token;
					}elseif(!$cols) {
						$cols = $token;
					}
				}
			}
	
			if(!$cols) {
				$cols = 'ALL';
			} else {
				$cols = explode(",", trim($cols,'() '));
			}
			unset($tokens['INTO']);
			$tokens[$token_category] =  array('table'=>$table, 'cols'=>$cols);
			return $tokens;
	
		}
		
	
		function load_reserved_words() {
		
		    $this->functions = array(
		        'abs',
		        'acos',
		        'adddate',
		        'addtime',
		        'aes_encrypt',
		        'aes_decrypt',
		        'against',
		        'ascii',
		        'asin',
		        'atan',
		        'avg',
		        'benchmark',
		        'bin',
		        'bit_and',
		        'bit_or',
		        'bitcount',
		        'bitlength',
		        'cast',
		        'ceiling',
		        'char',
		        'char_length',
		        'character_length',
		        'charset',
		        'coalesce',
		        'coercibility',
		        'collation',
		        'compress',
		        'concat',
		        'concat_ws',
		        'conection_id',
		        'conv',
		        'convert',
		        'convert_tz',
		        'cos',
		        'cot',
		        'count',
		        'crc32',
		        'curdate',
		        'current_user',
		        'currval',
		        'curtime',
		        'database',
		        'date_add',
		        'date_diff',
		        'date_format',
		        'date_sub',
		        'day',
		        'dayname',
		        'dayofmonth',
		        'dayofweek',
		        'dayofyear',
		        'decode',
		        'default',
		        'degrees',
		        'des_decrypt',
		        'des_encrypt',
		        'elt',
		        'encode',
		        'encrypt',
		        'exp',
		        'export_set',
		        'extract',
		        'field',
		        'find_in_set',
		        'floor',
		        'format',
		        'found_rows',
		        'from_days',
		        'from_unixtime',
		        'get_format',
		        'get_lock',
		        'group_concat',
		        'greatest',
		        'hex',
		        'hour',
		        'if',
		        'ifnull',
		        'in',
		        'inet_aton',
		        'inet_ntoa',
		        'insert',
		        'instr',
		        'interval',
		        'is_free_lock',
		        'is_used_lock',
		        'last_day',
		        'last_insert_id',
		        'lcase',
		        'least',
		        'left',
		        'length',
		        'ln',
		        'load_file',
		        'localtime',
		        'localtimestamp',
		        'locate',
		        'log',
		        'log2',
		        'log10',
		        'lower',
		        'lpad',
		        'ltrim',
		        'make_set',
		        'makedate',
		        'maketime',
		        'master_pos_wait',
		        'match',
		        'max',
		        'md5',
		        'microsecond',
		        'mid',
		        'min',
		        'minute',
		        'mod',
		        'month',
		        'monthname',
		        'nextval',
		        'now',
		        'nullif',
		        'oct',
		        'octet_length',
		        'old_password',
		        'ord',
		        'password',
		        'period_add',
		        'period_diff',
		        'pi',
		        'position',
		        'pow',
		        'power',
		        'quarter',
		        'quote',
		        'radians',
		        'rand',
		        'release_lock',
		        'repeat',
		        'replace',
		        'reverse',
		        'right',
		        'round',
		        'row_count',
		        'rpad',
		        'rtrim',
		        'sec_to_time',
		        'second',
		        'session_user',
		        'sha',
		        'sha1',
		        'sign',
		        'soundex',
		        'space',
		        'sqrt',
		        'std',
		        'stddev',
		        'stddev_pop',
		        'stddev_samp',
		        'strcmp',
		        'str_to_date',
		        'subdate',
		        'substring',
		        'substring_index',
		        'subtime',
		        'sum',
		        'sysdate',
		        'system_user',
		        'tan',
		        'time',
		        'timediff',
		        'timestamp',
		        'timestampadd',
		        'timestampdiff',
		        'time_format',
		        'time_to_sec',
		        'to_days',
		        'trim',
		        'truncate',
		        'ucase',
		        'uncompress',
		        'uncompressed_length',
		        'unhex',
		        'unix_timestamp',
		        'upper',
		        'user',
		        'utc_date',
		        'utc_time',
		        'utc_timestamp',
		        'uuid',
		        'var_pop',
		        'var_samp',
		        'variance',
		        'version',
		        'week',
		        'weekday',
		        'weekofyear',
		        'year',
		        'yearweek');
	
		/* includes functions */
		$this->reserved = array(
			'abs',
			'acos',
			'adddate',
			'addtime',
			'aes_encrypt',
			'aes_decrypt',
			'against',
			'ascii',
			'asin',
			'atan',
			'avg',
			'benchmark',
			'bin',
			'bit_and',
			'bit_or',
			'bitcount',
			'bitlength',
			'cast',
			'ceiling',
			'char',
			'char_length',
			'character_length',
			'charset',
			'coalesce',
			'coercibility',
			'collation',
			'compress',
			'concat',
			'concat_ws',
			'conection_id',
			'conv',
			'convert',
			'convert_tz',
			'cos',
			'cot',
			'count',
			'crc32',
			'curdate',
			'current_user',
			'currval',
			'curtime',
			'database',
			'date_add',
			'date_diff',
			'date_format',
			'date_sub',
			'day',
			'dayname',
			'dayofmonth',
			'dayofweek',
			'dayofyear',
			'decode',
			'default',
			'degrees',
			'des_decrypt',
			'des_encrypt',
			'elt',
			'encode',
			'encrypt',
			'exp',
			'export_set',
			'extract',
			'field',
			'find_in_set',
			'floor',
			'format',
			'found_rows',
			'from_days',
			'from_unixtime',
			'get_format',
			'get_lock',
			'group_concat',
			'greatest',
			'hex',
			'hour',
			'if',
			'ifnull',
			'in',
			'inet_aton',
			'inet_ntoa',
			'insert',
			'instr',
			'interval',
			'is_free_lock',
			'is_used_lock',
			'last_day',
			'last_insert_id',
			'lcase',
			'least',
			'left',
			'length',
			'ln',
			'load_file',
			'localtime',
			'localtimestamp',
			'locate',
			'log',
			'log2',
			'log10',
			'lower',
			'lpad',
			'ltrim',
			'make_set',
			'makedate',
			'maketime',
			'master_pos_wait',
			'match',
			'max',
			'md5',
			'microsecond',
			'mid',
			'min',
			'minute',
			'mod',
			'month',
			'monthname',
			'nextval',
			'now',
			'nullif',
			'oct',
			'octet_length',
			'old_password',
			'ord',
			'password',
			'period_add',
			'period_diff',
			'pi',
			'position',
			'pow',
			'power',
			'quarter',
			'quote',
			'radians',
			'rand',
			'release_lock',
			'repeat',
			'replace',
			'reverse',
			'right',
			'round',
			'row_count',
			'rpad',
			'rtrim',
			'sec_to_time',
			'second',
			'session_user',
			'sha',
			'sha1',
			'sign',
			'soundex',
			'space',
			'sqrt',
			'std',
			'stddev',
			'stddev_pop',
			'stddev_samp',
			'strcmp',
			'str_to_date',
			'subdate',
			'substring',
			'substring_index',
			'subtime',
			'sum',
			'sysdate',
			'system_user',
			'tan',
			'time',
			'timediff',
			'timestamp',
			'timestampadd',
			'timestampdiff',
			'time_format',
			'time_to_sec',
			'to_days',
			'trim',
			'truncate',
			'ucase',
			'uncompress',
			'uncompressed_length',
			'unhex',
			'unix_timestamp',
			'upper',
			'user',
			'utc_date',
			'utc_time',
			'utc_timestamp',
			'uuid',
			'var_pop',
			'var_samp',
			'variance',
			'version',
			'week',
			'weekday',
			'weekofyear',
			'year',
			'yearweek',
			'add',
			'all',
			'alter',
			'analyze',
			'and',
			'as',
			'asc',
			'asensitive',
			'auto_increment',
			'bdb',
			'before',
			'berkeleydb',
			'between',
			'bigint',
			'binary',
			'blob',
			'both',
			'by',
			'call',
			'cascade',
			'case',
			'change',
			'char',
			'character',
			'check',
			'collate',
			'column',
			'columns',
			'condition',
			'connection',
			'constraint',
			'continue',
			'create',
			'cross',
			'current_date',
			'current_time',
			'current_timestamp',
			'cursor',
			'database',
			'databases',
			'day_hour',
			'day_microsecond',
			'day_minute',
			'day_second',
			'dec',
			'decimal',
			'declare',
			'default',
			'delayed',
			'delete',
			'desc',
			'describe',
			'deterministic',
			'distinct',
			'distinctrow',
			'div',
			'double',
			'drop',
			'else',
			'elseif',
			'enclosed',
			'escaped',
			'exists',
			'exit',
			'explain',
			'false',
			'fetch',
			'fields',
			'float',
			'for',
			'force',
			'foreign',
			'found',
			'frac_second',
			'from',
			'fulltext',
			'grant',
			'group',
			'having',
			'high_priority',
			'hour_microsecond',
			'hour_minute',
			'hour_second',
			'if',
			'ignore',
			'in',
			'index',
			'infile',
			'inner',
			'innodb',
			'inout',
			'insensitive',
			'insert',
			'int',
			'integer',
			'interval',
			'into',
			'io_thread',
			'is',
			'iterate',
			'join',
			'key',
			'keys',
			'kill',
			'leading',
			'leave',
			'left',
			'like',
			'limit',
			'lines',
			'load',
			'localtime',
			'localtimestamp',
			'lock',
			'long',
			'longblob',
			'longtext',
			'loop',
			'low_priority',
			'master_server_id',
			'match',
			'mediumblob',
			'mediumint',
			'mediumtext',
			'middleint',
			'minute_microsecond',
			'minute_second',
			'mod',
			'natural',
			'not',
			'no_write_to_binlog',
			'null',
			'numeric',
			'on',
			'optimize',
			'option',
			'optionally',
			'or',
			'order',
			'out',
			'outer',
			'outfile',
			'precision',
			'primary',
			'privileges',
			'procedure',
			'purge',
			'read',
			'real',
			'references',
			'regexp',
			'rename',
			'repeat',
			'replace',
			'require',
			'restrict',
			'return',
			'revoke',
			'right',
			'rlike',
			'second_microsecond',
			'select',
			'sensitive',
			'separator',
			'set',
			'show',
			'smallint',
			'some',
			'soname',
			'spatial',
			'specific',
			'sql',
			'sqlexception',
			'sqlstate',
			'sqlwarning',
			'sql_big_result',
			'sql_calc_found_rows',
			'sql_small_result',
			'sql_tsi_day',
			'sql_tsi_frac_second',
			'sql_tsi_hour',
			'sql_tsi_minute',
			'sql_tsi_month',
			'sql_tsi_quarter',
			'sql_tsi_second',
			'sql_tsi_week',
			'sql_tsi_year',
			'ssl',
			'starting',
			'straight_join',
			'striped',
			'table',
			'tables',
			'terminated',
			'then',
			'timestampadd',
			'timestampdiff',
			'tinyblob',
			'tinyint',
			'tinytext',
			'to',
			'trailing',
			'true',
			'undo',
			'union',
			'unique',
			'unlock',
			'unsigned',
			'update',
			'usage',
			'use',
			'user_resources',
			'using',
			'utc_date',
			'utc_time',
			'utc_timestamp',
			'values',
			'varbinary',
			'varchar',
			'varcharacter',
			'varying',
			'when',
			'where',
			'while',
			'with',
			'write',
			'xor',
			'year_month',
			'zerofill'
			);
	
			for($i=0;$i<count($this->reserved);++$i) {
				$this->reserved[$i]=strtoupper($this->reserved[$i]);
				if(!empty($this->functions[$i])) $this->functions[$i] = strtoupper($this->functions[$i]);
			} 	
		}
	
	} // END CLASS
  	define('HAVE_PHP_SQL_PARSER',1);
} 

