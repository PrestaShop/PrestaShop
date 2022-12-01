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
abstract class Controller extends ControllerCore
{
    protected $profiler = null;

    /**
     * @var string|null
     */
    public $outPutHtml;

    public function __construct()
    {
        $this->profiler = Profiler::getInstance();
        $this->profiler->stamp('config');

        parent::__construct();
        $this->profiler->stamp('__construct');
    }

    public function run()
    {
        $this->init();
        $this->profiler->stamp('init');

        if ($this->checkAccess()) {
            $this->profiler->stamp('checkAccess');

            if (!$this->content_only && ($this->display_header || !empty($this->className))) {
                $this->setMedia();
                $this->profiler->stamp('setMedia');
            }

            $this->postProcess();
            $this->profiler->stamp('postProcess');

            if (!$this->content_only && ($this->display_header || !empty($this->className))) {
                $this->initHeader();
                $this->profiler->stamp('initHeader');
            }

            $this->initContent();
            $this->profiler->stamp('initContent');

            if (!$this->content_only && ($this->display_footer || !empty($this->className))) {
                $this->initFooter();
                $this->profiler->stamp('initFooter');
            }

            if ($this->ajax) {
                $action = Tools::toCamelCase(Tools::getValue('action'), true);

                if (!empty($action) && method_exists($this, 'displayAjax' . $action)) {
                    $this->{'displayAjax' . $action}();
                } elseif (method_exists($this, 'displayAjax')) {
                    $this->displayAjax();
                }

                return;
            }
        } else {
            $this->initCursedPage();
        }

        echo $this->displayProfiling();
    }

    /**
     * Display profiling
     * If it's a migrated page, we change the outPutHtml content, otherwise
     * we display the profiling at the end of the page.
     *
     * @return string
     */
    public function displayProfiling(): string
    {
        $content = '';
        if (!empty($this->redirect_after)) {
            $this->context->smarty->assign(
                [
                    'redirectAfter' => $this->redirect_after,
                ]
            );
            $content .= $this->context->smarty->fetch(__DIR__ . '/templates/redirect.tpl');
        } else {
            // Call original display method
            ob_start();
            $this->display();
            $displayOutput = ob_get_clean();
            if (empty($displayOutput) && isset($this->outPutHtml)) {
                $displayOutput = $this->outPutHtml;
            }

            $content .= $displayOutput;
            $this->profiler->stamp('display');
        }

        // Process all profiling data
        $this->profiler->processData();

        // Add some specific style for profiling information

        $this->context->smarty->assign(
            $this->profiler->getSmartyVariables()
        );

        if (strpos($content, '{$content}') === false) {
            return str_replace(
                '</html>',
                $this->context->smarty->fetch(__DIR__ . '/templates/profiling.tpl') . '</html>',
                $content
            );
        }

        if (isset($this->outPutHtml)) {
            $this->outPutHtml = str_replace(
                '{$content}',
                '{$content}' . $this->context->smarty->fetch(__DIR__ . '/templates/profiling.tpl'),
                $content
            );
        }

        // Return empty string since we change the outPutHtml
        return '';
    }
}
