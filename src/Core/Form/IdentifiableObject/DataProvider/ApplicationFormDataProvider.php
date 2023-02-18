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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Query\GetApplicationForEditing;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\QueryResult\EditableApplication;

/**
 * Provides data for authorized application add/edit forms
 */
final class ApplicationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(CommandBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ApplicationConstraintException
     */
    public function getData($id): array
    {
        /** @var EditableApplication $editableApplication */
        $editableApplication = $this->bus->handle(new GetApplicationForEditing((int) $id));

        return [
            'name' => $editableApplication->getName(),
            'description' => $editableApplication->getDescription(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
