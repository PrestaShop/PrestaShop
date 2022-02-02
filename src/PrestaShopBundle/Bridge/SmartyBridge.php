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

namespace PrestaShopBundle\Bridge;

use \Configuration;
use \Media;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use \Smarty;
use Symfony\Component\HttpFoundation\Response;
use \Tools;

/**
 * This class is used to render smarty as a symfony response
 */
class SmartyBridge
{
    public const LAYOUT = 'layout.tpl';

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var BreadcrumbsAndTitleHydrator
     */
    private $breadcrumbsAndTitleHydrator;

    /**
     * @var FooterHydrator
     */
    private $footerHydrator;

    /**
     * @var HeaderHydrator
     */
    private $headerHydrator;

    /**
     * @var NotificationsHydrator
     */
    private $notificationHydrator;

    /**
     * @var ToolbarFlagsHydrator
     */
    private $toolbarFlagsHydrator;

    /**
     * @var SmartyVarsAssigner
     */
    private $smartyVarsAssigner;

    public function __construct(
        LegacyContext $legacyContext,
        BreadcrumbsAndTitleHydrator $breadcrumbsAndTitleHydrator,
        FooterHydrator $footerHydrator,
        HeaderHydrator $headerHydrator,
        NotificationsHydrator $notificationHydrator,
        ToolbarFlagsHydrator $toolbarFlagsHydrator,
        SmartyVarsAssigner $smartyVarsAssigner
    )
    {
        $this->smarty = $legacyContext->getSmarty();
        $this->breadcrumbsAndTitleHydrator = $breadcrumbsAndTitleHydrator;
        $this->footerHydrator = $footerHydrator;
        $this->headerHydrator = $headerHydrator;
        $this->notificationHydrator = $notificationHydrator;
        $this->toolbarFlagsHydrator = $toolbarFlagsHydrator;
        $this->smartyVarsAssigner = $smartyVarsAssigner;
    }

    public function render(string $content, ControllerConfiguration $controllerConfiguration, $template = self::LAYOUT): Response
    {
        $this->breadcrumbsAndTitleHydrator->hydrate($controllerConfiguration);
        $this->notificationHydrator->hydrate($controllerConfiguration);
        $this->toolbarFlagsHydrator->hydrate($controllerConfiguration);
        //@Todo handle this later
        //$this->setMedia();
        $this->headerHydrator->hydrate($controllerConfiguration);
        $this->footerHydrator->hydrate($controllerConfiguration);
        $this->smartyVarsAssigner->assign($controllerConfiguration->templatesVars);

        return new Response($this->display($controllerConfiguration, $content, $template));
    }

    public function display(ControllerConfiguration $controllerConfiguration, string $content, $templateName): string
    {
        $this->smarty->assign([
            'content' => $content,
            'display_header' => $controllerConfiguration->displayHeader,
            'display_header_javascript' => $controllerConfiguration->displayHeaderJavascript,
            'display_footer' => $controllerConfiguration->displayFooter,
            'js_def' => Media::getJsDef(),
            'toggle_navigation_url' => $controllerConfiguration->link->getAdminLink('AdminEmployees', true, [], [
                'action' => 'toggleMenu',
            ]),
        ]);

        // Use page title from metaTitle if it has been set else from the breadcrumbs array
        if (!$controllerConfiguration->metaTitle) {
            $controllerConfiguration->metaTitle = $controllerConfiguration->toolbarTitle;
        }
        $this->smarty->assign(
            'meta_title',
            strip_tags(implode(' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ', $controllerConfiguration->metaTitle))
        );

        $template_dirs = $this->smarty->getTemplateDir() ?: [];

        // Check if header/footer have been overridden
        $dir = $this->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . trim($controllerConfiguration->folderTemplate, '\\/') . DIRECTORY_SEPARATOR;
        $module_list_dir = $this->smarty->getTemplateDir(0) . 'helpers' . DIRECTORY_SEPARATOR . 'modules_list' . DIRECTORY_SEPARATOR;

        $header_tpl = file_exists($dir . 'header.tpl') ? $dir . 'header.tpl' : 'header.tpl';
        $page_header_toolbar = file_exists($dir . 'page_header_toolbar.tpl') ? $dir . 'page_header_toolbar.tpl' : 'page_header_toolbar.tpl';
        $footer_tpl = file_exists($dir . 'footer.tpl') ? $dir . 'footer.tpl' : 'footer.tpl';
        $modal_module_list = file_exists($module_list_dir . 'modal.tpl') ? $module_list_dir . 'modal.tpl' : '';
        //@Todo later handle different view now just handle list
        $tpl_action = $controllerConfiguration->folderTemplate . $controllerConfiguration->display . '.tpl';

        // Check if action template has been overridden
        foreach ($template_dirs as $template_dir) {
            if (file_exists($template_dir . DIRECTORY_SEPARATOR . $tpl_action) && $controllerConfiguration->display != 'view' && $controllerConfiguration->display != 'options') {
                if (method_exists($this, $controllerConfiguration->display . Tools::toCamelCase($controllerConfiguration->controllerNameLegacy))) {
                    $this->{$controllerConfiguration->display . Tools::toCamelCase($controllerConfiguration->controllerNameLegacy)}();
                }
                $this->smarty->assign('content', $this->smarty->fetch($tpl_action));

                break;
            }
        }

        //if (!$this->ajax) {
        $template = $this->createTemplate($controllerConfiguration, $controllerConfiguration->template);
        $page = $template->fetch();

        //} else {
        //    $page = $this->content;
        //}

        //@Todo Handle later
        //if ($conf = Tools::getValue('conf')) {
        //    $this->smarty->assign('conf', $this->json ? json_encode($this->_conf[(int) $conf]) : $this->_conf[(int) $conf]);
        //}

        //@Todo Handle later
        //if ($error = Tools::getValue('error')) {
        //    $this->smarty->assign('error', $this->json ? json_encode($this->_error[(int) $error]) : $this->_error[(int) $error]);
        //}

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
            ]
        );

        return $this->smartyOutputContent($controllerConfiguration, $templateName);
    }

    /**
     * Create a template from the override file, else from the base file.
     *
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return \Smarty_Internal_Template
     */
    private function createTemplate(ControllerConfiguration $controllerConfiguration, $templateName)
    {
        if ($controllerConfiguration->folderTemplate) {
            if (!Configuration::get('PS_DISABLE_OVERRIDES') && file_exists($this->smarty->getTemplateDir(1) . DIRECTORY_SEPARATOR . $controllerConfiguration->folderTemplate . $templateName)) {
                return $this->smarty->createTemplate($controllerConfiguration->folderTemplate . $templateName, $this->smarty);
            } elseif (file_exists($this->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . $controllerConfiguration->folderTemplate . $templateName)) {
                return $this->smarty->createTemplate('controllers' . DIRECTORY_SEPARATOR . $controllerConfiguration->folderTemplate . $templateName, $this->smarty);
            }
        }

        return $this->smarty->createTemplate($this->smarty->getTemplateDir(0) . $templateName, $this->smarty);
    }

    /**
     * Renders controller templates and generates page content.
     *
     * @param array|string $templates Template file(s) to be rendered
     */
    private function smartyOutputContent(ControllerConfiguration $controllerConfiguration, $templates): string
    {
        $controllerConfiguration->cookie->write();

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
}
