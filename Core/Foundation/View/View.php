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
namespace PrestaShop\PrestaShop\Core\Foundation\View;

use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Business\Context;

class View
{
    protected $container;
    protected $data;
    protected $templatesDirectory;
    public $appPath;

    public function __construct(\Core_Foundation_IoC_Container $container)
    {
        $this->container = $container;
        $this->data = [];
    }

    /**
     * Has data
     * @param  string  $key
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->data[$key]) ? true : false;
    }

    /**
     * Get data
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Set data
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Get all datas
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Replace datas
     * @param  array  $data
     */
    public function replace(array $data)
    {
        $this->data = $data;
    }

    /**
     * Clear datas
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * Set the base template directory
     * @param   string $directory
     */
    public function setTemplatesDirectory($directory)
    {
        $this->templatesDirectory = rtrim($directory, DIRECTORY_SEPARATOR);
    }

    /**
     * Get template directory
     * @return string
     */
    public function getTemplatesDirectory()
    {
        return $this->templatesDirectory;
    }

    /**
     * Get template directory path
     * @param  string $file
     * @return string
     */
    public function getTemplatePathname($file)
    {
        return $this->templatesDirectory . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
    }

    /**
     * Display
     *
     * @param  string $template
     * @param  array $data
     */
    public function display($template, $data = null)
    {
        echo $this->fetch($template, $data);
    }

    /**
     * Fetch
     *
     * @param  string $template
     * @param  array  $data
     * @return string
     */
    public function fetch($template, $data = null)
    {
        return $this->render($template, $data);
    }

    /**
     * Render
     * @param  string $template
     * @param  array  $data
     * @return string
     * @throws \Exception
     */
    protected function render($template, $data = null)
    {
        $templatePathname = $this->getTemplatePathname($template);
        if (!is_file($templatePathname)) {
            throw new DevelopmentErrorException("View cannot render `$template` because the template does not exist");
        }

        $data = array_merge($this->data, (array) $data);
        extract($data);
        ob_start();
        require $templatePathname;

        return ob_get_clean();
    }
}
