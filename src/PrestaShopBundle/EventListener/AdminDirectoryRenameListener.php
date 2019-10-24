<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Security\AdminDirectoryNameGeneratorInterface;
use PrestaShop\PrestaShop\Core\Security\AdminDirectoryRenamerInterface;
use PrestaShop\PrestaShop\Core\Security\BackOfficeAccessPrerequisitesCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\Exception\UnableToRenameAdminDirectoryException;
use PrestaShopBundle\Controller\Admin\AuthorizationController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Event listener that checks if back office access prerequisites are satisfied.
 */
final class AdminDirectoryRenameListener
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var BackOfficeAccessPrerequisitesCheckerInterface
     */
    private $prerequisitesChecker;

    /**
     * @var AdminDirectoryRenamerInterface
     */
    private $adminDirectoryRenamer;

    /**
     * @var Tools
     */
    private $tools;
    /**
     * @var ControllerResolverInterface
     */
    private $controllerResolver;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var AdminDirectoryNameGeneratorInterface
     */
    private $adminDirectoryNameGenerator;

    /**
     * @param SessionInterface $session
     * @param BackOfficeAccessPrerequisitesCheckerInterface $prerequisitesChecker
     * @param AdminDirectoryRenamerInterface $adminDirectoryRenamer
     * @param AdminDirectoryNameGeneratorInterface $adminDirectoryNameGenerator
     * @param Tools $tools
     * @param ControllerResolverInterface $controllerResolver
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        SessionInterface $session,
        BackOfficeAccessPrerequisitesCheckerInterface $prerequisitesChecker,
        AdminDirectoryRenamerInterface $adminDirectoryRenamer,
        AdminDirectoryNameGeneratorInterface $adminDirectoryNameGenerator,
        Tools $tools,
        ControllerResolverInterface $controllerResolver,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->session = $session;
        $this->prerequisitesChecker = $prerequisitesChecker;
        $this->adminDirectoryRenamer = $adminDirectoryRenamer;
        $this->tools = $tools;
        $this->controllerResolver = $controllerResolver;
        $this->urlGenerator = $urlGenerator;
        $this->adminDirectoryNameGenerator = $adminDirectoryNameGenerator;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $controller = $this->controllerResolver->getController($event->getRequest());

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof AuthorizationController) {
            return;
        }

        if (!$this->prerequisitesChecker->defaultAdminDirectoryExists()) {
            return;
        }

        try {
            $newAdminDirectoryName = $this->adminDirectoryNameGenerator->generateRandomName();
            $this->adminDirectoryRenamer->rename($newAdminDirectoryName);

            $event->setResponse(new RedirectResponse(sprintf(
                '%s/%s%s',
                $this->tools->getShopDomainSsl(true),
                $newAdminDirectoryName,
                $this->urlGenerator->generate('_admin_login', [], UrlGeneratorInterface::RELATIVE_PATH)
            )));
        } catch (UnableToRenameAdminDirectoryException $e) {
            // If the admin directory could not be renamed - do nothing in the event listener.
        }
    }
}
