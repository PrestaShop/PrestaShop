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
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Business\Routing\RoutingService;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;

/**
 * This generates default action settings (template name, response format) depending on the request.
 *
 * This will add convenience hooks to search for data type to output, and the appropriate template engine
 * to use. The developer can always override these settings in the action code.
 */
final class AutoResponseFormatSetter extends ExecutionSequenceServiceWrapper
{
    /**
     * @var RoutingService
     */
    private $routingService;

    /**
     * @var \Adapter_LegacyContext
     */
    private $legacyContext;

    /**
     * Constructor.
     *
     * @param \Adapter_LegacyContext $legacyContext
     * @param RoutingService $routing
     */
    public function __construct(\Adapter_LegacyContext $legacyContext, RoutingService $routing)
    {
        $this->legacyContext = $legacyContext;
        $this->routingService = $routing;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getInitListeners()
     */
    public function getBeforeListeners()
    {
        return array(0 => array($this, 'suggestResponseFormat'));
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ExecutionSequenceServiceInterface::getCloseListeners()
     */
    public function getAfterListeners()
    {
        return array(0 => array($this, 'suggestTemplateFormat'));
    }

    /**
     * This helper will try to identify needed output format (HTML, w/o layout, xml, json, ...) via
     * HTTP request.
     *
     * @param BaseEvent $event
     */
    public function suggestResponseFormat(BaseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // layout-mode is prior to accept header. Used by subcall for example, to force response mode
        if (isset($request->headers) && $request->headers->has('layout-mode')) {
            $response->setResponseFormat($request->headers->get('layout-mode'));
            return;
        }

        // layout-mode is prior to accept header. Used by subcall for example, to force response mode
        if (isset($request->attributes) && $request->attributes->has('_layout_mode')) {
            $response->setResponseFormat($request->attributes->get('_layout_mode'));
            return;
        }

        if (!isset($request->headers) || !$request->headers->has('accept')) {
            return;
        }
        $accepts = explode(',', $request->headers->get('accept'));

        // FIXME: Temporary behavior: Legacy controller used to set Layout title and setup smarty i18n ('l' function)
        if ($legacyController = $request->attributes->get('_legacy_path')) {
            $response->setLegacyControllerName($legacyController);
            $this->legacyContext->setupLegacyTranslationContext($legacyController);
        }

        // Order by HTTP accept values first, then by follwing switch cases order
        foreach ($accepts as $accept) {
            switch ($accept) {

                case 'application/json':
                case 'text/javascript':
                    $response->setResponseFormat(BaseController::RESPONSE_JSON);
                    return;

                case 'text/html':
                case 'application/xhtml+xml':
                    $isXhr = $request->headers->has('x-requested-with') && ($request->headers->get('x-requested-with') == 'XMLHttpRequest');
                    $response->setResponseFormat($isXhr ? BaseController::RESPONSE_AJAX_HTML : BaseController::RESPONSE_LAYOUT_HTML);
                    return;

                case 'text/plain':
                    $response->setResponseFormat(BaseController::RESPONSE_RAW_TEXT);
                    return;

                case 'application/xml':
                    $response->setResponseFormat(Basecontroller::RESPONSE_XML);
                    return;

                default:
                    continue; // try next accept value given by HTTP request
            }
        }
    }

    /**
     * This trait helper will try to identify needed output format (HTML, w/o layout, xml, json, ...) via
     * HTTP request. If $response->getTemplateEngine() has already been set, then the helper do nothing.
     *
     * @param BaseEvent $event
     */
    public function suggestTemplateFormat(BaseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // no template if no HTML output!
        if (strpos($response->getResponseFormat(), 'html') !== false
            && isset($request->attributes)
            && $request->attributes->has('_controller_short')
        ) {
            $controllerString = $request->attributes->get('_controller_short');
            $path = explode('\\', $controllerString);
            if (count($path) < 2) {
                return;
            }

            $classMethod = array_pop($path); // extract last element (Class::method part)
            list($className, $methodName) = explode('::', $classMethod, 2);
            $matchCount = 0;

            $className = preg_replace('/Controller$/', '', $className, 1, $matchCount);
            if ($matchCount !== 1) {
                return; // Controller does not follow standard name pattern
            }

            $methodName = preg_replace('/Action$/', '', $methodName, 1, $matchCount);
            if ($matchCount !== 1) {
                return; // Action method does not follow standard name pattern
            }

            // if template was not defined, try to find it dynamically
            if (!$response->getTemplate()) {
                $templatePath = 'Core'.DIRECTORY_SEPARATOR.'Controller'.
                    DIRECTORY_SEPARATOR.$className.DIRECTORY_SEPARATOR. $methodName . '.' .
                    ($response->getEngineName() == 'smarty' ? 'tpl' : 'html.twig');

                $rootTemplatePath = _PS_THEME_DIR_;
                if (defined('_PS_ADMIN_DIR_')) {
                    $rootTemplatePath = _PS_BO_ALL_THEMES_DIR_ . 'default'.DIRECTORY_SEPARATOR.'template';
                }

                if (!file_exists($rootTemplatePath.DIRECTORY_SEPARATOR.$templatePath)) {
                    // The action did not set the template name, and we could not find it either...
                    throw new DevelopmentErrorException('Template "'.$templatePath.'" could not be found');
                }

                $response->setTemplate($templatePath);
            }
        }
    }
}
