<?php
/**
 * Smarty Internal Plugin Resource File
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Uwe Tews
 * @author     Rodney Rehm
 */

/**
 * Smarty Internal Plugin Resource File
 * Implements the file system as resource for Smarty templates
 *
 * @package    Smarty
 * @subpackage TemplateResources
 */
class Smarty_Internal_Resource_File extends Smarty_Resource
{
    /**
     * build template filepath by traversing the template_dir array
     *
     * @param Smarty_Template_Source    $source    source object
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return string fully qualified filepath
     * @throws SmartyException
     */
    protected function buildFilepath(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $file = $source->name;
        preg_match('#^(?P<absolute>[\\\/]|[a-zA-Z]:[\\\/])|(\[(?P<index>[^\]]+)\])|(?P<rel>\.[\\\/])#', $file, $fileMatch);
        // save basename
        if (!empty($fileMatch['absolute'])) {
            $file = $this->normalizePath($file);
            return is_file($file) ? $file : false;
        }
        // go relative to a given template?
        if (!empty($fileMatch['rel']) && $_template && $_template->parent instanceof Smarty_Internal_Template) {
            if ($_template->parent->source->type != 'file' && $_template->parent->source->type != 'extends' && !$_template->parent->allow_relative_path) {
                throw new SmartyException("Template '{$file}' cannot be relative to template of resource type '{$_template->parent->source->type}'");
            }
            $path = dirname($_template->parent->source->filepath) . DS . $file;
            // normalize path
            $path = $this->normalizePath($path);
            // files relative to a template only get one shot
            return is_file($path) ? $path : false;
        }

        if ($source->isConfig) {
            $_directories = $source->smarty->getConfigDir();
        } else {
            $_directories = $source->smarty->getTemplateDir();
        }
        // template_dir index?
        if (!empty($fileMatch['index'])) {
            $index = $fileMatch['index'];
            $_directory = null;
            // try string indexes
            if (isset($_directories[$index])) {
                $_directory = $_directories[$index];
            } elseif (is_numeric($index)) {
                // try numeric index
                $index = (int) $index;
                if (isset($_directories[$index])) {
                    $_directory = $_directories[$index];
                } else {
                    // try at location index
                    $keys = array_keys($_directories);
                    $_directory = $_directories[$keys[$index]];
                }
            }
            if ($_directory) {
                preg_match('#\](.+)$#', $file, $fileMatch);
                $path = $_directory . $fileMatch[1];
                $path = $this->normalizePath($path);
                if (is_file($path)) {
                    return $path;
                }
            } else {
                // index not found
                return false;
            }
        }

        // relative file name?
        foreach ($_directories as $_directory) {
            $_filepath = $_directory . $file;
            $path = $this->normalizePath($_filepath);
            if (is_file($path)) {
                return $path;
            }
            if ($source->smarty->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_directory)) {
                // try PHP include_path
                if (function_exists('stream_resolve_include_path')) {
                    $_filepath = stream_resolve_include_path($_filepath);
                } else {
                    $_filepath = Smarty_Internal_Get_Include_Path::getIncludePath($_filepath);
                }
                if ($_filepath !== false) {
                    $path = $this->normalizePath($_filepath);
                    if (is_file($path)) {
                        return $path;
                    }
                }
            }
        }
        // Could be relative to cwd
        $path = $this->normalizePath(getcwd() . DS . $file);
        return is_file($path) ? $path : false;
    }

    /**
     * Normalize path
     *  - remove /./ and /../
     *  - make it absolute
     *
     * @param string $path file path
     *
     * @return string
     */
    public function normalizePath($path)
    {
        if ($path[0] == '.') {
            $path = getcwd() . DS . $path;
        }
        $path = preg_replace('#[\\\/]+([.][\\\/]+)*#', DS, $path);
        while (strrpos($path, '.' . DS) !== false) {
            $path = preg_replace('#([\\\/]([^\\\/]+[\\\/]){2}([.][.][\\\/]){2})|([\\\/][^\\\/]+[\\\/][.][.][\\\/])#', DS, $path);
        }
        return $path;
    }

    /**
     * test is file exists and save timestamp
     *
     * @param  Smarty_Template_Source $source source object
     * @param  string                 $file   file name
     *
     * @return bool                   true if file exists
     */
    protected function fileExists(Smarty_Template_Source $source, $file)
    {
        $source->timestamp = is_file($file) ? @filemtime($file) : false;
        return $source->exists = !!$source->timestamp;
    }

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $source->filepath = $this->buildFilepath($source, $_template);

        if ($source->filepath !== false) {
            if (is_object($source->smarty->security_policy)) {
                $source->smarty->security_policy->isTrustedResourceDir($source->filepath);
            }
            $source->exists = true;
            $source->uid = sha1($source->filepath);
            if ($source->smarty->compile_check && !isset($source->timestamp)) {
                $source->timestamp = @filemtime($source->filepath);
            }
        } else {
            $source->timestamp = false;
            $source->exists = false;
        }
    }

    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->timestamp = $source->exists = is_file($source->filepath);
        if ($source->exists) {
            $source->timestamp = @filemtime($source->filepath);
        }
    }

    /**
     * Load template's source from file into current template object
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 template source
     * @throws SmartyException        if source cannot be loaded
     */
    public function getContent(Smarty_Template_Source $source)
    {
        if ($source->timestamp) {
            return file_get_contents($source->filepath);
        }
        if ($source instanceof Smarty_Config_Source) {
            throw new SmartyException("Unable to read config {$source->type} '{$source->name}'");
        }
        throw new SmartyException("Unable to read template {$source->type} '{$source->name}'");
    }

    /**
     * Determine basename for compiled filename
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 resource's basename
     */
    public function getBasename(Smarty_Template_Source $source)
    {
        return basename($source->filepath);
    }
}
