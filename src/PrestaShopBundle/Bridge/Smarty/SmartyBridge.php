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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Bridge\Smarty;

use Cookie;
use HelperShop;
use Link;
use Media;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use PrestaShopBundle\Bridge\Exception\BridgeException;
use Smarty;
use Symfony\Component\HttpFoundation\Response;
use Tools;

/**
 * This class is used to put all needed variable in the Smarty object,
 * and to render smarty as a symfony response.
 */
class SmartyBridge
{
    public const LAYOUT = 'layout.tpl';

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var ConfiguratorInterface[]
     */
    private $configurators;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param LegacyContext $legacyContext
     * @param Configuration $configuration
     * @param ConfiguratorInterface[] $configurators
     */
    public function __construct(
        LegacyContext $legacyContext,
        Configuration $configuration,
        iterable $configurators
    ) {
        $this->assertConfiguratorsType($configurators);
        $this->configurators = $configurators;
        $this->configuration = $configuration;
        $this->smarty = $legacyContext->getSmarty();
        $this->link = $legacyContext->getContext()->link;
        $this->cookie = $legacyContext->getContext()->cookie;
    }

    /**
     * @param string $content
     * @param ControllerConfiguration $controllerConfiguration
     * @param Response|null $response
     *
     * @return Response
     */
    public function render(
        string $content,
        ControllerConfiguration $controllerConfiguration,
        Response $response = null
    ): Response {
        foreach ($this->configurators as $configurator) {
            $configurator->configure($controllerConfiguration);
        }

        $this->smarty->assign($controllerConfiguration->templateVars);

        if ($response === null) {
            $response = new Response();
        }

        $response->headers->set('Cache-Control', 'no-store, no-cache');

        return $response->setContent($this->display($controllerConfiguration, $content, self::LAYOUT));
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     * @param string $content
     * @param string $templateName
     *
     * @return string
     */
    private function display(ControllerConfiguration $controllerConfiguration, string $content, $templateName): string
    {
        $helperShop = new HelperShop();
        $this->smarty->assign([
            'content' => $content,
            'display_header' => $controllerConfiguration->displayHeader,
            'display_header_javascript' => $controllerConfiguration->displayHeaderJavascript,
            'display_footer' => $controllerConfiguration->displayFooter,
            'js_def' => Media::getJsDef(),
            'toggle_navigation_url' => $this->link->getAdminLink('AdminEmployees', true, [], [
                'action' => 'toggleMenu',
            ]),
            'shop_list' => $helperShop->getRenderedShopList(),
        ]);

        if (!$controllerConfiguration->metaTitle) {
            $controllerConfiguration->metaTitle = $controllerConfiguration->toolbarTitle;
        }
        $this->smarty->assign(
            'meta_title',
            strip_tags(implode(' ' . $this->configuration->get('PS_NAVIGATION_PIPE') . ' ', $controllerConfiguration->metaTitle))
        );

        $template_dirs = $this->smarty->getTemplateDir() ?: [];

        // Check if header/footer have been overridden
        $dir = $this->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . trim($controllerConfiguration->templateFolder, '\\/') . DIRECTORY_SEPARATOR;
        $module_list_dir = $this->smarty->getTemplateDir(0) . 'helpers' . DIRECTORY_SEPARATOR . 'modules_list' . DIRECTORY_SEPARATOR;

        $header_tpl = file_exists($dir . 'header.tpl') ? $dir . 'header.tpl' : 'header.tpl';
        $page_header_toolbar = file_exists($dir . 'page_header_toolbar.tpl') ? $dir . 'page_header_toolbar.tpl' : 'page_header_toolbar.tpl';
        $footer_tpl = file_exists($dir . 'footer.tpl') ? $dir . 'footer.tpl' : 'footer.tpl';
        $modal_module_list = file_exists($module_list_dir . 'modal.tpl') ? $module_list_dir . 'modal.tpl' : '';
        $tpl_action = $controllerConfiguration->templateFolder . $controllerConfiguration->displayType . '.tpl';

        // Check if action template has been overridden
        foreach ($template_dirs as $template_dir) {
            if (file_exists($template_dir . DIRECTORY_SEPARATOR . $tpl_action) && $controllerConfiguration->displayType != 'view' && $controllerConfiguration->displayType != 'options') {
                if (method_exists($this, $controllerConfiguration->displayType . Tools::toCamelCase($controllerConfiguration->legacyControllerName))) {
                    $this->{$controllerConfiguration->displayType . Tools::toCamelCase($controllerConfiguration->legacyControllerName)}();
                }
                $this->smarty->assign('content', $this->smarty->fetch($tpl_action));

                break;
            }
        }

        $template = $this->createTemplate($controllerConfiguration, $controllerConfiguration->template);
        $page = $template->fetch();

        /* @see ControllerConfiguration::$errors */
        /* @see ControllerConfiguration::$warnings */
        /* @see ControllerConfiguration::$informations */
        /* @see ControllerConfiguration::$confirmations */
        foreach (['errors', 'warnings', 'informations', 'confirmations'] as $type) {
            if (!is_array($controllerConfiguration->$type)) {
                $controllerConfiguration->$type = (array) $controllerConfiguration->$type;
            }
            $this->smarty->assign($type, $controllerConfiguration->json ? json_encode(array_unique($controllerConfiguration->$type)) : array_unique($controllerConfiguration->$type));
        }

        if ($controllerConfiguration->showPageHeaderToolbar && !$controllerConfiguration->liteDisplay) {
            $this->smarty->assign(
                [
                    'page_header_toolbar' => $this->smarty->fetch($page_header_toolbar),
                ]
            );
            if (!empty($modal_module_list)) {
                $this->smarty->assign(
                    [
                        'modal_module_list' => $this->smarty->fetch($modal_module_list),
                    ]
                );
            }
        }

        $this->smarty->assign('baseAdminUrl', __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/');

        $this->smarty->assign(
            [
                'page' => $controllerConfiguration->json ? json_encode($page) : $page,
                'header' => $this->smarty->fetch($header_tpl),
                'footer' => $this->smarty->fetch($footer_tpl),
                'modal' => $this->renderModal($controllerConfiguration),
            ]
        );

        return $this->smartyOutputContent($templateName);
    }

    /**
     * Create a template from the override file, else from the base file.
     *
     * @param ControllerConfiguration $controllerConfiguration
     * @param string $templateName
     *
     * @return \Smarty_Internal_Template
     */
    private function createTemplate(ControllerConfiguration $controllerConfiguration, $templateName)
    {
        if ($controllerConfiguration->templateFolder) {
            if (!$this->configuration->get('PS_DISABLE_OVERRIDES') && file_exists($this->smarty->getTemplateDir(1) . DIRECTORY_SEPARATOR . $controllerConfiguration->templateFolder . $templateName)) {
                return $this->smarty->createTemplate($controllerConfiguration->templateFolder . $templateName, $this->smarty);
            } elseif (file_exists($this->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . $controllerConfiguration->templateFolder . $templateName)) {
                return $this->smarty->createTemplate('controllers' . DIRECTORY_SEPARATOR . $controllerConfiguration->templateFolder . $templateName, $this->smarty);
            }
        }

        return $this->smarty->createTemplate($this->smarty->getTemplateDir(0) . $templateName, $this->smarty);
    }

    /**
     * Renders controller templates and generates page content.
     *
     * @param array|string $templates Template file(s) to be rendered
     */
    private function smartyOutputContent($templates): string
    {
        $this->cookie->write();

        $js_tag = 'js_def';
        $this->smarty->assign($js_tag, $js_tag);

        if (!is_array($templates)) {
            $templates = [$templates];
        }

        $html = '';
        foreach ($templates as $template) {
            $html .= $this->smarty->fetch($template);
        }

        return trim($html);
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
                $this->smarty->assign($modal);
                $modalRender .= $this->smarty->fetch('modal.tpl');
            }
        }

        return $modalRender;
    }

    /**
     * Validates the type of configurators
     *
     * @param iterable $configurators
     *
     * @return void
     *
     * @throws BridgeException
     */
    private function assertConfiguratorsType(iterable $configurators): void
    {
        foreach ($configurators as $configurator) {
            if ($configurator instanceof ConfiguratorInterface) {
                continue;
            }

            throw new BridgeException(
                sprintf(
                    '%s expected, but got %s',
                    ConfiguratorInterface::class,
                    var_export($configurator, true)
                )
            );
        }
    }
}
