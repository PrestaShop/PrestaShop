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

namespace PrestaShop\PrestaShop\Core\Form\Entity\ResponseHandler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RedirectWithSuccessAddMessageHandler is default implementation of success response
 * when new entity has been added.
 */
final class RedirectWithSuccessAddMessageHandler implements SuccessResponseHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param FlashBagInterface $flashBag
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FlashBagInterface $flashBag,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessResponse(Request $request)
    {
        $this->flashBag->add(
            'success',
            $this->translator->trans('Successful creation.', [], 'Admin.Notifications.Success')
        );

        $redirectUrl = $this->router->generate(
            $request->attributes->get('redirect_after_add_route')
        );

        return new RedirectResponse($redirectUrl);
    }
}
