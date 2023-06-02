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

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Converts any legacy url into a migrated Symfony url (if it exists) and redirect to it.
 */
#[AsEventListener(
    event: RequestEvent::class,
    method: 'onKernelRequest',
    priority: 40,
)]
class LegacyUrlListener
{
    /**
     * @var LegacyUrlConverter
     */
    private $converter;

    /**
     * @param LegacyUrlConverter $converter
     */
    public function __construct(LegacyUrlConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        try {
            $convertedUrl = $this->converter->convertByRequest($event->getRequest());
        } catch (CoreException $e) {
            return;
        }

        $event->setResponse(new RedirectResponse($convertedUrl));
    }
}
