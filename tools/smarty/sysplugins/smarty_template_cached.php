<?php
/**
 * Created by PhpStorm.
 * User: Uwe Tews
 * Date: 04.12.2014
 * Time: 06:08
 */

/**
 * Smarty Resource Data Object
 * Cache Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 */
class Smarty_Template_Cached
{
    /**
     * Source Filepath
     *
     * @var string
     */
    public $filepath = false;

    /**
     * Source Content
     *
     * @var string
     */
    public $content = null;

    /**
     * Source Timestamp
     *
     * @var integer
     */
    public $timestamp = false;

    /**
     * Source Existence
     *
     * @var boolean
     */
    public $exists = false;

    /**
     * Cache Is Valid
     *
     * @var boolean
     */
    public $valid = null;

    /**
     * Cache was processed
     *
     * @var boolean
     */
    public $processed = false;

    /**
     * CacheResource Handler
     *
     * @var Smarty_CacheResource
     */
    public $handler = null;

    /**
     * Template Compile Id (Smarty_Internal_Template::$compile_id)
     *
     * @var string
     */
    public $compile_id = null;

    /**
     * Template Cache Id (Smarty_Internal_Template::$cache_id)
     *
     * @var string
     */
    public $cache_id = null;

    /**
     * Id for cache locking
     *
     * @var string
     */
    public $lock_id = null;

    /**
     * flag that cache is locked by this instance
     *
     * @var bool
     */
    public $is_locked = false;

    /**
     * Source Object
     *
     * @var Smarty_Template_Source
     */
    public $source = null;

    /**
     * create Cached Object container
     *
     * @param Smarty_Internal_Template $_template template object
     */
    public function __construct(Smarty_Internal_Template $_template)
    {
        $this->compile_id = $_template->compile_id;
        $this->cache_id = $_template->cache_id;
        if (!isset($_template->source)) {
            $_template->loadSource();
        }
        $this->source = $_template->source;
        if (!class_exists('Smarty_CacheResource', false)) {
            require SMARTY_SYSPLUGINS_DIR . 'smarty_cacheresource.php';
        }
        $this->handler = Smarty_CacheResource::load($_template->smarty);
    }

    /**
     * @param Smarty_Internal_Template $_template
     *
     * @return Smarty_Template_Cached
     */
    static function load(Smarty_Internal_Template $_template)
    {
        $_template->cached = $cached = new Smarty_Template_Cached($_template);
        $cached->handler->populate($cached, $_template);
        // caching enabled ?
        if (!($_template->caching == Smarty::CACHING_LIFETIME_CURRENT || $_template->caching == Smarty::CACHING_LIFETIME_SAVED) || $_template->source->recompiled) {
            $cached->valid = false;
        }
        return $cached;
    }

    /**
     * Check if cache is valid, lock cache if required
     *
     * @param \Smarty_Internal_Template $_template
     *
     * @return bool flag true if cache is valid
     */
    public function isCached(Smarty_Internal_Template $_template)
    {
        if ($this->valid !== null) {
            return $this->valid;
        }
        while (true) {
            while (true) {
                 if ($this->exists === false || $_template->smarty->force_compile || $_template->smarty->force_cache) {
                    $this->valid = false;
                } else {
                    $this->valid = true;
                }
                if ($this->valid && $_template->caching == Smarty::CACHING_LIFETIME_CURRENT && $_template->cache_lifetime >= 0 && time() > ($this->timestamp + $_template->cache_lifetime)) {
                    // lifetime expired
                    $this->valid = false;
                }
                if ($this->valid && $_template->source->timestamp > $this->timestamp) {
                    $this->valid = false;
                }
                if ($this->valid || !$_template->smarty->cache_locking) {
                    break;
                }
                if (!$this->handler->locked($_template->smarty, $this)) {
                    $this->handler->acquireLock($_template->smarty, $this);
                    break 2;
                }
                $this->handler->populate($this, $_template);
            }
            if ($this->valid) {
                if (!$_template->smarty->cache_locking || $this->handler->locked($_template->smarty, $this) === null) {
                    // load cache file for the following checks
                    if ($_template->smarty->debugging) {
                        Smarty_Internal_Debug::start_cache($_template);
                    }
                    if ($this->handler->process($_template, $this) === false) {
                        $this->valid = false;
                    } else {
                        $this->processed = true;
                    }
                    if ($_template->smarty->debugging) {
                        Smarty_Internal_Debug::end_cache($_template);
                    }
                } else {
                    $this->is_locked = true;
                    continue;
                }
            } else {
                return $this->valid;
            }
            if ($this->valid && $_template->caching === Smarty::CACHING_LIFETIME_SAVED && $_template->properties['cache_lifetime'] >= 0 && (time() > ($_template->cached->timestamp + $_template->properties['cache_lifetime']))) {
                $this->valid = false;
            }
            if ($_template->smarty->cache_locking) {
                if (!$this->valid) {
                    $this->handler->acquireLock($_template->smarty, $this);
                } elseif ($this->is_locked) {
                    $this->handler->releaseLock($_template->smarty, $this);
                }
            }
            return $this->valid;
        }
        return $this->valid;
    }

    /**
     * Process cached template
     *
     * @param Smarty_Internal_Template $_template template object
     */
    public function process(Smarty_Internal_Template $_template)
    {
        if ($this->handler->process($_template, $this) === false) {
            $this->valid = false;
        }
        if ($this->valid) {
            $this->processed = true;
        } else {
            $this->processed = false;
        }
    }

