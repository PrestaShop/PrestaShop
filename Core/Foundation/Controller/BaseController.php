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
namespace PrestaShop\PrestaShop\Core\Foundation\Controller;

use Symfony\Component\Routing\RequestContext;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController
{
    const RESPONSE_LAYOUT_HTML = 'layout/html'; // encaspulate with a layout and call templating engine to format response.
    const RESPONSE_NUDE_HTML = 'nude/html'; // same as LAYOUT_HTML but with empty layout (<html>,<head>,<title>,<body>, only).
    const RESPONSE_AJAX_HTML = 'none/html'; // no layout and call templating engine to format response.
    const RESPONSE_RAW_TEXT = 'none/raw'; // no layout, no templating, no data transformation, direct controller action output
    const RESPONSE_XML = 'none/xml'; // no layout, no templating, transform response from array to XML
    const RESPONSE_JSON = 'none/json'; // no layout, transform response from array to json format
    const RESPONSE_NONE = 'none/none'; // no auto response output: case when action want to dump a file for example

    /**
     * This function will transform the resulting controller action content into various formats.
     * If you need a new one, you can override this function in your extended class. Don't forget to call
     * parent::formatResponse() in your own switch/default case.
     *
     * @param string $format
     * @param Response $response
     * @throws \ErrorException
     */
    public function formatResponse($format, Response &$response)
    {
        switch($format) {
            case 'html':
                $this->formatHtmlResponse($response);
                break;
            case 'json':
                $this->formatJsonResponse($response);
                break;
            case 'xml':
                throw new \ErrorException('Not yet supported!');
            case 'raw':
                return;
            case 'none':
                exit(0); // Break PHP process! Controller action should have already sent its result by itself (file, binary, etc...)
            default:
                throw new \ErrorException('Unknown format.');
        }
    }

    /**
     * This function will format data in an HTML result. Most of the time, you should use a template engine to render
     * the response. The data given by the controller action is available in $response->getContentData(), and once
     * you rendered the HTML content, you should put it in $response->setContent().
     *
     * @param Response $response
     */
    abstract protected function formatHtmlResponse(Response &$response);

    /**
     * This will format data from $response->getContentData() into JSON format.
     *
     * @param Response $response
     */
    protected final function formatJsonResponse(Response &$response)
    {
        $content = $response->getContentData();
        $configuration = \Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        $response->setContent(json_encode($content, $configuration->get('_PS_MODE_DEV_') ? JSON_PRETTY_PRINT : 0));
    }

    /**
     * This will choose the encapsulation function to execute.
     * If you need a new one, you can override this function in your extended class. Don't forget to call
     * parent::encapsulateResponse() in your own switch/default case.
     *
     * @param string $encapsulation
     * @param Response $response
     * @throws \ErrorException
     */
    public function encapsulateResponse($encapsulation, Response &$response)
    {
        switch($encapsulation) {
            case 'layout':
                $this->encapsulateLayout($response);
                break;
            case 'nude':
                $this->encapsulateNudeHtml($response);
                break;
            case 'none':
                return;
            default:
                throw new \ErrorException('Unknown encapsulation.');
        }
    }

    /**
     * This function will encapsulate an HTML content into a very smart HTML layout,
     * with the minimum required to be valid HTML document.
     * If you need more HTML stuff in this mode, override this function in your extended class.
     *
     * @param Response $response
     */
    protected function encapsulateNudeHtml(Response &$response)
    {
        $content = $response->getContent();
        $content = '<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="index, follow, all" />
        <title>Carrément a voir !</title>
    </head>
    <body>
'.$content.'
    </body>
</html>';
        $response->setContent($content);
    }

    /**
     * This function should encapsulate the content to display into an HTML layout (menu, headers, footers, etc...)
     * Implements it and use $response->getContent() to retrieve the main content.
     * Once you encapsulated the content in the layout, use $response->setContent() to store the result.
     *
     * @param Response $response
     */
    abstract protected function encapsulateLayout(Response &$response);

    /**
     * This will call all methods added by TRAITS on the Controller, with the function
     * name matching the $startingWith parameter.
     * The TRAIT function is triggered if:
     * - its name start with the $startWith parameter
     * - is public
     * - accepts a unique argument: the RequestContext instance
     *
     * This is triggered by Router before calling action ('beforeAction' parameter)
     * and after calling action ('afterAction' parameter).
     *
     * @param RequestContext $requestContext
     * @param string $startingWith
     * @return boolean True if all traits methods returned true too.
     */
    public final function callTraits(Request &$request, Response &$response, $startingWith = 'beforeAction')
    {
        // TODO : comment les ordonnancer?
        $res = true;
        foreach(class_uses($this) as $trait) {
            $methods = (new \ReflectionClass($trait))->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach($methods as $m) {
                if (strpos($m->name, $startingWith) === 0) {
                    $res = $res & $this->{$m->name}($request, $response);
                }
            }
        }
        return $res;
    }
}
