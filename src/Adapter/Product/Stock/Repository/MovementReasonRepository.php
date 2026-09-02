<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