    /**
     * Render cached template
     *
     * @param Smarty_Internal_Template $_template
     *
     * @return string
     * @throws Exception
     */
    public function render(Smarty_Internal_Template $_template)
    {
        if (!$this->processed) {
            $this->process($_template);
        }
        return $_template->getRenderedTemplateCode();
    }

    /**
     * Write this cache object to handler
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return boolean success
     */
    public function write(Smarty_Internal_Template $_template, $content)
    {
        if (!$_template->source->recompiled) {
            if ($this->handler->writeCachedContent($_template, $content)) {
                $this->content = null;
                $this->timestamp = time();
                $this->exists = true;
                $this->valid = true;
                $this->processed = false;
                if ($_template->smarty->cache_locking) {
                    $this->handler->releaseLock($_template->smarty, $this);
                }

                return true;
            }
            $this->content = null;
            $this->timestamp = false;
            $this->exists = false;
            $this->valid = false;
            $this->processed = false;
        }

        return false;
    }

    /**
     * Read cache content from handler
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @return string content
     */
    public function read(Smarty_Internal_Template $_template)
    {
        if (!$_template->source->recompiled) {
            return $this->handler->readCachedContent($_template);
        }
        return false;
    }

    /**
     * Sanitize content and write it to cache resource
     *
     * @param Smarty_Internal_Template $_template
     * @param string                   $content
     * @param bool                     $no_output_filter
     *
     * @throws SmartyException
     */
    public function updateCache(Smarty_Internal_Template $_template, $content, $no_output_filter)
    {
        $_template->properties['has_nocache_code'] = false;
        // get text between non-cached items
        $cache_split = preg_split("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $content);
        // get non-cached items
        preg_match_all("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $content, $cache_parts);
        $output = '';
        // loop over items, stitch back together
        foreach ($cache_split as $curr_idx => $curr_split) {
            // escape PHP tags in template content
            $output .= preg_replace('/(<%|%>|<\?php|<\?|\?>|<script\s+language\s*=\s*[\"\']?\s*php\s*[\"\']?\s*>)/', "<?php echo '\$1'; ?>\n", $curr_split);
            if (isset($cache_parts[0][$curr_idx])) {
                $_template->properties['has_nocache_code'] = true;
                $output .= $cache_parts[1][$curr_idx];
            }
        }
        if (!$no_output_filter && !$_template->has_nocache_code && (isset($_template->smarty->autoload_filters['output']) || isset($_template->smarty->registered_filters['output']))) {
            $output = Smarty_Internal_Filter_Handler::runFilter('output', $output, $_template);
        }
        // write cache file content
        $this->writeCachedContent($_template, $output);
    }

    /**
     * Writes the content to cache resource
     *
     * @param Smarty_Internal_Template $_template
     * @param string                   $content
     *
     * @return bool
     */
    public function writeCachedContent(Smarty_Internal_Template $_template, $content)
    {
        if ($_template->source->recompiled || !($_template->caching == Smarty::CACHING_LIFETIME_CURRENT || $_template->caching == Smarty::CACHING_LIFETIME_SAVED)) {
            // don't write cache file
            return false;
        }
        $_template->properties['cache_lifetime'] = $_template->cache_lifetime;
        $_template->properties['unifunc'] = 'content_' . str_replace(array('.', ','), '_', uniqid('', true));
        $content = Smarty_Internal_Extension_CodeFrame::create($_template, $content, true);
        if (!empty($_template->properties['tpl_function'])) {
            foreach ($_template->properties['tpl_function'] as $funcParam) {
                if (is_file($funcParam['compiled_filepath'])) {
                    // read compiled file
                    $code = file_get_contents($funcParam['compiled_filepath']);
                    // grab template function
                    if (preg_match("/\/\* {$funcParam['call_name']} \*\/([\S\s]*?)\/\*\/ {$funcParam['call_name']} \*\//", $code, $match)) {
                        unset($code);
                        $content .= "<?php " . $match[0] . "?>\n";
                    }
                }
            }
        }
        return $this->write($_template, $content);
    }

    /**
     * check client side cache
     *
     * @param Smarty_Internal_Template $_template
     * @param  string                  $content
     */
    public function cacheModifiedCheck(Smarty_Internal_Template $_template, $content)
    {
        $_isCached = $_template->isCached() && !$_template->has_nocache_code;
        $_last_modified_date = @substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
        if ($_isCached && $this->timestamp <= strtotime($_last_modified_date)) {
            switch (PHP_SAPI) {
                case 'cgi': // php-cgi < 5.3
                case 'cgi-fcgi': // php-cgi >= 5.3
                case 'fpm-fcgi': // php-fpm >= 5.3.3
                    header('Status: 304 Not Modified');
                    break;

                case 'cli':
                    if ( /* ^phpunit */
                    !empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS']) /* phpunit$ */
                    ) {
                        $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
                    }
                    break;

                default:
                    header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
                    break;
            }
        } else {
            switch (PHP_SAPI) {
                case 'cli':
                    if ( /* ^phpunit */
                    !empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS']) /* phpunit$ */
                    ) {
                        $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->timestamp) . ' GMT';
                    }
                    break;

                default:
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->timestamp) . ' GMT');
                    break;
            }
            echo $content;
        }
    }
}
