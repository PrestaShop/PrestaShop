<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\EventListener;

use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Allow a redirection to the right url when using BetterSecurity annotation.
 */
class DemoModeEnabledListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var bool
     */
    private $isDemoModeEnabled;

    /**
     * DemoModeEnabledListener constructor.
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param SessionInterface $session
     * @param $isDemoModeEnabled
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator, SessionInterface $session, $isDemoModeEnabled)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->session = $session;
        $this->isDemoModeEnabled = $isDemoModeEnabled;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest() || !$this->isDemoModeEnabled) {
            return;
        }

        if (!$demoConfigurations = $event->getRequest()->attributes->get('_demo_restricted')) {
            return;
        }

        foreach ($demoConfigurations as $demoConfiguration) {
            if (!$demoConfiguration instanceof DemoRestricted) {
                continue;
            }

            $this->throwNotificationMessage($demoConfiguration);
            $url = $this->router->generate($demoConfiguration->getRoute());

            $event->setController(function () use ($url){
                return new RedirectResponse($url);
            });

            return;
        }
    }

    /**
     * Send an error message when redirected, will only work on migrated pages.
     *
     * @param AdminSecurity $demoRestricted
     */
    private function throwNotificationMessage(DemoRestricted $demoRestricted)
    {
        $this->session->getFlashBag()->add(
            'error',
            $this->translator->trans(
                $demoRestricted->getMessage(),
                [],
                $demoRestricted->getDomain()
            )
        );
    }
}