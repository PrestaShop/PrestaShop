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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7521 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MediaCore
{
	public static $jquery_ui_dependencies = array(
		'ui.core' => array('fileName' => 'jquery.ui.core.min.js', 'dependencies' => array(), 'theme' => false),
		'ui.widget' => array('fileName' => 'jquery.ui.widget.min.js', 'dependencies' => array(), 'theme' => false),
		'ui.mouse' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => false),
		'ui.position' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array(), 'theme' => false),
		'ui.draggable' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => false),
		'ui.droppable' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('uicore', 'ui.widget', 'ui.mouse', 'ui.draggable'), 'theme' => false),
		'ui.resizable' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
		'ui.selectable' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
		'ui.sortable' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
		'ui.accordion' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
		'ui.autocomplete' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position'), 'theme' => true),
		'ui.button' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
		'ui.dialog' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position'), 'theme' => true),
		'ui.slider' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
		'ui.tabs' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
		'ui.datepicker' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core'), 'theme' => true),
		'ui.progressbar' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
		'effects.core' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array(), 'theme' => false),
		'effects.blind' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.bounce' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.clip' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.drop' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.explode' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.fade' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.fold' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.highlight' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.pulsate' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.scale' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.shake' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.slide' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
		'effects.transfer' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('effects.core'), 'theme' => false)
	);

	public static function minifyHTML($html_content)
	{
		if (strlen($html_content) > 0)
		{
			//set an alphabetical order for args
			$html_content = preg_replace_callback(
				'/(<[a-zA-Z0-9]+)((\s?[a-zA-Z0-9]+=[\"\\\'][^\"\\\']*[\"\\\']\s?)*)>/',
				array('Media', 'minifyHTMLpregCallback'),
				$html_content);

			require_once(_PS_TOOL_DIR_.'minify_html/minify_html.class.php');
			$html_content = str_replace(chr(194).chr(160), '&nbsp;', $html_content);
			$html_content = Minify_HTML::minify($html_content, array('xhtml', 'cssMinifier', 'jsMinifier'));

			if (Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION'))
			{
				//$html_content = preg_replace('/"([^\>\s"]*)"/i', '$1', $html_content);//FIXME create a js bug
				$html_content = preg_replace('/<!DOCTYPE \w[^\>]*dtd\">/is', '', $html_content);
				$html_content = preg_replace('/\s\>/is', '>', $html_content);
				$html_content = str_replace('</li>', '', $html_content);
				$html_content = str_replace('</dt>', '', $html_content);
				$html_content = str_replace('</dd>', '', $html_content);
				$html_content = str_replace('</head>', '', $html_content);
				$html_content = str_replace('<head>', '', $html_content);
				$html_content = str_replace('</html>', '', $html_content);
				$html_content = str_replace('</body>', '', $html_content);
				//$html_content = str_replace('</p>', '', $html_content);//FIXME doesnt work...
				$html_content = str_replace("</option>\n", '', $html_content);//TODO with bellow
				$html_content = str_replace('</option>', '', $html_content);
				$html_content = str_replace('<script type=text/javascript>', '<script>', $html_content);//Do a better expreg
				$html_content = str_replace("<script>\n", '<script>', $html_content);//Do a better expreg
			}

			return $html_content;
		}
		return false;
	}

	public static function minifyHTMLpregCallback($preg_matches)
	{
		$args = array();
		preg_match_all('/[a-zA-Z0-9]+=[\"\\\'][^\"\\\']*[\"\\\']/is', $preg_matches[2], $args);
		$args = $args[0];
		sort($args);
		// if there is no args in the balise, we don't write a space (avoid previous : <title >, now : <title>)
		if (empty($args))
			$output = $preg_matches[1].'>';
		else
			$output = $preg_matches[1].' '.implode(' ', $args).'>';
		return $output;
	}

	public static function packJSinHTML($html_content)
	{
		if (strlen($html_content) > 0)
		{
			$html_content_copy = $html_content;
			$html_content = preg_replace_callback(
				'/\\s*(<script\\b[^>]*?>)([\\s\\S]*?)(<\\/script>)\\s*/i',
				array('Media', 'packJSinHTMLpregCallback'),
				$html_content);

			// If the string is too big preg_replace return an error
			// In this case, we don't compress the content
			if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR)
			{
				error_log('ERROR: PREG_BACKTRACK_LIMIT_ERROR in function packJSinHTML');
				return $html_content_copy;
			}
			return $html_content;
		}
		return false;
	}

	public static function packJSinHTMLpregCallback($preg_matches)
	{
		$preg_matches[1] = $preg_matches[1].'/* <![CDATA[ */';
		$preg_matches[2] = Media::packJS($preg_matches[2]);
		$preg_matches[count($preg_matches) - 1] = '/* ]]> */'.$preg_matches[count($preg_matches) - 1];
		unset($preg_matches[0]);
		$output = implode('', $preg_matches);
		return $output;
	}


	public static function packJS($js_content)
	{
		if (strlen($js_content) > 0)
		{
			require_once(_PS_TOOL_DIR_.'js_minify/jsmin.php');
			return JSMin::minify($js_content);
		}
		return false;
	}

	public static function minifyCSS($css_content, $fileuri = false, &$import_url = array())
	{
		global $current_css_file;

		$current_css_file = $fileuri;
		if (strlen($css_content) > 0)
		{
			$css_content = preg_replace('#/\*.*?\*/#s', '', $css_content);
			$css_content = preg_replace_callback('#url\((?:\'|")?([^\)\'"]*)(?:\'|")?\)#s', array('Tools', 'replaceByAbsoluteURL'), $css_content);

			$css_content = preg_replace('#\s+#', ' ', $css_content);
			$css_content = str_replace("\t", '', $css_content);
			$css_content = str_replace("\n", '', $css_content);
			//$css_content = str_replace('}', "}\n", $css_content);

			$css_content = str_replace('; ', ';', $css_content);
			$css_content = str_replace(': ', ':', $css_content);
			$css_content = str_replace(' {', '{', $css_content);
			$css_content = str_replace('{ ', '{', $css_content);
			$css_content = str_replace(', ', ',', $css_content);
			$css_content = str_replace('} ', '}', $css_content);
			$css_content = str_replace(' }', '}', $css_content);
			$css_content = str_replace(';}', '}', $css_content);
			$css_content = str_replace(':0px', ':0', $css_content);
			$css_content = str_replace(' 0px', ' 0', $css_content);
			$css_content = str_replace(':0em', ':0', $css_content);
			$css_content = str_replace(' 0em', ' 0', $css_content);
			$css_content = str_replace(':0pt', ':0', $css_content);
			$css_content = str_replace(' 0pt', ' 0', $css_content);
			$css_content = str_replace(':0%', ':0', $css_content);
			$css_content = str_replace(' 0%', ' 0', $css_content);

			// Store all import url
			preg_match_all('#@import .*?;#i', $css_content, $m);
			for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			{
				$import_url[] = $m[0][$i];
				$css_content = str_replace($m[0][$i], '', $css_content);
			}

			return trim($css_content);
		}
		return false;
	}

	/**
	 * addJS return javascript path
	 *
	 * @param mixed $js_uri
	 * @return string
	 */
	public static function getJSPath($js_uri)
	{
		if (is_array($js_uri) || $js_uri === null || empty($js_uri))
			return false;
		$url_data = parse_url($js_uri);
		if (!array_key_exists('host', $url_data))
			$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);		// remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
		else
			$file_uri = $js_uri;
		// check if js files exists

		if (!preg_match('/^http(s?):\/\//i', $file_uri) && !@filemtime($file_uri))
			return false;

		if (Context::getContext()->controller->controller_type == 'admin')
		{
			$js_uri = preg_replace('/^'.preg_quote(__PS_BASE_URI__, '/').'/', '/', $js_uri);
			$js_uri = dirname(preg_replace('/\?.+$/', '', $_SERVER['REQUEST_URI']).'a').'/..'.$js_uri;
		}
		
		return $js_uri;
	}

	/**
	 * addCSS return stylesheet path.
	 *
	 * @param mixed $css_uri
	 * @param string $css_media_type
	 * @return string
	 */
	public static function getCSSPath($css_uri, $css_media_type = 'all')
	{
		if (empty($css_uri))
			return false;
		// remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
		$url_data = parse_url($css_uri);
		$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
		// check if css files exists
		if (!@filemtime($file_uri))
			return false;

		if (Context::getContext()->controller->controller_type == 'admin')
		{
			$css_uri = preg_replace('/^'.preg_quote(__PS_BASE_URI__, '/').'/', '/', $css_uri);
			$css_uri = dirname(preg_replace('/\?.+$/', '', $_SERVER['REQUEST_URI']).'a').'/..'.$css_uri;
		}

		return array($css_uri => $css_media_type);
	}

	/**
	 * return jquery path.
	 *
	 * @param mixed $version
	 * @return string
	 */
	public static function getJqueryPath($version = null, $folder = null, $minifier = true)
	{
		$add_no_conflict = false;
		if ($version === null)
			$version = _PS_JQUERY_VERSION_; //set default version
		else if (preg_match('/^([0-9]+\.)+[0-9]$/Ui', $version))
			$add_no_conflict = true;
		else
			return false;

		if ($folder === null)
			$folder = _PS_JS_DIR_.'jquery/'; //set default folder
		//check if file exist
		$file = $folder.'jquery-'.$version.($minifier ? '.min.js' : '.js');

		// remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
		$url_data = parse_url($file);
		$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
		// check if js files exists, if not try to load query from ajax.googleapis.com

		$return = array();
		if (@filemtime($file_uri))
			$return[] = Media::getJSPath($file);
		else
			$return[] = Media::getJSPath(Tools::getCurrentUrlProtocolPrefix().'ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery'.($minifier ? '.min.js' : '.js'));

		if ($add_no_conflict)
			$return[] = Media::getJSPath(_PS_JS_DIR_.'jquery/jquery.noConflict.php?version='.$version);

		return $return;
	}

	/**
	 * return jqueryUI component path.
	 *
	 * @param mixed $component
	 * @return string
	 */
	public static function getJqueryUIPath($component, $theme, $check_dependencies)
	{
		$ui_path = array('js' => array(), 'css' => array());
		$folder = _PS_JS_DIR_.'jquery/ui/';
		$file = 'jquery.'.$component.'.min.js';
		$url_data = parse_url($folder.$file);
		$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
		$ui_tmp = array();
		if ($check_dependencies && array_key_exists($component, self::$jquery_ui_dependencies))
			foreach (self::$jquery_ui_dependencies[$component]['dependencies'] as $dependency)
				$ui_tmp[] = Media::getJqueryUIPath($dependency, $theme, false);

		if (self::$jquery_ui_dependencies[$component]['theme'] && $check_dependencies)
		{
			$theme_css = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.ui.theme.css');
			$comp_css = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.'.$component.'.css');
			if (!empty($theme_css) || $theme_css)
				$ui_path['css'] = array_merge($ui_path['css'], $theme_css);
			if (!empty($comp_css) || $comp_css)
				$ui_path['css'] = array_merge($ui_path['css'], $comp_css);
		}

		if (@filemtime($file_uri))
		{
			if (!empty($ui_tmp))
			{
				foreach ($ui_tmp as $ui)
				{
					$ui_path['js'][] = $ui['js'];
					$ui_path['css'][] = $ui['css'];
				}
				$ui_path['js'][] = Media::getJSPath($folder.$file);
			}
			else
				$ui_path['js'] = Media::getJSPath($folder.$file);
		}

		//add i18n file for datepicker
		if ($component == 'ui.datepicker')
			$ui_path['js'][] = Media::getJSPath($folder.'i18n/jquery.ui.datepicker-'.Context::getContext()->language->iso_code.'.js');

		return $ui_path;
	}

	/**
	 * return jquery plugin path.
	 *
	 * @param mixed $name
	 * @return void
	 */
	public static function getJqueryPluginPath($name, $folder)
	{
		$plugin_path = array('js' => array(), 'css' => array());
		if ($folder === null)
			$folder = _PS_JS_DIR_.'jquery/plugins/'; //set default folder

		$file = 'jquery.'.$name.'.js';
		$url_data = parse_url($folder);
		$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
		if (@filemtime($file_uri.$file))
			$plugin_path['js'] = Media::getJSPath($folder.$file);
		elseif (@filemtime($file_uri.$name.'/'.$file))
			$plugin_path['js'] = Media::getJSPath($folder.$name.'/'.$file);
		else
			return false;
		$plugin_path['css'] = Media::getJqueryPluginCSSPath($name);
		return $plugin_path;
	}

	/**
	 * return jquery plugin css path if exist.
	 *
	 * @param mixed $name
	 * @return void
	 */
	public static function getJqueryPluginCSSPath($name)
	{
		$folder = _PS_JS_DIR_.'jquery/plugins/';
		$file = 'jquery.'.$name.'.css';
		$url_data = parse_url($folder);
		$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
		if (@filemtime($file_uri.$file))
			return Media::getCSSPath($folder.$file);
		elseif (@filemtime($file_uri.$name.'/'.$file))
			return Media::getCSSPath($folder.$name.'/'.$file);
		else
			return false;
	}

	/**
	* Combine Compress and Cache CSS (ccc) calls
	*
	* @param array css_files
	* @return array processed css_files
	*/
	public static function cccCss($css_files)
	{
		//inits
		$css_files_by_media = array();
		$compressed_css_files = array();
		$compressed_css_files_not_found = array();
		$compressed_css_files_infos = array();
		$protocol_link = Tools::getCurrentUrlProtocolPrefix();

		// group css files by media
		foreach ($css_files as $filename => $media)
		{
			if (!array_key_exists($media, $css_files_by_media))
				$css_files_by_media[$media] = array();

			$infos = array();
			$infos['uri'] = $filename;
			$url_data = parse_url($filename);
			$infos['path'] = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);
			$css_files_by_media[$media]['files'][] = $infos;
			if (!array_key_exists('date', $css_files_by_media[$media]))
				$css_files_by_media[$media]['date'] = 0;
			$css_files_by_media[$media]['date'] = max(
				file_exists($infos['path']) ? filemtime($infos['path']) : 0,
				$css_files_by_media[$media]['date']
			);

			if (!array_key_exists($media, $compressed_css_files_infos))
				$compressed_css_files_infos[$media] = array('key' => '');
			$compressed_css_files_infos[$media]['key'] .= $filename;
		}

		// get compressed css file infos
		foreach ($compressed_css_files_infos as $media => &$info)
		{
			$key = md5($info['key'].$protocol_link);
			$filename = _PS_THEME_DIR_.'cache/'.$key.'_'.$media.'.css';
			$info = array(
				'key' => $key,
				'date' => file_exists($filename) ? filemtime($filename) : 0
			);
		}

		// aggregate and compress css files content, write new caches files
		$import_url = array();
		foreach ($css_files_by_media as $media => $media_infos)
		{
			$cache_filename = _PS_THEME_DIR_.'cache/'.$compressed_css_files_infos[$media]['key'].'_'.$media.'.css';
			if ($media_infos['date'] > $compressed_css_files_infos[$media]['date'])
			{
				$compressed_css_files[$media] = '';
				foreach ($media_infos['files'] as $file_infos)
				{
					if (file_exists($file_infos['path']))
						$compressed_css_files[$media] .= Media::minifyCSS(file_get_contents($file_infos['path']), $file_infos['uri'], $import_url);
					else
						$compressed_css_files_not_found[] = $file_infos['path'];
				}
				if (!empty($compressed_css_files_not_found))
					$content = '/* WARNING ! file(s) not found : "'.
						implode(',', $compressed_css_files_not_found).
						'" */'."\n".$compressed_css_files[$media];
				else
					$content = $compressed_css_files[$media];

				$content = implode('', $import_url).$content;
				file_put_contents($cache_filename, $content);
				chmod($cache_filename, 0777);
			}
			$compressed_css_files[$media] = $cache_filename;
		}

		// rebuild the original css_files array
		$css_files = array();
		foreach ($compressed_css_files as $media => $filename)
		{
			$url = str_replace(_PS_THEME_DIR_, _THEMES_DIR_._THEME_NAME_.'/', $filename);
			$css_files[$protocol_link.Tools::getMediaServer($url).$url] = $media;
		}
		return $css_files;
	}


	/**
	* Combine Compress and Cache (ccc) JS calls
	*
	* @param array js_files
	* @return array processed js_files
	*/
	public static function cccJS($js_files)
	{
		//inits
		$compressed_js_files_not_found = array();
		$js_files_infos = array();
		$js_files_date = 0;
		$compressed_js_filename = '';
		$js_external_files = array();
		$protocol_link = Tools::getCurrentUrlProtocolPrefix();

		// get js files infos
		foreach ($js_files as $filename)
		{
			$expr = explode(':', $filename);

			if ($expr[0] == 'http')
				$js_external_files[] = $filename;
			else
			{
				$infos = array();
				$infos['uri'] = $filename;
				$url_data = parse_url($filename);
				$infos['path'] = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);
				$js_files_infos[] = $infos;

				$js_files_date = max(
					file_exists($infos['path']) ? filemtime($infos['path']) : 0,
					$js_files_date
				);
				$compressed_js_filename .= $filename;
			}
		}

		// get compressed js file infos
		$compressed_js_filename = md5($compressed_js_filename);

		$compressed_js_path = _PS_THEME_DIR_.'cache/'.$compressed_js_filename.'.js';
		$compressed_js_file_date = file_exists($compressed_js_path) ? filemtime($compressed_js_path) : 0;

		// aggregate and compress js files content, write new caches files
		if ($js_files_date > $compressed_js_file_date)
		{
			$content = '';
			foreach ($js_files_infos as $file_infos)
			{
				if (file_exists($file_infos['path']))
					$content .= file_get_contents($file_infos['path']).';';
				else
					$compressed_js_files_not_found[] = $file_infos['path'];
			}
			$content = Media::packJS($content);

			if (!empty($compressed_js_files_not_found))
				$content = '/* WARNING ! file(s) not found : "'.
					implode(',', $compressed_js_files_not_found).
					'" */'."\n".$content;

			file_put_contents($compressed_js_path, $content);
			chmod($compressed_js_path, 0777);
		}

		// rebuild the original js_files array
		$url = str_replace(_PS_ROOT_DIR_.'/', __PS_BASE_URI__, $compressed_js_path);

		return array_merge(array($protocol_link.Tools::getMediaServer($url).$url), $js_external_files);
	}

}