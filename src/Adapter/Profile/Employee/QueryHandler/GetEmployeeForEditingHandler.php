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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\QueryHandler;

use Employee;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler\GetEmployeeForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EditableEmployee;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParser;
use PrestaShop\PrestaShop\Core\Image\Parser\ImageTagSourceParserInterface;

/**
 * Handles command that gets employee for editing.
 */
final class GetEmployeeForEditingHandler extends AbstractObjectModelHandler implements GetEmployeeForEditingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @param ImageTagSourceParserInterface|null $imageTagSourceParser
     */
    public function __construct(ImageTagSourceParserInterface $imageTagSourceParser = null)
    {
        $this->imageTagSourceParser = $imageTagSourceParser ?? new ImageTagSourceParser();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetEmployeeForEditing $query)
    {
        $employeeId = $query->getEmployeeId();
        $employee = new Employee($employeeId->getValue());

        if ($employee->id !== $employeeId->getValue()) {
            throw new EmployeeNotFoundException($employeeId, sprintf('Employee with id "%s" was not found', $employeeId->getValue()));
        }

        $avatarUrl = $this->getAvatarUrl($employeeId->getValue());

        return new EditableEmployee(
            $employeeId,
            new FirstName($employee->firstname),
            new LastName($employee->lastname),
            new Email($employee->email),
            $avatarUrl ? $avatarUrl['path'] : $employee->getImage(),
            (int) $employee->default_tab,
            (int) $employee->id_lang,
            (bool) $employee->active,
            (int) $employee->id_profile,
            $employee->getAssociatedShops(),
            $employee->has_enabled_gravatar
        );
    }

    /**
     * @param int $imageId
     *
     * @return array|null
     */
    private function getAvatarUrl(int $imageId): ?array
    {
        $imagePath = _PS_EMPLOYEE_IMG_DIR_ . $imageId . '.jpg';
        $imageTag = $this->getTmpImageTag($imagePath, $imageId, 'employee');
        $imageSize = $this->getImageSize($imagePath);

        if (empty($imageTag) || null === $imageSize) {
            return null;
        }

        return [
            'size' => sprintf('%skB', $imageSize),
            'path' => $this->imageTagSourceParser->parse($imageTag),
        ];
    }
}
