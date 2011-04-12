<?php
	
	
	class fianet_socket
	{		
		var $host;
		var $port;
		
		var $is_ssl = false;
		
		var $method = 'GET';
		
		var $data = array();
		var $path;
		
		function fianet_socket($fianet_url, $fianet_file, $method = 'GET', $data = array())
		{
			if (is_array($data))
			{
				$this->data = $data;
			}
			$this->parse_fianet_url($fianet_url, $fianet_file);
			if (strtoupper($method) == 'GET' || strtoupper($method) == 'POST')
			{
				$this->method = strtoupper($method);
			}
		}
		
		function parse_fianet_url($fianet_url, $fianet_file)
		{
			if (ereg("^http://", $fianet_url))
			{
				$this->is_ssl = false;
				$this->host = eregi_replace("http://([^/]+)/.+", "\\1", $fianet_url);
				$this->port = 80;
				$this->path = eregi_replace("http://[^/]+/", "/", $fianet_url) . $fianet_file;
			}
			if (ereg("^https://", $fianet_url))
			{
				$this->is_ssl = true;
				$this->host = eregi_replace("https://([^/]+)/.+", "\\1", $fianet_url);
				$this->port = 443;
				$this->path = eregi_replace("https://[^/]+/", "/", $fianet_url) . $fianet_file;
			}
			//debug($this->path);
		}
		
		function determine_boundary()
		{
			srand((double)microtime() * 1000000);
			return ("---------------------".substr(md5(rand(0,32000)),0,10));
		}
		
		function build_header($boundary, $data)
		{
			if ($this->method == 'POST')
			{
				$header = "POST ".$this->path." HTTP/1.0\r\n";
				$header .= "Host: ".$this->host."\r\n";
				$header .= "Content-type: application/x-www-form-urlencoded; boundary=$boundary\r\n";
				$header .= "Content-length: " . strlen($data) . "\r\n\r\n";
			}
			elseif ($this->method == 'GET')
			{
				if ($data != '')
				{
					if (strlen($this->path."?".$data) > 2048)
					{
						fianet_insert_log("fianet_socket.php - build_header() <br />\nMaximum length in get method reached(".strlen($this->path."?".$data).") : <br />\n".$this->path."?".$data);
					}
					$header = "GET ".$this->path."?".$data." HTTP/1.1\r\n";
				}
				else
				{
					$header = "GET ".$this->path." HTTP/1.1\r\n";
				}
				$header .= "Host: ".$this->host."\r\n";
				$header .= "Connection: close\r\n\r\n";
			}
			return ($header);
		}
		
		function build_data($boundary)
		{
			$data = "";
			foreach($this->data as $index => $value)
			{
				if ($data == '')
				{
					$data .= $index . '='. urlencode($value);
				}
				else
				{
					$data .= '&'.$index . '='. urlencode($value);
				}
			}
			return ($data);
		}
		
		function send()
		{
			$boundary = $this->determine_boundary();
			$data = $this->build_data($boundary);
			$header = $this->build_header($boundary, $data);

			//debug($header, 'header');
			//debug(htmlentities($data), 'data');
			$response = $this->connect($header, $data);
			if ($response != false)
			{
				$d['header'] = $response['header'];
				$d['data'] = htmlentities($response['data']);
				//debug($d, 'Réponse');
			}
			
			return ($response);
		}
		
		function connect($header, $data)
		{
			$res['header'] = "";
			$res['data'] = "";
			if ($this->is_ssl)
			{
				$host = 'ssl://'.$this->host . ':'.$this->port;
				$socket = fsockopen ('ssl://'.$this->host, $this->port);
			}
			else
			{
				$host = $this->host . ':' . $this->port;
				$socket = fsockopen ($this->host, $this->port);
			}
			if ($socket !== false)
			{
				$data_reached = false;
				if (@fputs($socket, $header.$data))
				{
					while (!feof($socket))
					{
						$line = fgets($socket);
						if (!$data_reached)
						{
							if ($line == "\r\n")
							{
								$data_reached = true;
							}
							else
							{
								$res['header'] .= $line;
							}
						}
						else
						{
							$res['data'] .= $line;
						}
					}
				}
				else
				{
					fianet_insert_log("fianet_socket.php - connect() <br />\nEnvoie des données impossible sur : <br />\n".$host);
					$res = false;
				}
			    fclose($socket);
			}
			else
			{
				fianet_insert_log("fianet_socket.php - connect() <br />\nConnexion socket impossible : <br />\n".$host);
				$res = false;
			}
			return ($res);
		}
	}
