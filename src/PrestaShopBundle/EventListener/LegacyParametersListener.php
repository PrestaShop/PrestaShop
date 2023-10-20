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

namespace PrestaShopBundle\EventListener;

use PrestaShopBundle\Routing\Converter\LegacyParametersConverter;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tools;

/**
 * This listener converts the request routing information (if present) into an array
 * of legacy parameters which is then injected into the Tools class allowing to access
 * former legacy parameters using the same Tools::getValue and the same parameter name.
 *
 * Note: this is limited to parameters defined in the routing via _legacy_link and _legacy_parameters
 */
class LegacyParametersListener
{
    /**
     * @var LegacyParametersConverter
     */
    private $converter;

    /**
     * @param LegacyParametersConverter $converter
     */
    public function __construct(LegacyParametersConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $legacyParameters = $this->converter->getParameters($request->attributes->all(), $request->query->all());
        if (null === $legacyParameters) {
            return;
        }

        Tools::setFallbackParameters($legacyParameters);
    }
}
