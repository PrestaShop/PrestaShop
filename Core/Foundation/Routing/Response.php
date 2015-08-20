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

/**
 * This is an extension of Symfony's Response class, to add $contentData, and template engine callback attributes.
 * @see Symfony\Component\HttpFoundation\Response
 */
class Response extends sfResponse
{
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
     * Set data before formatting.
     *
     * @param mixed $data
     */
    public final function setContentData($data)
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
    public final function addContentData($key, $data)
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
    public final function replaceContentData($key, $data)
    {
        $this->contentData[$key] = $data;
        return $this;
    }

    /**
     * Get data to format it.
     *
     * @return mixed
     */
    public final function getContentData()
    {
        return $this->contentData;
    }
    
    /**
     * Set suggested response format.
     *
     * @param string $format
     */
    public final function setResponseFormat($format)
    {
        $this->responseFormat = $format;
    }
    
    /**
     * Get suggested response format.
     *
     * @return string
     */
    public final function getResponseFormat()
    {
        return $this->responseFormat;
    }
    
    /**
     * Set template engine (callable finetuned & ready to be executed)
     *
     * @param callable $callable
     */
    public final function setTemplateEngine(callable $callable)
    {
        $this->templateEngine = $callable;
    }
    
    /**
     * Get template engine (callable finetuned & ready to be executed)
     *
     * @return callable|false
     */
    public final function getTemplateEngine()
    {
        return $this->templateEngine;
    }
}