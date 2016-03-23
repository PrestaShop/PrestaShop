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
namespace PrestaShopBundle\Service\Listeners;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Routing\Router;

class AdminControllersListener
{
    protected $request;
    protected $router;
    protected $renderer;
    protected $translator;

    public function __construct(Router $router, \Twig_Environment $twig, $translator)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->renderer = $twig;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->request = $event->getRequest();
        // @TODO: Check if we have to do any check by:
        //  - Check whether it's an Action Controller method
        //  - Check that we are not on an XMLHttpRequest, but a classic one
        $this->buildTopNavMenu();
    }

    final private function buildTopNavMenu()
    {
        $yamlParser = new Parser();
        $yamlNavigationPath = __DIR__.'/../../Resources/config/admin/navigation.yml';
        $tabConfiguration = $yamlParser->parse(file_get_contents($yamlNavigationPath));
        $explodedControllerInfo = explode('::', $this->request->attributes->get('_controller'));
        $explodedControllerName = explode('\\', $explodedControllerInfo[0]);
        $controllerNameIndex = count($explodedControllerName) - 1;
        $controllerName = $explodedControllerName[$controllerNameIndex];

        if (isset($tabConfiguration[$controllerName])) {
            // Construct tabs and inject into twig tpl
            $tabDataContent = [];
            // Get current route name to know when to put "current" class on HTML dom
            $currentRouteName = $this->request->get('_route');

            foreach ($tabConfiguration[$controllerName] as $tabName => $tabData) {
                $tabData['isCurrent'] = false;
                if ($currentRouteName === $tabData['route']) {
                    $tabData['isCurrent'] = true;
                }
                $tabData['title'] = $this->translator->trans($tabData['title'], [], 'AdminControllersListener');
                $tabData['route'] = $this->router->generate($tabData['route']);
                $tabDataContent[] = $this->renderer->render(
                    'PrestaShopBundle:Admin/Common/_partials:_header_tab.html.twig',
                    ['tabData' => $tabData]
                );
            }
            // Inject them to templating system as global to be able to pass it to the legacy afterwards and once
            // controller has given a response
            $this->renderer->addGlobal('headerTabContent', $tabDataContent);
        }
    }
}
