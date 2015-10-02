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
namespace PrestaShop\PrestaShop\Core\Business\Controller\ExecutionSequenceService;

use PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceWrapper;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

/**
 * This service will add helpers to try to inflate objects from ORM.
 *
 * When request attributes matches some specific format, the helpers will call ORM
 * objects and inject them into the response->getContentData() array
 */
final class AutoObjectInflater extends ExecutionSequenceServiceWrapper
{
    /**
     * @var \Adapter_AutoInflaterManager
     */
    private $autoInflaterManager;

    /**
     * Constructor.
     *
     * @param \Adapter_AutoInflaterManager $autoInflaterManager
     */
    public function __construct(\Adapter_AutoInflaterManager $autoInflaterManager)
    {
        $this->autoInflaterManager = $autoInflaterManager;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getInitListeners()
     */
    public function getBeforeListeners()
    {
        return array(
            0 => array($this, 'inflateRequestedObjects'),
            1 => array($this, 'inflateRequestedCollection')
        );
    }

    /**
     * This helper will try to identify some elements in the uri parameters (if well set in the routes*.yml files,
     * and in the right syntax), to query them in the database, and to complete $response->getContentData with the
     * found object.
     *
     * Route setting example:
     * path:     /path/to/route/{id_order}/rest/of/the/path
     * This will try to Inflate a Order object by instantiation with the value of the route parameter as the unique constructor parameter.
     *
     * @param BaseEvent $event
     */
    public function inflateRequestedObjects(BaseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        foreach ($request->attributes->all() as $key => $value) {
            // Find parameters that begins with id_ to try to inflate corresponding object
            if (strpos($key, 'id_') === 0) {
                $className = substr($key, 3);
                $object = $this->autoInflaterManager->inflateObject($className, $value);

                if ($object === false) {
                    continue;
                }
                if ($object === null) {
                    $response->addContentData($className, null);
                    continue;
                }
                $response->addContentData($className, $object);
            }
        }
    }

    /**
     * This helper will try to identify some elements in the uri parameters (if well set in the routes*.yml files,
     * and in the right syntax), to query them in the database, and to complete $response->getContentData with the
     * found collections.
     *
     * Route setting example:
     * path:     /path/to/route/{ls_mykey_limit}/{ls_mykey_start}/{ls_mykey_order_by}/{ls_mykey_order_way}
     * defaults:
     *     ls_mykey_class: Product
     *     ls_mykey_method: getProducts
     *     ls_mykey_limit: 10
     *     ls_mykey_start: 0
     *     ls_mykey_order_by: 'id'
     *     ls_mykey_order_way: 'ASC'
     *
     * All these route parameters will be completed by query (GETs) and request (POSTs) parameters that
     * starts with 'ls_mykey_' prefix
     *
     * @param BaseEvent $event
     */
    public function inflateRequestedCollection(BaseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // fetch parameters from route attributes
        $collectionParameters = array();
        foreach ($request->attributes->all() as $key => $value) {
            $subKeys = explode('_', $key, 3);
            if (count($subKeys) < 3) {
                continue;
            }
            if ($subKeys[0] != 'ls') {
                continue;
            }
            $collectionParameters[$subKeys[1]][$subKeys[2]] = $value;
        }

        // fetch parameters from query bag (GETs)
        $collectionQueryParameters = array();
        foreach ($request->query->all() as $key => $value) {
            $subKeys = explode('_', $key, 3);
            if (count($subKeys) < 3) {
                continue;
            }
            if ($subKeys[0] != 'ls') {
                continue;
            }
            $collectionQueryParameters[$subKeys[1]][$subKeys[2]] = $value;
        }

        // fetch parameters from request bag (POSTs)
        $collectionRequestParameters = array();
        foreach ($request->request->all() as $key => $value) {
            $subKeys = explode('_', $key, 3);
            if (count($subKeys) < 3) {
                continue;
            }
            if ($subKeys[0] != 'ls') {
                continue;
            }
            $collectionRequestParameters[$subKeys[1]][$subKeys[2]] = $value;
        }

        foreach ($collectionParameters as $key => $parameters) {
            try {
                $method = $parameters['method'];
                $class = $parameters['class'];

                $collection = $this->autoInflaterManager->inflateCollection(
                    $class,
                    $method,
                    $parameters,
                    isset($collectionQueryParameters[$key])? $collectionQueryParameters[$key] : null,
                    isset($collectionRequestParameters[$key])? $collectionRequestParameters[$key] : null
                );

                if ($collection === false) {
                    continue;
                }
                if ($collection === null) {
                    // To indicate we tried, but failed.
                    $response->addContentData($key, null);
                }
                $response->addContentData($key, $collection);
            } catch (\Exception $e) {
                // To indicate we tried, but failed.
                $response->addContentData($key, null);
            }
        }
    }
}
