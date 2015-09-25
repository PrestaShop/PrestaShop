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
    use AdminAuthenticationTrait; // All AdminController subclasses should be protected by authentication middleware.
    use AdminCommonActionTrait; // describe common actions for the Admin Interface

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
        $originCtrl = new \AdminLegacyLayoutControllerCore($response->getLegacyControllerName(), $response->getTitle(), $response->getHeaderToolbarBtn(), $response->getDisplayType(), $response->getJs());
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
    
    /**
     * Is authentication required to use the corresponding actions?
     *
     * {@inheritdoc}
     *
     * Override this in your controller if you want to allow anonymous users to call it.
     * For example, for Login controllers, should be overriden to return false.
     * Warning: if you return false on an Admin controller subclass, then the corresponding actions
     * will be wide opened to anonymous connections.
     *
     * @return boolean True if authenticated user is needed. False if the controller can be called by anonymous users.
     */
    protected function isAuthenticationNeeded()
    {
        return true;
    }
}
