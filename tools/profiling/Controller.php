<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
