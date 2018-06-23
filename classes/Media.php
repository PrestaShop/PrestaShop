<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class MediaCore
 */
class MediaCore
{
    public static $jquery_ui_dependencies = array(
        'ui.core' => array('fileName' => 'jquery.ui.core.min.js', 'dependencies' => array(), 'theme' => true),
        'ui.widget' => array('fileName' => 'jquery.ui.widget.min.js', 'dependencies' => array(), 'theme' => false),
        'ui.mouse' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => false),
        'ui.position' => array('fileName' => 'jquery.ui.position.min.js', 'dependencies' => array(), 'theme' => false),
        'ui.draggable' => array('fileName' => 'jquery.ui.draggable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => false),
        'ui.droppable' => array('fileName' => 'jquery.ui.droppable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse', 'ui.draggable'), 'theme' => false),
        'ui.resizable' => array('fileName' => 'jquery.ui.resizable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.selectable' => array('fileName' => 'jquery.ui.selectable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.sortable' => array('fileName' => 'jquery.ui.sortable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.autocomplete' => array('fileName' => 'jquery.ui.autocomplete.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position', 'ui.menu'), 'theme' => true),
        'ui.button' => array('fileName' => 'jquery.ui.button.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
        'ui.dialog' => array('fileName' => 'jquery.ui.dialog.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position','ui.button'), 'theme' => true),
        'ui.menu' => array('fileName' => 'jquery.ui.menu.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position'), 'theme' => true),
        'ui.slider' => array('fileName' => 'jquery.ui.slider.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.spinner' => array('fileName' => 'jquery.ui.spinner.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.button'), 'theme' => true),
        'ui.tabs' => array('fileName' => 'jquery.ui.tabs.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
        'ui.datepicker' => array('fileName' => 'jquery.ui.datepicker.min.js', 'dependencies' => array('ui.core'), 'theme' => true),
        'ui.progressbar' => array('fileName' => 'jquery.ui.progressbar.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
        'ui.tooltip' => array('fileName' => 'jquery.ui.tooltip.min.js', 'dependencies' => array('ui.core', 'ui.widget','ui.position','effects.core'), 'theme' => true),
        'ui.accordion' => array('fileName' => 'jquery.ui.accordion.min.js', 'dependencies' => array('ui.core', 'ui.widget','effects.core'), 'theme' => true),
        'effects.core' => array('fileName' => 'jquery.effects.core.min.js', 'dependencies' => array(), 'theme' => false),
        'effects.blind' => array('fileName' => 'jquery.effects.blind.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.bounce' => array('fileName' => 'jquery.effects.bounce.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.clip' => array('fileName' => 'jquery.effects.clip.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.drop' => array('fileName' => 'jquery.effects.drop.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.explode' => array('fileName' => 'jquery.effects.explode.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.fade' => array('fileName' => 'jquery.effects.fade.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.fold' => array('fileName' => 'jquery.effects.fold.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.highlight' => array('fileName' => 'jquery.effects.highlight.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.pulsate' => array('fileName' => 'jquery.effects.pulsate.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.scale' => array('fileName' => 'jquery.effects.scale.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.shake' => array('fileName' => 'jquery.effects.shake.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.slide' => array('fileName' => 'jquery.effects.slide.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.transfer' => array('fileName' => 'jquery.effects.transfer.min.js', 'dependencies' => array('effects.core'), 'theme' => false)
    );

    private static $jquery_ui_datepicker_iso_code = array(
        'bn' => 'en',
        'bz' => 'en',
        'dh' => 'de',
        'gb' => 'en-GB',
        'ag' => 'es',
        'cb' => 'es',
        'mx' => 'es',
        'pe' => 'es',
        've' => 'es',
        'qc' => 'fr-CA',
        'ga' => 'en',
        'lo' => 'en',
        'br' => 'pt-BR',
        'sh' => 'en',
        'si' => 'sl',
        'ug' => 'en',
        'ur' => 'en',
        'vn' => 'vi',
        'zh' => 'zh-CN',
        'tw' => 'zh-TW',
    );

    /**
     * @var array list of javascript definitions
     */
    protected static $js_def = array();

    /**
     * @var array list of javascript inline scripts
     */
    protected static $inline_script = array();

    /**
     * @var array list of javascript external scripts
     */
    protected static $inline_script_src = array();

    /**
     * @var string pattern used in replaceByAbsoluteURL
     */
    public static $pattern_callback = '#(url\((?![\'"]?(?:data:|//|https?:))(?:\'|")?)([^\)\'"]*)(?=[\'"]?\))#s';

    /**
     * @var string used for preg_replace_callback parameter (avoid global)
     */
    protected static $current_css_file;

    /**
     * @var string pattern used in packJSinHTML
     */
    public static $pattern_js = '/(<\s*script(?:\s+[^>]*(?:javascript|src)[^>]*)?\s*>)(.*)(<\s*\/script\s*[^>]*>)/Uims';

    protected static $pattern_keepinline = 'data-keepinline';

    /**
     * Minify JS
     *
     * @param string $jsContent
     *
     * @return string
     */
    public static function packJS($jsContent)
    {
        if (!empty($jsContent)) {
            try {
                $jsContent = JSMin::minify($jsContent);
            } catch (Exception $e) {
                if (_PS_MODE_DEV_) {
                    echo $e->getMessage();
                }

                return ';'.trim($jsContent, ';').';';
            }
        }

        return ';'.trim($jsContent, ';').';';
    }

    /**
     * Minify CSS
     *
     * @param string $cssContent
     * @param bool   $fileUri
     * @param array  $importUrl
     *
     * @return bool|string
     */
    public static function minifyCSS($cssContent, $fileUri = false, &$importUrl = array())
    {
        Media::$current_css_file = $fileUri;

        if (strlen($cssContent) > 0) {
            $cssContent = Minify_CSSmin::minify($cssContent);
            $limit  = Media::getBackTrackLimit();
            $cssContent = preg_replace_callback(Media::$pattern_callback, array('Media', 'replaceByAbsoluteURL'), $cssContent, $limit);
            $cssContent = str_replace('\'images_ie/', '\'images/', $cssContent);
            $cssContent = preg_replace_callback('#(AlphaImageLoader\(src=\')([^\']*\',)#s', array('Tools', 'replaceByAbsoluteURL'), $cssContent);

            // Store all import url
            preg_match_all('#@(import|charset) .*?;#i', $cssContent, $m);
            for ($i = 0, $total = count($m[0]); $i < $total; $i++) {
                if (isset($m[1][$i]) && $m[1][$i] == 'import') {
                    $importUrl[] = $m[0][$i];
                }
                $cssContent = str_replace($m[0][$i], '', $cssContent);
            }

            return trim($cssContent);
        }

        return false;
    }

    /**
     * Replace URL by absolute URL
     *
     * @param array $matches
     *
     * @return bool|string
     */
    public static function replaceByAbsoluteURL($matches)
    {
        if (array_key_exists(1, $matches) && array_key_exists(2, $matches)) {
            if (!preg_match('/^(?:https?:)?\/\//iUs', $matches[2])) {
                $protocolLink = Tools::getCurrentUrlProtocolPrefix();
                $sep = '/';
                $tmp = $matches[2][0] == $sep ? $matches[2] : dirname(Media::$current_css_file).$sep.ltrim($matches[2], $sep);
                $server = Tools::getMediaServer($tmp);

                return $matches[1].$protocolLink.$server.$tmp;
            } else {
                return $matches[0];
            }
        }

        return false;
    }

    /**
     * addJS return javascript path
     *
     * @param mixed $jsUri
     *
     * @return string
     */
    public static function getJSPath($jsUri)
    {
        return Media::getMediaPath($jsUri);
    }

    /**
     * addCSS return stylesheet path.
     *
     * @param mixed  $cssUri
     * @param string $cssMediaType
     * @param bool   $needRtl
     *
     * @return string
     */
    public static function getCSSPath($cssUri, $cssMediaType = 'all', $needRtl = true)
    {
        // RTL Ready: search and load rtl css file if it's not originally rtl
        if ($needRtl && Context::getContext()->language->is_rtl) {
            $cssUriRtl = preg_replace('/(^[^.].*)(\.css)$/', '$1_rtl.css', $cssUri);
            $rtlMedia = Media::getMediaPath($cssUriRtl, $cssMediaType);
            if ($rtlMedia != false) {
                return $rtlMedia;
            }
        }
        // End RTL
        return Media::getMediaPath($cssUri, $cssMediaType);
    }

    /**
     * Get Media path
     *
     * @param  string $mediaUri
     * @param null    $cssMediaType
     *
     * @return array|bool|mixed|string
     */
    public static function getMediaPath($mediaUri, $cssMediaType = null)
    {
        if (is_array($mediaUri) || $mediaUri === null || empty($mediaUri)) {
            return false;
        }

        $urlData = parse_url($mediaUri);
        if (!is_array($urlData)) {
            return false;
        }

        if (!array_key_exists('host', $urlData)) {
            $mediaUriHostMode = '/'.ltrim(str_replace(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, _PS_CORE_DIR_), __PS_BASE_URI__, $mediaUri), '/\\');
            $mediaUri = '/'.ltrim(str_replace(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, _PS_ROOT_DIR_), __PS_BASE_URI__, $mediaUri), '/\\');
            // remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
            $fileUri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $mediaUri);
            $fileUriHostMode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, Tools::str_replace_once(_PS_CORE_DIR_, '', $mediaUri));

            if (!@filemtime($fileUri) || @filesize($fileUri) === 0) {
                if (!defined('_PS_HOST_MODE_')) {
                    return false;
                } elseif (!@filemtime($fileUriHostMode) || @filesize($fileUriHostMode) === 0) {
                    return false;
                } else {
                    $mediaUri = $mediaUriHostMode;
                }
            }

            $mediaUri = str_replace('//', '/', $mediaUri);
        }

        if ($cssMediaType) {
            return array($mediaUri => $cssMediaType);
        }

        return $mediaUri;
    }

    /**
     * return jquery path.
     *
     * @param mixed $version
     *
     * @return string
     */
    public static function getJqueryPath($version = null, $folder = null, $minifier = true)
    {
        $addNoConflict = false;
        if ($version === null) {
            $version = _PS_JQUERY_VERSION_;
        } //set default version
        elseif (preg_match('/^([0-9\.]+)$/Ui', $version)) {
            $addNoConflict = true;
        } else {
            return false;
        }

        if ($folder === null) {
            $folder = _PS_JS_DIR_.'jquery/';
        } //set default folder
        //check if file exist
        $file = $folder.'jquery-'.$version.($minifier ? '.min.js' : '.js');

        // remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
        $urlData = parse_url($file);
        $fileUri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
        $fileUriHostMode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
        // check if js files exists, if not try to load query from ajax.googleapis.com

        $return = array();

        if (@filemtime($fileUri) || (defined('_PS_HOST_MODE_') && @filemtime($fileUriHostMode))) {
            $return[] = Media::getJSPath($file);
        } else {
            $return[] = Media::getJSPath(Tools::getCurrentUrlProtocolPrefix().'ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery'.($minifier ? '.min.js' : '.js'));
        }

        if ($addNoConflict) {
            $return[] = Media::getJSPath(Context::getContext()->shop->getBaseURL(true, false)._PS_JS_DIR_.'jquery/jquery.noConflict.php?version='.$version);
        }

        // added jQuery migrate for compatibility with new version of jQuery
        // will be removed when using latest version of jQuery
        $return[] = Media::getJSPath(_PS_JS_DIR_.'jquery/jquery-migrate-1.2.1.min.js');

        return $return;
    }

    /**
     * return jqueryUI component path.
     *
     * @param mixed $component
     *
     * @return string
     */
    public static function getJqueryUIPath($component, $theme, $checkDependencies)
    {
        $uiPath = array('js' => array(), 'css' => array());
        $folder = _PS_JS_DIR_.'jquery/ui/';
        $file = 'jquery.'.$component.'.min.js';
        $urlData = parse_url($folder.$file);
        $fileUri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
        $fileUriHostMode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
        $uiTmp = array();
        if (isset(Media::$jquery_ui_dependencies[$component]) && Media::$jquery_ui_dependencies[$component]['theme'] && $checkDependencies) {
            $themeCss = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.ui.theme.css');
            $compCss = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.'.$component.'.css');
            if (!empty($themeCss) || $themeCss) {
                $uiPath['css'] = array_merge($uiPath['css'], $themeCss);
            }
            if (!empty($compCss) || $compCss) {
                $uiPath['css'] = array_merge($uiPath['css'], $compCss);
            }
        }
        if ($checkDependencies && array_key_exists($component, self::$jquery_ui_dependencies)) {
            foreach (self::$jquery_ui_dependencies[$component]['dependencies'] as $dependency) {
                $uiTmp[] = Media::getJqueryUIPath($dependency, $theme, false);
                if (self::$jquery_ui_dependencies[$dependency]['theme']) {
                    $depCss = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.'.$dependency.'.css');
                }


                if (isset($depCss) && (!empty($depCss) || $depCss)) {
                    $uiPath['css'] = array_merge($uiPath['css'], $depCss);
                }
            }
        }
        if (@filemtime($fileUri) || (defined('_PS_HOST_MODE_') && @filemtime($fileUriHostMode))) {
            if (!empty($uiTmp)) {
                foreach ($uiTmp as $ui) {
                    if (!empty($ui['js'])) {
                        $uiPath['js'][] = $ui['js'];
                    }

                    if (!empty($ui['css'])) {
                        $uiPath['css'][] = $ui['css'];
                    }
                }
                $uiPath['js'][] = Media::getJSPath($folder.$file);
            } else {
                $uiPath['js'] = Media::getJSPath($folder.$file);
            }
        }

        //add i18n file for datepicker
        if ($component == 'ui.datepicker') {
            if (!is_array($uiPath['js'])) {
                $uiPath['js'] = array($uiPath['js']);
            }

            $datePickerIsoCode = Context::getContext()->language->iso_code;
            if (array_key_exists($datePickerIsoCode, self::$jquery_ui_datepicker_iso_code)) {
                $datePickerIsoCode = self::$jquery_ui_datepicker_iso_code[$datePickerIsoCode];
            }
            $uiPath['js'][] = Media::getJSPath($folder.'i18n/jquery.ui.datepicker-'.$datePickerIsoCode.'.js');
        }

        return $uiPath;
    }

    /**
     * return jquery plugin path.
     *
     * @param mixed $name
     * @param string|null  $folder
     *
     * @return bool|string
     */
    public static function getJqueryPluginPath($name, $folder = null)
    {
        $pluginPath = array('js' => array(), 'css' => array());
        if ($folder === null) {
            $folder = _PS_JS_DIR_.'jquery/plugins/';
        } //set default folder

        $file = 'jquery.'.$name.'.js';
        $urlData = parse_url($folder);
        $fileUri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
        $fileUriHostMode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);

        if (@file_exists($fileUri.$file) || (defined('_PS_HOST_MODE_') && @file_exists($fileUriHostMode.$file))) {
            $pluginPath['js'] = Media::getJSPath($folder.$file);
        } elseif (@file_exists($fileUri.$name.'/'.$file) || (defined('_PS_HOST_MODE_') && @file_exists($fileUriHostMode.$name.'/'.$file))) {
            $pluginPath['js'] = Media::getJSPath($folder.$name.'/'.$file);
        } else {
            return false;
        }
        $pluginPath['css'] = Media::getJqueryPluginCSSPath($name, $folder);

        return $pluginPath;
    }

    /**
     * return jquery plugin css path if exist.
     *
     * @param mixed       $name
     * @param string|null $folder
     *
     * @return bool|string
     */
    public static function getJqueryPluginCSSPath($name, $folder = null)
    {
        if ($folder === null) {
            $folder = _PS_JS_DIR_.'jquery/plugins/';
        } //set default folder
        $file = 'jquery.'.$name.'.css';
        $urlData = parse_url($folder);
        $fileUri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);
        $fileUriHostMode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $urlData['path']);

        if (@file_exists($fileUri.$file) || (defined('_PS_HOST_MODE_') && @file_exists($fileUriHostMode.$file))) {
            return Media::getCSSPath($folder.$file);
        } elseif (@file_exists($fileUri.$name.'/'.$file) || (defined('_PS_HOST_MODE_') && @file_exists($fileUriHostMode.$name.'/'.$file))) {
            return Media::getCSSPath($folder.$name.'/'.$file);
        } else {
            return false;
        }
    }

    /**
     * Combine Compress and Cache CSS (ccc) calls
     *
     * @param array $cssFiles
     *
     * @return array processed css_files
     */
    public static function cccCss($cssFiles)
    {
        //inits
        $cssFilesByMedia = array();
        $externalCssFiles = array();
        $compressedCssFiles = array();
        $compressedCssFilesNotFound = array();
        $compressedCssFilesInfos = array();
        $protocolLink = Tools::getCurrentUrlProtocolPrefix();
        $cachePath = _PS_THEME_DIR_.'cache/';

        // group css files by media
        foreach ($cssFiles as $filename => $media) {
            if (!array_key_exists($media, $cssFilesByMedia)) {
                $cssFilesByMedia[$media] = array();
            }

            $infos = array();
            $infos['uri'] = $filename;
            $urlData = parse_url($filename);

            if (array_key_exists('host', $urlData)) {
                $externalCssFiles[$filename] = $media;
                continue;
            }

            $infos['path'] = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $urlData['path']);

            if (!@filemtime($infos['path'])) {
                $infos['path'] = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $urlData['path']);
            }

            $cssFilesByMedia[$media]['files'][] = $infos;
            if (!array_key_exists('date', $cssFilesByMedia[$media])) {
                $cssFilesByMedia[$media]['date'] = 0;
            }
            $cssFilesByMedia[$media]['date'] = max(
                (int) @filemtime($infos['path']),
                $cssFilesByMedia[$media]['date']
            );

            if (!array_key_exists($media, $compressedCssFilesInfos)) {
                $compressedCssFilesInfos[$media] = array('key' => '');
            }
            $compressedCssFilesInfos[$media]['key'] .= $filename;
        }

        // get compressed css file infos
        $version = (int) Configuration::get('PS_CCCCSS_VERSION');
        foreach ($compressedCssFilesInfos as $media => &$info) {
            $key = md5($info['key'].$protocolLink);
            $filename = $cachePath.'v_'.$version.'_'.$key.'_'.$media.'.css';

            $info = array(
                'key' => $key,
                'date' => (int) @filemtime($filename),
            );
        }

        foreach ($cssFilesByMedia as $media => $mediaInfos) {
            if ($mediaInfos['date'] > $compressedCssFilesInfos[$media]['date']) {
                if ($compressedCssFilesInfos[$media]['date']) {
                    Configuration::updateValue('PS_CCCCSS_VERSION', ++$version);
                    break;
                }
            }
        }

        // aggregate and compress css files content, write new caches files
        $importUrl = array();
        foreach ($cssFilesByMedia as $media => $mediaInfos) {
            $cacheFilename = $cachePath.'v_'.$version.'_'.$compressedCssFilesInfos[$media]['key'].'_'.$media.'.css';
            if ($mediaInfos['date'] > $compressedCssFilesInfos[$media]['date']) {
                $cacheFilename = $cachePath.'v_'.$version.'_'.$compressedCssFilesInfos[$media]['key'].'_'.$media.'.css';
                $compressedCssFiles[$media] = '';
                foreach ($mediaInfos['files'] as $file_infos) {
                    if (file_exists($file_infos['path'])) {
                        $compressedCssFiles[$media] .= Media::minifyCSS(file_get_contents($file_infos['path']), $file_infos['uri'], $importUrl);
                    } else {
                        $compressedCssFilesNotFound[] = $file_infos['path'];
                    }
                }
                if (!empty($compressedCssFilesNotFound)) {
                    $content = '/* WARNING ! file(s) not found : "'.
                        implode(',', $compressedCssFilesNotFound).
                        '" */'."\n".$compressedCssFiles[$media];
                } else {
                    $content = $compressedCssFiles[$media];
                }

                $content = '@charset "UTF-8";'."\n".$content;
                $content = implode('', $importUrl).$content;
                file_put_contents($cacheFilename, $content);
                chmod($cacheFilename, 0777);
            }
            $compressedCssFiles[$media] = $cacheFilename;
        }

        // rebuild the original css_files array
        $cssFiles = array();
        foreach ($compressedCssFiles as $media => $filename) {
            $url = str_replace(_PS_THEME_DIR_, _THEMES_DIR_._THEME_NAME_.'/', $filename);
            $cssFiles[$protocolLink.Tools::getMediaServer($url).$url] = $media;
        }

        return array_merge($externalCssFiles, $cssFiles);
    }

    /**
     * Get backtrack limit
     *
     * @return int|null|string
     */
    public static function getBackTrackLimit()
    {
        static $limit = null;
        if ($limit === null) {
            $limit = @ini_get('pcre.backtrack_limit');
            if (!$limit) {
                $limit = -1;
            }
        }

        return $limit;
    }

    /**
     * Combine Compress and Cache (ccc) JS calls
     *
     * @param array $jsFiles
     *
     * @return array processed js_files
     */
    public static function cccJS($jsFiles)
    {
        //inits
        $compressedJsFilesNotFound = array();
        $jsFilesInfos = array();
        $jsFilesDate = 0;
        $compressedJsFilename = '';
        $jsExternalFiles = array();
        $protocolLink = Tools::getCurrentUrlProtocolPrefix();
        $cachePath = _PS_THEME_DIR_.'cache/';

        // get js files infos
        foreach ($jsFiles as $filename) {
            if (Validate::isAbsoluteUrl($filename)) {
                $jsExternalFiles[] = $filename;
            } else {
                $infos = array();
                $infos['uri'] = $filename;
                $urlData = parse_url($filename);
                $infos['path'] = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $urlData['path']);

                if (!@filemtime($infos['path'])) {
                    $infos['path'] = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $urlData['path']);
                }

                $jsFilesInfos[] = $infos;

                $jsFilesDate = max(
                    (int) @filemtime($infos['path']),
                    $jsFilesDate
                );
                $compressedJsFilename .= $filename;
            }
        }

        // get compressed js file infos
        $compressedJsFilename = md5($compressedJsFilename);
        $version = (int) Configuration::get('PS_CCCJS_VERSION');
        $compressedJsPath = $cachePath.'v_'.$version.'_'.$compressedJsFilename.'.js';
        $compressedJsFileDate = (int) @filemtime($compressedJsPath);

        // aggregate and compress js files content, write new caches files
        if ($jsFilesDate > $compressedJsFileDate) {
            if ($compressedJsFileDate) {
                Configuration::updateValue('PS_CCCJS_VERSION', ++$version);
            }

            $compressedJsPath = $cachePath.'v_'.$version.'_'.$compressedJsFilename.'.js';
            $content = '';
            foreach ($jsFilesInfos as $fileInfos) {
                if (file_exists($fileInfos['path'])) {
                    $tmpContent = file_get_contents($fileInfos['path']);
                    if (preg_match('@\.(min|pack)\.[^/]+$@', $fileInfos['path'], $matches)) {
                        $content .= preg_replace('/\/\/@\ssourceMappingURL\=[_a-zA-Z0-9-.]+\.'.$matches[1].'\.map\s+/', '', $tmpContent);
                    } else {
                        $content .= Media::packJS($tmpContent);
                    }
                } else {
                    $compressedJsFilesNotFound[] = $fileInfos['path'];
                }
            }

            if (!empty($compressedJsFilesNotFound)) {
                $content = '/* WARNING ! file(s) not found : "'.
                    implode(',', $compressedJsFilesNotFound).
                    '" */'."\n".$content;
            }

            file_put_contents($compressedJsPath, $content);
            chmod($compressedJsPath, 0777);
        }

        // rebuild the original js_files array
        if (strpos($compressedJsPath, _PS_ROOT_DIR_) !== false) {
            $url = str_replace(_PS_ROOT_DIR_.'/', __PS_BASE_URI__, $compressedJsPath);
        }

        if (strpos($compressedJsPath, _PS_CORE_DIR_) !== false) {
            $url = str_replace(_PS_CORE_DIR_.'/', __PS_BASE_URI__, $compressedJsPath);
        }

        return array_merge(array($protocolLink.Tools::getMediaServer($url).$url), $jsExternalFiles);
    }

    /**
     * Clear theme cache
     *
     * @return void
     */
    public static function clearCache()
    {
        $files = array_merge(
            glob(_PS_THEME_DIR_.'assets/cache/*'),
            glob(_PS_THEME_DIR_.'cache/*')
        );

        foreach ($files as $file) {
            if ('index.php' !== basename($file)) {
                Tools::deleteFile($file);
            }
        }

        $version = (int) Configuration::get('PS_CCCJS_VERSION');
        Configuration::updateValue('PS_CCCJS_VERSION', ++$version);
        $version = (int) Configuration::get('PS_CCCCSS_VERSION');
        Configuration::updateValue('PS_CCCCSS_VERSION', ++$version);
    }

    /**
     * Get JS definitions
     *
     * @return array JS definitions
     */
    public static function getJsDef()
    {
        ksort(Media::$js_def);
        return Media::$js_def;
    }

    /**
     * Get JS inline script
     *
     * @return array inline script
     */
    public static function getInlineScript()
    {
        return Media::$inline_script;
    }

    /**
     * Add a new javascript definition at bottom of page
     *
     * @param mixed $jsDef
     *
     * @return void
     */
    public static function addJsDef($jsDef)
    {
        if (is_array($jsDef)) {
            foreach ($jsDef as $key => $js) {
                Media::$js_def[$key] = $js;
            }
        } elseif ($jsDef) {
            Media::$js_def[] = $jsDef;
        }
    }

    /**
     * Add a new javascript definition from a capture at bottom of page
     *
     * @param mixed $params
     * @param string $content
     * @param Smarty $smarty
     * @param bool $repeat
     *
     * @return void
     */
    public static function addJsDefL($params, $content, $smarty = null, &$repeat = false)
    {
        if (!$repeat && isset($params) && Tools::strlen($content)) {
            if (!is_array($params)) {
                $params = (array) $params;
            }

            foreach ($params as $param) {
                Media::$js_def[$param] = $content;
            }
        }
    }

    public static function deferInlineScripts($output)
    {
        /* Try to enqueue in js_files inline scripts with src but without conditionnal comments */
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(($output));
        libxml_use_internal_errors(false);
        $scripts = $dom->getElementsByTagName('script');
        if (is_object($scripts) && $scripts->length) {
            foreach ($scripts as $script) {
                /** @var DOMElement $script */
                if ($src = $script->getAttribute('src')) {
                    if (substr($src, 0, 2) == '//') {
                        $src = Tools::getCurrentUrlProtocolPrefix().substr($src, 2);
                    }

                    $patterns = array(
                        '#code\.jquery\.com/jquery-([0-9\.]+)(\.min)*\.js$#Ui',
                        '#ajax\.googleapis\.com/ajax/libs/jquery/([0-9\.]+)/jquery(\.min)*\.js$#Ui',
                        '#ajax\.aspnetcdn\.com/ajax/jquery/jquery-([0-9\.]+)(\.min)*\.js$#Ui',
                        '#cdnjs\.cloudflare\.com/ajax/libs/jquery/([0-9\.]+)/jquery(\.min)*\.js$#Ui',
                        '#/jquery-([0-9\.]+)(\.min)*\.js$#Ui'
                    );

                    foreach ($patterns as $pattern) {
                        $matches = array();
                        if (preg_match($pattern, $src, $matches)) {
                            $minifier = $version = false;
                            if (isset($matches[2]) && $matches[2]) {
                                $minifier = (bool) $matches[2];
                            }
                            if (isset($matches[1]) && $matches[1]) {
                                $version = $matches[1];
                            }
                            if ($version) {
                                if ($version != _PS_JQUERY_VERSION_) {
                                    Context::getContext()->controller->addJquery($version, null, $minifier);
                                }
                                Media::$inline_script_src[] = $src;
                            }
                        }
                    }
                    if (!in_array($src, Media::$inline_script_src) && !$script->getAttribute(Media::$pattern_keepinline)) {
                        Context::getContext()->controller->addJS($src);
                    }
                }
            }
        }
        $output = preg_replace_callback(Media::$pattern_js, array('Media', 'deferScript'), $output);

        return $output;
    }

    /**
     * Get all JS scripts and place it to bottom
     * To be used in callback with deferInlineScripts
     *
     * @param array $matches
     *
     * @return bool|string Empty string or original script lines
     */
    public static function deferScript($matches)
    {
        if (!is_array($matches)) {
            return false;
        }
        $inline = '';

        if (isset($matches[0])) {
            $original = trim($matches[0]);
        }

        if (isset($matches[2])) {
            $inline = trim($matches[2]);
        }

        /* This is an inline script, add its content to inline scripts stack then remove it from content */
        if (!empty($inline) && preg_match(Media::$pattern_js, $original) !== false && !preg_match('/'.Media::$pattern_keepinline.'/', $original) && Media::$inline_script[] = $inline) {
            return '';
        }
        /* This is an external script, if it already belongs to js_files then remove it from content */
        preg_match('/src\s*=\s*["\']?([^"\']*)[^>]/ims', $original, $results);
        if (array_key_exists(1, $results)) {
            if (substr($results[1], 0, 2) == '//') {
                $protocolLink = Tools::getCurrentUrlProtocolPrefix();
                $results[1] = $protocolLink.ltrim($results[1], '/');
            }

            if (in_array($results[1], Context::getContext()->controller->js_files) || in_array($results[1], Media::$inline_script_src)) {
                return '';
            }
        }

        /* return original string because no match was found */
        return "\n".$original;
    }
}
