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
                $query->getShopConstraint()
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
