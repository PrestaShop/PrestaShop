<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetRequiredFieldsForCustomerHandlerInterface;

/**
 * Handles query which gets required fields for customer sign up
 *
 * @internal
 */
#[AsQueryHandler]
final class GetRequiredFieldsForCustomerHandler implements GetRequiredFieldsForCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetRequiredFieldsForCustomer $query)
    {
        $requiredFields = (new Customer())->getFieldsRequiredDatabase();

        if (empty($requiredFields)) {
            return [];
        }

        $fields = [];

        foreach ($requiredFields as $field) {
            $fields[] = $field['field_name'];
        }

        return $fields;
    }
}
