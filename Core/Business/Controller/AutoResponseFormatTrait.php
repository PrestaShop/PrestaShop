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
namespace PrestaShop\PrestaShop\Core\Business\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;

/**
 * This Trait will add convenience hooks to search for data type to output, and the appropriate template engine
 * to use.
 */
trait AutoResponseFormatTrait
{
    /**
     * This trait helper will try to identify needed output format (HTML, w/o layout, xml, json, ...) via
     * HTTP request.
     *
     * @param Request $request
     * @param Response $response
     * @return boolean True if success; False to forbid action execution
     */
    public function beforeActionSuggestResponseFormat(Request &$request, Response &$response)
    {
        if (!isset($request->headers) || !$request->headers->has('accept')) {
            return true; // non blocking fail
        }
        $accepts = explode(',', $request->headers->get('accept'));

        // Order by HTTP accept values first, then by follwing switch cases order
        foreach($accepts as $accept) {
            switch($accept) {

                case 'application/json':
                case 'text/javascript':
                    $response->setResponseFormat(BaseController::RESPONSE_JSON);
                    return true;

                case 'text/html':
                case 'application/xhtml+xml':
                    $isXhr = $request->headers->has('x-requested-with') && ($request->headers->get('x-requested-with') == 'XMLHttpRequest');
                    $response->setResponseFormat($isXhr ? BaseController::RESPONSE_AJAX_HTML : BaseController::RESPONSE_LAYOUT_HTML);
                    return true;

                case 'text/plain':
                    $response->setResponseFormat(Basecontroller::RESPONSE_RAW_TEXT);
                    return true;

                case 'application/xml':
                    $response->setResponseFormat(Basecontroller::RESPONSE_XML);
                    return true;

                default:
                    continue; // try next accept value given by HTTP request
            }
        }
        return true; // non blocking fail
    }

    /**
     * This trait helper will try to identify needed output format (HTML, w/o layout, xml, json, ...) via
     * HTTP request. If $response->getTemplateEngine() has already been set, then the helper do nothing.
     *
     * @param Request $request
     * @param Response $response
     * @return boolean True if success; False to forbid action execution
     */
    public function afterActionSuggestTemplateFormat(Request &$request, Response &$response)
    {
        if ($response->getTemplateEngine()) {
            return true; // already set by controller action.
        }
        
        // no template if no HTML output!
        if (strpos($response->getResponseFormat(), 'html') !== false
            && isset($request->attributes)
            && $request->attributes->has('_controller')
        ) {
            $controllerString = $request->attributes->get('_controller');
            $path = explode('\\', $controllerString);
            if (count($path) < 2) {
                return true; // not enough info to suggest template
            }
            
            $classMethod = array_pop($path); // extract last element (Class::method part)
            list($className, $methodName) = explode('::', $classMethod, 2);
            $matchCount = 0;
            
            $className = preg_replace('/Controller$/', '', $className, 1, $matchCount);
            if ($matchCount !== 1) {
                return true; // Controller does not follow standard name pattern
            }
            
            $methodName = preg_replace('/Action$/', '', $methodName, 1, $matchCount);
            if ($matchCount !== 1) {
                return true; // Action method does not follow standard name pattern
            }
            
            // TODO : from here, plug template engine.
            $response->setTemplateEngine(function(array $contentData) use($path, $className, $methodName) {
                return 'Ici, appeler le template et son moteur, avec çà : '
                    .implode('/',$path).'/'.$className.'/'.$methodName.'.tpl'
                    .'<br/>'.print_r($contentData, true); // FIXME
            });
        }
        
        
        return true; // non blocking fail
    }
}
