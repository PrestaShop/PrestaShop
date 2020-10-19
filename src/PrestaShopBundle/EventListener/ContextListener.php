<?php
/*
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
declare(strict_types=1);

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Psr\Container\ContainerInterface;

class ContextListener
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(LegacyContext $context, ContainerInterface $container)
    {
        $this->context = $context;
        $this->container = $container;
    }

    public function onKernelRequest()
    {
        if (null === $this->context->getContext()->getCurrentLocale()) {
            if (null === $this->context->getContext()->container) {
                $this->context->getContext()->container = $this->container;
            }
            $localeRepo = $this->container->get(\Controller::SERVICE_LOCALE_REPOSITORY);
            $this->context->getContext()->currentLocale = $localeRepo->getLocale(
                $this->context->getContext()->language->getLocale()
            );
        }
        if (null === $this->context->getContext()->controller) {
            $this->context->getContext()->controller = new \AdminController();
        }
    }
}
