<?php

/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Twig;

use PrestaShop\PrestaShop\Adapter\ClassLang;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Twig extension for the Symfony Asset component.
 *
 * @author Mlanawo Mbechezi <mlanawo.mbechezi@ikimea.com>
 */
class AdminExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface, \Twig_Extension_InitRuntimeInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var \Twig_Environment
     */
    private $environment;


    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * AdminExtension constructor.
     * @param RequestStack|null $requestStack
     * @param ContainerInterface $container
     */
    public function __construct(RequestStack $requestStack = null, ContainerInterface $container)
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    final private function buildTopNavMenu(ParameterBag $parameterBag)
    {
        static $tabDataContent = null;

        if (null === $tabDataContent) {
            $yamlParser = new Parser();
            $yamlNavigationPath = __DIR__ . '/../Resources/config/app/navigation.yml';
            $tabConfiguration = $yamlParser->parse(file_get_contents($yamlNavigationPath));
            $explodedControllerInfo = explode('::', $parameterBag->get('_controller'));
            $explodedControllerName = explode('\\', $explodedControllerInfo[0]);
            $controllerNameIndex = count($explodedControllerName) - 1;
            $controllerName = $explodedControllerName[$controllerNameIndex];

            $moduleManager = $this->container->get('prestashop.module.manager');

            if (isset($tabConfiguration[$controllerName])) {
                // Construct tabs and inject into twig tpl
                $tabDataContent = array();
                // Get current route name to know when to put "current" class on HTML dom
                $currentRouteName = $parameterBag->get('_route');

                $translator = $this->container->get('translator');
                $locale = $translator->getLocale();

                $tabMenu = (new ClassLang($locale))->getClassLang('TabLangCore');

                foreach ($tabConfiguration[$controllerName] as $tabName => $tabData) {
                    if (!empty($tabMenu)) {
                        $untranslated = $translator->getSourceString($tabData['title'], $tabMenu->getDomain());
                        $translatedField = $tabMenu->getFieldValue('name', $untranslated);
                        if (!empty($translatedField) && $translatedField != $tabData['title']) {
                            $tabData['title'] = $translatedField;
                        }
                    }

                    $tabData['isCurrent'] = false;
                    if ($currentRouteName === $tabData['route']) {
                        $tabData['isCurrent'] = true;
                    }

                    if ($tabName === 'module_tab_notifications') {
                        $tabData['notificationsCounter'] = $moduleManager->countModulesWithNotifications();
                    }

                    $tabDataContent[] = $this->environment->render(
                        'PrestaShopBundle:Admin/Common/_partials:_header_tab.html.twig',
                        array('tabData' => $tabData)
                    );
                }
                // Inject them to templating system as global to be able to pass it to the legacy afterwards and once
                // controller has given a response
            }
        }

        return $tabDataContent;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        $globals = array();

        if (null !== $this->requestStack->getCurrentRequest()) {
            $globals['headerTabContent'] = $this->buildTopNavMenu($this->requestStack->getCurrentRequest()->attributes);
        }

        return $globals;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twig_admin_extension';
    }
}
