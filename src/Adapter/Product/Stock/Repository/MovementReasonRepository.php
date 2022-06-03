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
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\MovementReasonConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\MovementReasonId;

class MovementReasonRepository
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Provides stock movement reason id from configuration
     *
     * @throws MovementReasonConfigurationNotFoundException
     * @throws MovementReasonConstraintException
     */
    public function getReasonIdFromConfiguration(string $configurationKey): MovementReasonId
    {
        $id = (int) $this->configuration->get($configurationKey);

        if (!$id) {
            throw new MovementReasonConfigurationNotFoundException(sprintf(
                'Movement reason id is not configured by "%s"',
                $configurationKey
            ));
        }

        return new MovementReasonId($id);
    }

    /**
     * @param bool $increased true if quantity increased, false if decreased
     *
     * @throws MovementReasonConfigurationNotFoundException
     * @throws MovementReasonConstraintException
     */
    public function getEmployeeEditionReasonId(bool $increased): MovementReasonId
    {
        return $this->getReasonIdFromConfiguration(
            $increased
            ? MovementReasonId::INCREASE_BY_EMPLOYEE_EDITION_CONFIG_KEY
            : MovementReasonId::DECREASE_BY_EMPLOYEE_EDITION_CONFIG_KEY
        );
    }
}
