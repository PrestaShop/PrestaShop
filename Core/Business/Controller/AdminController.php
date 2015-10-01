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

use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Business\Routing\AdminRouter;

/**
 * Base class for all Admin controllers.
 *
 * Others won't be accepted by AdminRouter.
 * You must extends this one, and use traits that you need.
 * For more explanations about action functions normalization, please read:
 * @see PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController
 */
class AdminController extends BaseController
{
    /**
     * @var boolean
     */
    private $constructorCalled = false;

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController::__construct()
     */
    public function __construct(AdminRouter $router, Container $container)
    {
        parent::__construct($router, $container);
        $this->registerExecutionSequenceService($container->make('PrestaShop\\PrestaShop\\Core\\Business\\Controller\\ExecutionSequenceService\\AuthenticationMiddleware'));
        $this->constructorCalled = true;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerInterface::isConstructionStrategyChecked()
     */
    final public function isConstructionStrategyChecked()
    {
        return parent::isConstructionStrategyChecked() && $this->constructorCalled;
    }

    /**
     * This function should encapsulate the content to display into an HTML layout (menu, headers, footers, etc...).
     *
     * {@inheritdoc}
     *
     * @param Response $response
     */
    protected function encapsulateLayout(Response &$response)
    {
        // Display catched WarningExceptions (not catched one will fail display and will be catched by Router->dispatch())
        $warningBlock = '';
        if ($this->getWarningIterator() && $this->getWarningIterator()->count()) {
            $warningBlock = $response->getTemplateEngine($this->container)->view->fetch('Core/system_messages.tpl', array(
                'exceptions' => $this->getWarningIterator(),
                'color' => 'orange'
            ));
        }
        // Display notices and success messages
        $noticeBlock = '';
        if ($this->getInfoIterator() && $this->getInfoIterator()->count()) {
            $noticeBlock = $response->getTemplateEngine($this->container)->view->fetch('Core/user_messages.tpl', array(
                'messages' => $this->getInfoIterator(),
                'color' => 'blue'
            ));
        }
        if ($this->getSuccessIterator() && $this->getSuccessIterator()->count()) {
            $noticeBlock .= $response->getTemplateEngine($this->container)->view->fetch('Core/user_messages.tpl', array(
                'messages' => $this->getSuccessIterator(),
                'color' => 'green'
            ));
        }

        //GET LAYOUT FROM ORIGINAL CONTROLLER REQUESTED
        $originCtrl = new \AdminLegacyLayoutControllerCore($response->getLegacyControllerName(), $response->getTitle(), $response->getHeaderToolbarBtn(), $response->getDisplayType(), $response->getJs(), $response->getCss());
        $originCtrl->run();

        $link = new \Link();
        $content = str_replace(
            array(
                '{$content}',
                'var currentIndex = \'index.php\';'),
            array(
                $warningBlock.$noticeBlock.$response->getContent(),
                'var currentIndex = \''.$link->getAdminLink($response->getLegacyControllerName()).'\';'),
            $originCtrl->outPutHtml
        );

        $response->setContent($content);
    }

    /**
     * This function will format data in an HTML result.
     *
     * {@inheritdoc}
     *
     * @param Response $response
     */
    protected function formatHtmlResponse(Response &$response)
    {
        $templateEngine = $response->getTemplateEngine($this->container);
        $response->setContent($templateEngine->view->fetch($response->getTemplate(), $response->getContentData()));
    }
}
