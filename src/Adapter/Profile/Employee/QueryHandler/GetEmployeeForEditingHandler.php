<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\QueryHandler;

use Employee;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
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
#[AsQueryHandler]
final class GetEmployeeForEditingHandler extends AbstractObjectModelHandler implements GetEmployeeForEditingHandlerInterface
{
    /**
     * @var ImageTagSourceParserInterface
     */
    private $imageTagSourceParser;

    /**
     * @param ImageTagSourceParserInterface|null $imageTagSourceParser
     */
    public function __construct(?ImageTagSourceParserInterface $imageTagSourceParser = null)
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
