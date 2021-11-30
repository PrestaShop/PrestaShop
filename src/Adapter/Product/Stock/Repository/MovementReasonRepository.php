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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Repository;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\MovementReasonConfigurationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\MovementReasonId;

class MovementReasonRepository
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        ConfigurationInterface $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Provides stock movement reason id by configuration
     *
     * @param string $configurationName
     *
     * @return MovementReasonId
     *
     * @throws MovementReasonConfigurationNotFoundException
     */
    public function getIdByConfiguration(string $configurationName): MovementReasonId
    {
        $id = (int) $this->configuration->get($configurationName);

        if (!$id) {
            throw new MovementReasonConfigurationNotFoundException(sprintf(
                'Movement reason id is not configured by "%s"',
                $configurationName
            ));
        }

        return new MovementReasonId($id);
    }

    /**
     * @param bool $increased true if quantity increased, false if decreased
     *
     * @return MovementReasonId
     */
    public function getIdForEmployeeEdition(bool $increased): MovementReasonId
    {
        if ($increased) {
            return $this->getIdByConfiguration(MovementReasonId::MOVEMENT_REASON_INCREASE_BY_EMPLOYEE_EDITION);
        }

        return $this->getIdByConfiguration(MovementReasonId::MOVEMENT_REASON_DECREASE_BY_EMPLOYEE_EDITION);
    }
}
