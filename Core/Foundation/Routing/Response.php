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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Routing;

use Symfony\Component\HttpFoundation\Response as sfResponse;
use PrestaShop\PrestaShop\Core\Foundation\View\ViewFactory;
use PrestaShop\PrestaShop\Core\Business\Context;

/**
 * This is an extension of Symfony's Response class, to add $contentData, and template engine callback attributes.
 * @see Symfony\Component\HttpFoundation\Response
 */
class Response extends sfResponse
{
    /**
     * @var Response
     */
    private static $lastRouterResponseInstance = null;

    /**
     * Get the Reponse instance pinned by the Router during dispatch() call.
     *
     * @return Response
     */
    public static function getLastRouterResponseInstance()
    {
        return self::$lastRouterResponseInstance;
    }
    
    /**
     * Stores the current object in a singleton attribute, as the last Response instantiated during dispatch() call.
     * This is done by the Router.
     */
    public function pinAsLastRouterResponseInstance()
    {
        self::$lastRouterResponseInstance = $this;
    }

    /**
     * @var mixed
     */
    protected $contentData = array();

    /**
     * @var string|false
     */
    protected $responseFormat = false;

    /**
     * @var callable|false
     */
    protected $templateEngine = false;

    /**
     * @var string
     */
    protected $engineName = 'smarty';

    /**
     * @var string
     */
    protected $template = null;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $displayType = null;

    /**
     * @var string
     */
    protected $legacyControllerName = null;

    /**
     * @var array
     */
    protected $headerToolbarBtn = array();

    /**
     * Set data before formatting.
     *
     * @param mixed $data
     */
    final public function setContentData($data)
    {
        $this->contentData = $data;
    }

    /**
     * Add data before formatting.
     * The key should not exists in the dataset to be inserted.
     *
     * @param string $key
     * @param mixed $data
     * @return boolean False if key already exists in the dataset. True for success.
     */
    final public function addContentData($key, $data)
    {
        if (array_key_exists($key, $this->contentData)) {
            return false;
        }
        $this->contentData[$key] = $data;
        return true;
    }

    /**
     * Add or replace data before formatting.
     * If the key already exists in the dataset, the data will be replaced.
     *
     * @param string $key
     * @param mixed $data
     * @return Response $this object, to allow chaining functions.
     */
    final public function replaceContentData($key, $data)
    {
        $this->contentData[$key] = $data;
        return $this;
    }

    /**
     * Get data to format it.
     *
     * @param string $key The key to retrieve in the data. If null (by default), retrieves all data array.
     * @return array|mixed The whole array, or just the requested sub element.
     */
    final public function getContentData($key = null)
    {
        if ($key !== null) {
            if (!array_key_exists($key, $this->contentData)) {
                return null;
            }
            return $this->contentData[$key];
        }
        return $this->contentData;
    }
    
    /**
     * Set suggested response format.
     *
     * @param string $format
     */
    final public function setResponseFormat($format)
    {
        $this->responseFormat = $format;
    }
    
    /**
     * Get suggested response format.
     *
     * @return string
     */
    final public function getResponseFormat()
    {
        return $this->responseFormat;
    }
    
    /**
     * Set template engine (callable finetuned & ready to be executed)
     *
     * @param object $callable
     */
    final public function setTemplateEngine($callable)
    {
        $this->templateEngine = $callable;
    }
    
    /**
     * Get template engine
     *
     * @param \Core_Foundation_IoC_Container &$container The Container needed to call ViewFactory
     * @return object
     */
    final public function getTemplateEngine(\Core_Foundation_IoC_Container &$container)
    {
        if (!$this->templateEngine) {
            $this->setTemplateEngine(new ViewFactory($container, $this->getEngineName()));
        }

        return $this->templateEngine;
    }

    /**
     * Set engine name
     *
     * @param string $engineName
     */
    public function setEngineName($engineName)
    {
        $this->engineName = $engineName;
    }

    /**
     * Get engine name
     *
     * @return string
     */
    public function getEngineName()
    {
        return $this->engineName;
    }

    /**
     * Set template
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set legacyControllerName
     *
     * @param string $legacyControllerName
     */
    public function setLegacyControllerName($legacyControllerName)
    {
        $this->legacyControllerName = $legacyControllerName;
    }

    /**
     * Get legacyControllerName
     *
     * @return string
     */
    public function getLegacyControllerName()
    {
        return $this->legacyControllerName ? $this->legacyControllerName : 'AdminDashboard';
    }

    /**
     * Set headerToolbarBtn
     *
     * @param array $headerToolbarBtn
     */
    public function setHeaderToolbarBtn($headerToolbarBtn)
    {
        $this->headerToolbarBtn = $headerToolbarBtn;
    }

    /**
     * Get headerToolbarBtn
     *
     * @return string
     */
    public function getHeaderToolbarBtn()
    {
        return $this->headerToolbarBtn;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get legacyControllerName
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set displayType
     *
     * @param string $displayType
     */
    public function setDisplayType($displayType)
    {
        $this->displayType = $displayType;
    }

    /**
     * Get $displayType
     *
     * @return string
     */
    public function getDisplayType()
    {
        return $this->displayType;
    }
}
