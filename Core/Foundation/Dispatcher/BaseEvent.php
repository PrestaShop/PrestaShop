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
namespace PrestaShop\PrestaShop\Core\Foundation\Dispatcher;

use Symfony\Component\EventDispatcher\Event;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * The BaseEvent class will extend Symfony Event class, adding some specific elements.
 *
 * These kinds of element could be passed to the listeners:
 * - the application service Container
 * - an optional Exception if the Event is triggered from a catch block (for example)
 * - The Request & the Response objects, if the dispatcher allows the listener to access them
 * - and even more in the subclasses.
 */
class BaseEvent extends Event
{
    private $request = null;
    private $response = null;
    private $filePath = null;
    private $message = null;
    private $exception = null;
    private $container = null;

    /**
     * Constructor
     *
     * @see Event::__construct()
     *
     * @param string $message The optional message to dispatch, if any.
     * @param \Exception $exception The optional exception if we want to dispatch it to the listener.
     */
    public function __construct($message = null, \Exception $exception = null)
    {
        $this->message = (string)$message;
        $this->exception = $exception;
    }

    /**
     * Sets a Response object to the event.
     *
     * @param Response $response
     * @return \PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent $this, For fluid method chaining
     */
    public function setResponse(Response &$response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Gets the linked Response object.
     *
     * @return \PrestaShop\PrestaShop\Core\Foundation\Routing\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a Request object to the event.
     *
     * @param Request $request
     * @return \PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent $this, For fluid method chaining
     */
    public function setRequest(Request &$request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Gets the linked Request object.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets a string containing a file path.
     *
     * @param string $filePath The file path, absolute.
     * @return \PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent $this, For fluid method chaining
     */
    public function setFilePath($filePath)
    {
        $this->filePath = (string)$filePath;
        return $this;
    }

    /**
     * Gets the file path linked with this event.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Sets the application Container to allow the listener access the service Container.
     *
     * If you do this, the listener will have access to all the services!
     *
     * @param \Core_Foundation_IoC_Container $container The application service container
     * @return \PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent $this, For fluid method chaining
     */
    public function setContainer(\Core_Foundation_IoC_Container &$container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Gets the application service container.
     *
     * @return \Core_Foundation_IoC_Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets the initial message of the event (optional).
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Gets the initial exception (that is the source of an event triggered in a catch block for example).
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
