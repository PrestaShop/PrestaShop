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

namespace PrestaShopBundle\Bridge\Controller;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Tools;

/**
 * Create configuration for controller migrate horizontally
 */
class ControllerConfigurationFactory
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(
        TokenStorage $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }

    public function create(array $configuration = []): ControllerConfiguration
    {
        $configuratorController = new ControllerConfiguration();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $configuration = $resolver->resolve($configuration);

        $configuratorController->id = $configuration['id'];
        $configuratorController->controllerName = $configuration['controllerName'];
        $configuratorController->controllerNameLegacy = $configuration['controllerNameLegacy'];
        $configuratorController->positionIdentifier = $configuration['positionIdentifier'];
        $configuratorController->table = $configuration['table'];
        $configuratorController->user = $this->getUser();
        $configuratorController->folderTemplate = Tools::toUnderscoreCase(substr($configuratorController->controllerNameLegacy, 5)) . '/';

        return $configuratorController;
    }

    private function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'id',
            'controllerName',
            'controllerNameLegacy',
            'positionIdentifier',
            'table',
        ]);
    }
}
