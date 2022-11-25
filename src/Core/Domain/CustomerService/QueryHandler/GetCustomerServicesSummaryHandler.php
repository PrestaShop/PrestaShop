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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler;

use Contact;
use CustomerThread;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceSummary;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceSummary;
use Symfony\Component\Routing\RouterInterface;

class GetCustomerServicesSummaryHandler implements GetCustomerServicesSummaryHandlerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return CustomerServiceSummary[]
     */
    public function handle(GetCustomerServiceSummary $query): array
    {
        $customerServicesSummary = [];
        foreach (Contact::getCategoriesContacts() as $categoriesContact) {
            $customerServiceSummary = new CustomerServiceSummary((int) $categoriesContact['id_contact']);
            $customerThreadId = 0;
            foreach (CustomerThread::getContacts() as $contact) {
                if ($categoriesContact['id_contact'] === $contact['id_contact']) {
                    $customerServiceSummary->setTotalThreads((int) $contact['total']);
                    $customerThreadId = $contact['id_customer_thread'];
                }
            }
            if ($customerThreadId > 0) {
                $customerServiceSummary->setViewUrl(
                    $this->router->generate(
                        'admin_customer_threads_view',
                        [
                            'customerThreadId' => $customerThreadId,
                        ]
                    )
                );
            }
            $customerServicesSummary[$customerServiceSummary->getContactId()] = $customerServiceSummary;
        }

        return $customerServicesSummary;
    }
}
