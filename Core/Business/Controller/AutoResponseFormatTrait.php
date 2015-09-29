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
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;

/**
 * Trait to generate default action settings (template name, response format) depending on the request.
 *
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
        // layout-mode is prior to accept header. Used by subcall for example, to force response mode
        if (isset($request->headers) && $request->headers->has('layout-mode')) {
            $response->setResponseFormat($request->headers->get('layout-mode'));
            return true;
        }

        // layout-mode is prior to accept header. Used by subcall for example, to force response mode
        if (isset($request->attributes) && $request->attributes->has('_layout_mode')) {
            $response->setResponseFormat($request->attributes->get('_layout_mode'));
            return true;
        }

        if (!isset($request->headers) || !$request->headers->has('accept')) {
            return true; // non blocking fail
        }
        $accepts = explode(',', $request->headers->get('accept'));

        // FIXME: Temporary behavior: Legacy controller used to set Layout title and setup smarty i18n ('l' function)
        if ($legacyController = $request->attributes->get('_legacy_path')) {
            $response->setLegacyControllerName($legacyController);
            $legacyContext = $this->container->make('Adapter_LegacyContext');
            $legacyContext->setupLegacyTranslationContext($legacyController);
        }

        // Order by HTTP accept values first, then by follwing switch cases order
        foreach ($accepts as $accept) {
            switch ($accept) {

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
        // no template if no HTML output!
        if (strpos($response->getResponseFormat(), 'html') !== false
            && isset($request->attributes)
            && $request->attributes->has('_controller_short')
        ) {
            $controllerString = $request->attributes->get('_controller_short');
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

            //If template was not defined, try to find it dynamically
            if (!$response->getTemplate()) {
                $templatePath = 'Core'.DIRECTORY_SEPARATOR.'Controller'.
                    DIRECTORY_SEPARATOR.$className.DIRECTORY_SEPARATOR. $methodName . '.' .
                    ($response->getEngineName() == 'smarty' ? 'tpl' : 'html.twig');

                $rootTemplatePath = _PS_THEME_DIR_;
                if (defined('_PS_ADMIN_DIR_')) {
                    $rootTemplatePath = _PS_BO_ALL_THEMES_DIR_ . 'default'.DIRECTORY_SEPARATOR.'template';
                }

                if (!file_exists($rootTemplatePath.DIRECTORY_SEPARATOR.$templatePath)) {
                    throw new DevelopmentErrorException('Template "'.$templatePath.'" could not be found');
                }

                $response->setTemplate($templatePath);
            }
        }

        return true; // non blocking fail
    }
}
