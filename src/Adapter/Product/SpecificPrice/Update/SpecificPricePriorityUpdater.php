<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update;

use Doctrine\DBAL\Connection;
use Exception;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotSetSpecificPricePrioritiesException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use SpecificPrice;

/**
 * Responsible for updates related to specific price priorities
 */
class SpecificPricePriorityUpdater
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ConfigurationInterface $configuration
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->configuration = $configuration;
    }

    /**
     * @param ProductId $productId
     * @param PriorityList $priorityList
     *
     * @throws CoreException
     */
    public function setPrioritiesForProduct(ProductId $productId, PriorityList $priorityList): void
    {
        try {
            if (!SpecificPrice::setSpecificPriority($productId->getValue(), $priorityList->getPriorities())) {
                throw new CannotSetSpecificPricePrioritiesException(sprintf(
                    'Failed updating specific price priorities for product #%d',
                    $productId->getValue()
                ));
            }
        } catch (Exception) {
            throw new CoreException(sprintf(
                'Error occurred when trying to set specific price priorities for product #%d',
                $productId->getValue()
            ));
        }
    }

    /**
     * @param PriorityList $priorityList
     */
    public function updateDefaultPriorities(PriorityList $priorityList): void
    {
        try {
            $this->configuration->set(
                'PS_SPECIFIC_PRICE_PRIORITIES',
                implode(';', $priorityList->getPriorities())
            );
        } catch (Exception) {
            throw new CoreException('Error occurred when trying to update default specific price priorities');
        }
    }

    /**
     * @param ProductId $productId
     */
    public function removePrioritiesForProduct(ProductId $productId): void
    {
        try {
            $this->connection->delete(
                $this->dbPrefix . 'specific_price_priority',
                ['id_product' => $productId->getValue()]
            );
        } catch (Exception) {
            throw new CoreException(sprintf(
                'Error occurred when trying to remove specific price priorities for product #%d',
                $productId->getValue()
            ));
        }
    }
}
