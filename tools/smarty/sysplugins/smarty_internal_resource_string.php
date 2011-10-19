<?php
/**
 * Smarty Internal Plugin Resource String
 *
 * @package Smarty
 * @subpackage TemplateResources
 * @author Uwe Tews
 * @author Rodney Rehm
 */

/**
 * Smarty Internal Plugin Resource String
 *
 * Implements the strings as resource for Smarty template
 *
 * {@internal unlike eval-resources the compiled state of string-resources is saved for subsequent access}}
 *
 * @package Smarty
 * @subpackage TemplateResources
 */
class Smarty_Internal_Resource_String extends Smarty_Resource {

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     * @return void
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template=null)
    {
        $source->uid = $source->filepath = sha1($source->name);
        $source->timestamp = 0;
        $source->exists = true;
    }

    /**
     * Load template's source from $resource_name into current template object
     *
     * {@internal if source begins with "base64:" or "urlencode:", the source is decoded accordingly}}
     *
     * @param Smarty_Template_Source $source source object
     * @return string template source
     * @throws SmartyException if source cannot be loaded
     */
    public function getContent(Smarty_Template_Source $source)
    {
        // decode if specified
        if (($pos = strpos($source->name, ':')) !== false) {
            if (!strncmp($source->name, 'base64', 6)) {
                return base64_decode(substr($source->name, 7));
            } elseif (!strncmp($source->name, 'urlencode', 9)) {
                return urldecode(substr($source->name, 10));
            }
        }

        return $source->name;
    }

    /**
     * Determine basename for compiled filename
     *
     * Always returns an empty string.
     *
     * @param Smarty_Template_Source $source source object
     * @return string resource's basename
     */
    protected function getBasename(Smarty_Template_Source $source)
    {
        return '';
    }

}

?>