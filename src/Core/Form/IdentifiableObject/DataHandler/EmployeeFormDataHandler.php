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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\Password;
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\EmployeeDataProviderInterface;

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
     * @var EmployeeFormAccessCheckerInterface
     */
    private $employeeFormAccessChecker;

    /**
     * @var EmployeeDataProviderInterface
     */
    private $employeeDataProvider;

    /**
     * @var Hashing
     */
    private $hashing;

    /**
     * @param CommandBusInterface $bus
     * @param array $defaultShopAssociation
     * @param int $superAdminProfileId
     * @param EmployeeFormAccessCheckerInterface $employeeFormAccessChecker
     * @param EmployeeDataProviderInterface $employeeDataProvider
     * @param Hashing $hashing
     */
    public function __construct(
        CommandBusInterface $bus,
        array $defaultShopAssociation,
        $superAdminProfileId,
        EmployeeFormAccessCheckerInterface $employeeFormAccessChecker,
        EmployeeDataProviderInterface $employeeDataProvider,
        Hashing $hashing
    ) {
        $this->bus = $bus;
        $this->defaultShopAssociation = $defaultShopAssociation;
        $this->superAdminProfileId = $superAdminProfileId;
        $this->employeeFormAccessChecker = $employeeFormAccessChecker;
        $this->employeeDataProvider = $employeeDataProvider;
        $this->hashing = $hashing;
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
            $data['default_page'],
            $data['language'],
            $data['active'],
            $data['profile'],
            isset($data['shop_association']) ? $data['shop_association'] : $this->defaultShopAssociation
        ));

        return $employeeId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $command = (new EditEmployeeCommand($id))
            ->setFirstName($data['firstname'])
            ->setLastName($data['lastname'])
            ->setEmail($data['email'])
            ->setDefaultPageId((int) $data['default_page'])
            ->setLanguageId((int) $data['language'])
            ->setActive((bool) $data['active'])
            ->setProfileId((int) $data['profile'])
        ;

        if ($this->employeeFormAccessChecker->isRestrictedAccess((int) $id)) {
            if ($this->shouldChangePassword($data)) {
                $this->assertPasswordIsSameAsOldPassword(
                    $data['change_password']['old_password'],
                    $id
                );

                $command->setPlainPassword($data['change_password']['new_password']);
            }
        } elseif (isset($data['password'])) {
            $command->setPlainPassword($data['password']);
        }

        if (isset($data['shop_association'])) {
            $shopAssociation = $data['shop_association'] ?: [];
            $command->setShopAssociation(
                array_map(function ($shopId) { return (int) $shopId; }, $shopAssociation)
            );
        }

        $this->bus->handle($command);
    }

    /**
     * Asserts if given password is the same as employee's password.
     *
     * @param string $plainPassword
     * @param int $employeeId
     *
     * @throws EmployeeConstraintException
     */
    private function assertPasswordIsSameAsOldPassword($plainPassword, $employeeId)
    {
        $oldPassword = $this->employeeDataProvider->getEmployeeHashedPassword($employeeId);

        if (!$this->hashing->checkHash($plainPassword, $oldPassword)) {
            throw new EmployeeConstraintException('Old and new passwords do not match.', EmployeeConstraintException::INCORRECT_PASSWORD);
        }
    }

    /**
     * Checks if all required fields are present in form data for changing the password.
     *
     * @param array $formData
     *
     * @return bool
     */
    private function shouldChangePassword(array $formData)
    {
        if (!isset($formData['change_password'])) {
            return false;
        }

        return
            null !== $formData['change_password']['old_password'] &&
            null !== $formData['change_password']['new_password']
        ;
    }
}
