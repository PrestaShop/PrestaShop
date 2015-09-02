<?php
/**
 * 2007-2015 PrestaShop
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop;

class View
{
    /**
     * Data available to the view templates
     */
    protected $data;

    /**
     * Path to templates base directory (without trailing slash)
     * @var string
     */
    protected $templatesDirectory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * Does view data have value with key?
     * @param  string  $key
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->data[$key]) ? true : false;
    }

    /**
     * Return view data value with key
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Set view data value with key
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Return view data
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Replace view data
     * @param  array  $data
     */
    public function replace(array $data)
    {
        $this->data = $data;
    }

    /**
     * Clear view data
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * Set the base directory that contains view templates
     * @param   string $directory
     * @throws  \InvalidArgumentException If directory is not a directory
     */
    public function setTemplatesDirectory($directory)
    {
        $this->templatesDirectory = rtrim($directory, DIRECTORY_SEPARATOR);
    }

    /**
     * Get templates base directory
     * @return string
     */
    public function getTemplatesDirectory()
    {
        return $this->templatesDirectory;
    }

    /**
     * Get fully qualified path to template file using templates base directory
     * @param  string $file The template file pathname relative to templates base directory
     * @return string
     */
    public function getTemplatePathname($file)
    {
        return $this->templatesDirectory . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
    }

    /********************************************************************************
     * Rendering
     *******************************************************************************/

    /**
     * Display template
     *
     * This method echoes the rendered template to the current output buffer
     *
     * @param  string   $template   Pathname of template file relative to templates directory
     * @param  array    $data       Any additonal data to be passed to the template.
     */
    public function display($template, $data = null)
    {
        echo $this->fetch($template, $data);
    }

    /**
     * Return the contents of a rendered template file
     *
     * @param    string $template   The template pathname, relative to the template base directory
     * @param    array  $data       Any additonal data to be passed to the template.
     * @return string               The rendered template
     */
    public function fetch($template, $data = null)
    {
        return $this->render($template, $data);
    }

    /**
     * Render a template file
     *
     * NOTE: This method should be overridden by custom view subclasses
     *
     * @param  string $template     The template pathname, relative to the template base directory
     * @param  array  $data         Any additonal data to be passed to the template.
     * @return string               The rendered template
     * @throws \Exception    If resolved template pathname is not a valid file
     */
    protected function render($template, $data = null)
    {
        $templatePathname = $this->getTemplatePathname($template);
        if (!is_file($templatePathname)) {
            throw new \Exception("View cannot render `$template` because the template does not exist");
        }

        $data = array_merge($this->data, (array) $data);
        extract($data);
        ob_start();
        require $templatePathname;

        return ob_get_clean();
    }
}
