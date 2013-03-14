<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PhpClosure
{
	private $_js_files = false;
	private $_output_format			= 'json';
	private $_output_info			= 'compiled_code';
	private $_optimization_level	= 'SIMPLE_OPTIMIZATIONS';
	private $compiler_uri			= 'http://closure-compiler.appspot.com/compile';

	public function __construct($js_files = array())
	{
		$this->_js_files = $js_files;
	}
	
	public function getCompiledCode()
	{
		$data = $this->getData();

		$options = array(
			'http'=>array(
				'method' => "POST",
				'header' => 
					"Content-type: application/x-www-form-urlencoded\r\n".
					"Content-length: ". strlen($data) ."\r\n",
				'content' => $data,
			)
		);
		
		$context = stream_context_create($options);
		$json_response = file_get_contents($this->_compiler_uri, null, $context);
		$response = json_decode($json_response);
		
		if (isset($response->compiledCode))
			return $response->compiledCode;
		elseif (isset($response->serverErrors))
		{
			$server_errors = array_pop($response->serverErrors);
			throw new Exception($server_errors->error);
		}
	}
	
	protected function getData()
	{		
		$params = array(
			'compilation_level'	=> $this->_optimization_level,
			'output_format'		=> $this->_output_format,
			'output_info'		=> $this->_output_info,
		);
		
		$index = 0;
		foreach ($this->_js_files as $js_file)
			$params['code_url_'.$index++] = _PS_BASE_URL_.$js_file['uri'];
		
		foreach ($params as $key => $value)
			$data[] = preg_replace('/_[0-9]*$/', '', $key).'='.urlencode($value);

		return implode('&', $data);
	}
	
	
}
