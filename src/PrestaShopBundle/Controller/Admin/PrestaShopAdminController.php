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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Default controller for PrestaShop admin pages.
 */
class PrestaShopAdminController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            TranslatorInterface::class => TranslatorInterface::class,
            CommandBusInterface::class => CommandBusInterface::class,
            HookDispatcherInterface::class => HookDispatcherInterface::class,
        ];
    }

    /**
     * Get commands bus to execute commands.
     */
    protected function dispatchCommand(mixed $command): mixed
    {
        return $this->container->get(CommandBusInterface::class)->handle($command);
    }

    protected function dispatchHookWithParameters(string $hookName, array $parameters = []): void
    {
        $this->container->get(HookDispatcherInterface::class)->dispatchWithParameters($hookName, $parameters);
    }

    protected function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->container->get(TranslatorInterface::class)->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Adds a list of errors as flash error message.
     *
     * @param array $errorMessages Error message, can be a string or an array with parameters for trans method
     */
    protected function addFlashErrors(array $errorMessages)
    {
        foreach ($errorMessages as $error) {
            $message = is_array($error) ? $this->trans($error['key'], $error['parameters'], $error['domain']) : $error;
            $this->addFlash('error', $message);
        }
    }
}
