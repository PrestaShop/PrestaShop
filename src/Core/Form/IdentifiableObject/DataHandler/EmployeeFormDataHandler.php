<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\LastName;

/**
 * Handles submitted employee form's data.
 */
final class EmployeeFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var array
     */
    private $defaultShopAssociation;

    /**
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @param CommandBusInterface $bus
     * @param array $defaultShopAssociation
     * @param int $superAdminProfileId
     */
    public function __construct(
        CommandBusInterface $bus,
        array $defaultShopAssociation,
        $superAdminProfileId
    ) {
        $this->bus = $bus;
        $this->defaultShopAssociation = $defaultShopAssociation;
        $this->superAdminProfileId = $superAdminProfileId;
    }
    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        // Super admins have access to all shops and that cannot be changed by the user.
        if ($data['profile'] == $this->superAdminProfileId) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        /** @var EmployeeId $employeeId */
        $employeeId = $this->bus->handle(new AddEmployeeCommand(
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['password'],
            $data['optin'],
            $data['default_page'],
            $data['language'],
            $data['active'],
            $data['profile'],
            $data['shop_association']
        ));

        return $employeeId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $command = (new EditEmployeeCommand($id))
            ->setFirstName(new FirstName($data['firstname']))
            ->setLastName(new LastName($data['lastname']))
            ->setEmail(new Email($data['email']))
            ->setIsSubscribedToNewsletter((bool) $data['optin'])
            ->setDefaultPageId((int) $data['default_page'])
            ->setLanguageId((int) $data['language'])
            ->setActive((bool) $data['active'])
            ->setProfileId((int) $data['profile'])
        ;

        if (isset($data['password'])) {
            $command->setPlainPassword($data['password']);
        }

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $command->setShopAssociation(
                array_map(function ($shopId) { return (int) $shopId; }, $shopAssociation)
            );
        }

        /** @var EmployeeId $employeeId */
        $employeeId = $this->bus->handle($command);

        return $employeeId->getValue();
    }
}
