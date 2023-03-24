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

use PrestaShop\PrestaShop\Core\Domain\Contact\Repository\ContactRepositoryInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Repository\CustomerMessageRepository;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceSummary;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerServiceSummary;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Repository\CustomerThreadRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 * @experimental Refacto needed once the new model architecture is defined.
 */
class GetCustomerServicesSummaryHandler implements GetCustomerServicesSummaryHandlerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContactRepositoryInterface
     */
    private $contactRepository;

    /**
     * @var CustomerThreadRepository
     */
    private $customerThreadRepository;

    /**
     * @var CustomerMessageRepository
     */
    private $customerMessageRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        RouterInterface $router,
        ContactRepositoryInterface $contactRepository,
        CustomerThreadRepository $customerThreadRepository,
        CustomerMessageRepository $customerMessageRepository,
        TranslatorInterface $translator
    ) {
        $this->router = $router;
        $this->contactRepository = $contactRepository;
        $this->customerThreadRepository = $customerThreadRepository;
        $this->customerMessageRepository = $customerMessageRepository;
        $this->translator = $translator;
    }

    /**
     * @return array{summaries: array<int,CustomerServiceSummary>, statistics: array<string, (float|int)>}
     */
    public function handle(GetCustomerServiceSummary $query): array
    {
        $contacts = $this->contactRepository->getContacts();
        $customerServicesSummary = [];
        $customerServicesSummary['summaries'] = [];
        foreach ($this->contactRepository->getCategoriesContacts() as $categoriesContact) {
            $customerThreadId = 0;
            $totalThreads = 0;
            $viewUrl = '';
            foreach ($contacts as $contact) {
                if ($categoriesContact['id_contact'] === $contact['id_contact']) {
                    $totalThreads = (int) $contact['total'];
                    $customerThreadId = $contact['id_customer_thread'];
                    break;
                }
            }
            if ($customerThreadId > 0) {
                $viewUrl = $this->router->generate(
                    'admin_customer_threads_view',
                    [
                        'customerThreadId' => $customerThreadId,
                    ]
                );
            }
            $customerServiceSummary = new CustomerServiceSummary(
                (int) $categoriesContact['id_contact'],
                $categoriesContact['name'],
                $categoriesContact['description'],
                $totalThreads,
                $viewUrl
            );

            $customerServicesSummary['summaries'][$customerServiceSummary->getContactId()] = $customerServiceSummary;
        }

        $customerServicesSummary['statistics'] = [
            $this->translator->trans('Total threads', [], 'Admin.Catalog.Feature') => $all = $this->customerThreadRepository->getTotalCustomerThreads(),
            $this->translator->trans('Threads pending', [], 'Admin.Catalog.Feature') => $pending = $this->customerThreadRepository->getTotalCustomerThreads('status LIKE "%pending%"'),
            $this->translator->trans('Total number of customer messages', [], 'Admin.Catalog.Feature') => $this->customerMessageRepository->getTotalCustomerMessages('id_employee = 0'),
            $this->translator->trans('Total number of employee messages', [], 'Admin.Catalog.Feature') => $this->customerMessageRepository->getTotalCustomerMessages('id_employee != 0'),
            $this->translator->trans('Unread threads', [], 'Admin.Catalog.Feature') => $unread = $this->customerThreadRepository->getTotalCustomerThreads('status = "open"'),
            $this->translator->trans('Closed threads', [], 'Admin.Catalog.Feature') => $all - ($unread + $pending),
        ];

        return $customerServicesSummary;
    }
}
