<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Extension;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\Smarty\ConfiguratorInterface;
use PrestaShopBundle\Bridge\SymfonyLayoutFeature;
use PrestaShopBundle\EventListener\ContextShopListener;
use Smarty;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * Handles twig variables for pure symfony layout (that replaces the old legacy layout),
 * it is tightly coupled with ContextShopListener which is responsible for creating the ControllerConfiguration
 * and set the ControllerBridge on Context->controller, the controller configuration is then accessible via a request
 * attribute.
 *
 * These components/listeners were initially created for horizontal migration (which has been
 * abandoned since) but it turns out they fit the need we have for Symfony layout especially
 * handling most of the variables previously initialized by AdminLegacyLayoutController.
 */
class SymfonyLayoutExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var LegacyContext */
    private $context;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ConfiguratorInterface[]
     */
    private $configurators;

    /**
     * @var SymfonyLayoutFeature
     */
    private $symfonyLayoutFeature;

    public function __construct(
        LegacyContext $context,
        RequestStack $requestStack,
        iterable $configurators,
        SymfonyLayoutFeature $symfonyLayoutFeature
    ) {
        $this->context = $context;
        $this->requestStack = $requestStack;
        $this->configurators = $configurators;
        $this->symfonyLayoutFeature = $symfonyLayoutFeature;
    }

    public function getGlobals(): array
    {
        $useSymfonyLayout = $this->symfonyLayoutFeature->isEnabled();
        $layoutVariables = [];

        if ($this->symfonyLayoutFeature->isEnabled()) {
            $request = $this->requestStack->getCurrentRequest();
            $controllerConfiguration = $request->attributes->get(ContextShopListener::CONTROLLER_CONFIGURATION_ATTRIBUTE);
            if ($controllerConfiguration instanceof ControllerConfiguration) {
                foreach ($this->configurators as $configurator) {
                    $configurator->configure($controllerConfiguration);
                }
                $layoutVariables = $controllerConfiguration->templateVars + $this->renderSmartyContent($controllerConfiguration);
            }
        }

        return $layoutVariables + [
            'use_legacy_layout' => !$useSymfonyLayout,
            'use_symfony_layout' => $useSymfonyLayout,
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('renderSmarty', [$this, 'renderSmarty'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @internal
     * @experimental
     *
     * This twig function is used to render a smarty component template.
     * Note: it is used temporarily and only as internal usage while we are performing
     * the Symfony layout migration. It will be removed when it's not useful anymore and
     * this will not be counted as a breaking change.
     *
     * {{ renderSmarty('components/layout/search_form.tpl', {
     *   'baseAdminUrl': baseAdminUrl,
     *   'bo_query': bo_query,
     * }) }}
     *
     * @param string $smartyTemplate
     * @param array $smartyVariables
     *
     * @return string
     */
    public function renderSmarty(string $smartyTemplate, array $smartyVariables = []): string
    {
        $smarty = $this->getSmarty();
        $smarty->setTemplateDir([
            _PS_BO_ALL_THEMES_DIR_ . 'new-theme' . DIRECTORY_SEPARATOR . 'template',
            _PS_OVERRIDE_DIR_ . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates',
        ]);
        $smarty->assign($smartyVariables);

        return $smarty->fetch($smartyTemplate);
    }

    private function renderSmartyContent(ControllerConfiguration $controllerConfiguration): array
    {
        $smarty = $this->context->getSmarty();
        $smarty->assign($controllerConfiguration->templateVars);
        $smarty->setTemplateDir([
            _PS_BO_ALL_THEMES_DIR_ . 'new-theme' . DIRECTORY_SEPARATOR . 'template',
            _PS_OVERRIDE_DIR_ . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates',
        ]);

        $module_list_dir = $smarty->getTemplateDir(0) . 'helpers' . DIRECTORY_SEPARATOR . 'modules_list' . DIRECTORY_SEPARATOR;
        $modal_module_list = file_exists($module_list_dir . 'modal.tpl') ? $module_list_dir . 'modal.tpl' : '';
        if ($controllerConfiguration->showPageHeaderToolbar && !$controllerConfiguration->liteDisplay) {
            if (!empty($modal_module_list)) {
                $controllerConfiguration->templateVars['modal_module_list'] = $smarty->fetch($modal_module_list);
            }
        }

        return [
            'modal' => $this->renderModal($controllerConfiguration),
        ];
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return string
     */
    private function renderModal(ControllerConfiguration $controllerConfiguration): string
    {
        $modalRender = '';
        if (is_array($controllerConfiguration->modals) && count($controllerConfiguration->modals)) {
            foreach ($controllerConfiguration->modals as $modal) {
                $this->getSmarty()->assign($modal);
                $modalRender .= $this->getSmarty()->fetch('modal.tpl');
            }
        }

        return $modalRender;
    }

    private function getSmarty(): Smarty
    {
        return $this->context->getSmarty();
    }
}
