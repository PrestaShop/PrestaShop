<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceSignature;

/**
 * @internal
 */
#[AsQueryHandler]
final class GetCustomerServiceSignatureHandler implements GetCustomerServiceSignatureHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerServiceSignature $query)
    {
        $signature = $this->configuration->get('PS_CUSTOMER_SERVICE_SIGNATURE');

        return $signature[$query->getLanguageId()->getValue()];
    }
}
