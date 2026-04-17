<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Customer;
use Group;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\SearchCustomers;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\SearchCustomersHandlerInterface;

/**
 * Handles query that searches for customers by given phrases
 *
 * @internal
 */
#[AsQueryHandler]
final class SearchCustomersHandler implements SearchCustomersHandlerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param Configuration $configuration
     * @param int $contextLangId
     */
    public function __construct(
        Configuration $configuration,
        int $contextLangId
    ) {
        $this->configuration = $configuration;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SearchCustomers $query)
    {
        $limit = 50;
        $phrases = array_unique($query->getPhrases());

        $customers = [];

        foreach ($phrases as $searchPhrase) {
            if (empty($searchPhrase)) {
                continue;
            }

            $customersResult = Customer::searchByName(
                $searchPhrase,
                $limit,
                $query->getShopConstraint(),
                $query->getExcludeGuests()
            );
            if (!is_array($customersResult)) {
                continue;
            }

            // Will we work with groups or not? We could ask inside the loop, but this is faster
            $assignGroups = false;
            if (Group::isFeatureActive()) {
                $assignGroups = true;

                // Get our group data and extract ids and names
                $groupNames = [];
                foreach (Group::getGroups($this->contextLangId) as $group) {
                    $groupNames[$group['id_group']] = $group['name'];
                }
            }

            foreach ($customersResult as $customerArray) {
                if (!$customerArray['active']) {
                    continue;
                }

                $customerArray['fullname_and_email'] = sprintf(
                    '%s %s - %s',
                    $customerArray['firstname'],
                    $customerArray['lastname'],
                    $customerArray['email']
                );

                // Assign group names and default group information
                $customerArray['groups'] = [];
                if ($assignGroups) {
                    $group_ids = explode(',', $customerArray['group_ids']);
                    foreach ($group_ids as $id_group) {
                        $customerArray['groups'][$id_group] = [
                            'id_group' => $id_group,
                            'name' => $groupNames[$id_group] ?? '',
                            'default' => $id_group == $customerArray['id_default_group'],
                        ];
                    }
                }
                unset($customerArray['group_ids']);

                // Removing some information that could be considered a security risk
                unset(
                    $customerArray['passwd'],
                    $customerArray['secure_key'],
                    $customerArray['last_passwd_gen'],
                    $customerArray['reset_password_token'],
                    $customerArray['reset_password_validity']
                );

                $isB2BEnabled = $this->configuration->getBoolean('PS_B2B_ENABLE');

                if (!$isB2BEnabled) {
                    unset(
                        $customerArray['company']
                    );
                }

                $customers[$customerArray['id_customer']] = $customerArray;
            }
        }

        return $customers;
    }
}
