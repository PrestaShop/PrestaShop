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

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\MissingCustomerRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Provides reusable methods for customer command handlers.
 *
 * @internal
 */
abstract class AbstractCustomerHandler
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $objectTypeLabel;

    /**
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->translator = $translator;
        $this->logger = $logger;

        $this->objectTypeLabel = $this->translator->trans('Customer', [], 'Admin.Global');
    }

    /**
     * @param CustomerId $customerId
     * @param Customer $customer
     *
     * @throws CustomerNotFoundException
     */
    protected function assertCustomerWasFound(CustomerId $customerId, Customer $customer)
    {
        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found.', $customerId->getValue()));
        }
    }

    /**
     * @param Customer $customer
     *
     * @throws MissingCustomerRequiredFieldsException
     */
    protected function assertRequiredFieldsAreNotMissing(Customer $customer)
    {
        $errors = $customer->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingCustomerRequiredFieldsException($missingFields, sprintf('One or more required fields for customer are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }
}
