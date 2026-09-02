<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EditableEmployee;

/**
 * Provides data for employee forms.
 */
final class EmployeeFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @var array
     */
    private $defaultShopAssociation;

    /**
     * @var string
     */
    private $defaultAvatarUrl;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $isMultistoreFeatureActive
     * @param array $defaultShopAssociation
     * @param string $defaultAvatarUrl
     */
    public function __construct(
        CommandBusInterface $queryBus,
        $isMultistoreFeatureActive,
        array $defaultShopAssociation,
        string $defaultAvatarUrl
    ) {
        $this->queryBus = $queryBus;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->defaultShopAssociation = $defaultShopAssociation;
        $this->defaultAvatarUrl = $defaultAvatarUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($employeeId)
    {
        /** @var EditableEmployee $editableEmployee */
        $editableEmployee = $this->queryBus->handle(new GetEmployeeForEditing((int) $employeeId));

        return [
            'firstname' => $editableEmployee->getFirstName()->getValue(),
            'lastname' => $editableEmployee->getLastName()->getValue(),
            'email' => $editableEmployee->getEmail()->getValue(),
            'default_page' => $editableEmployee->getDefaultPageId(),
            'language' => $editableEmployee->getLanguageId(),
            'active' => $editableEmployee->isActive(),
            'profile' => $editableEmployee->getProfileId(),
            'shop_association' => $editableEmployee->getShopAssociation(),
            'has_enabled_gravatar' => $editableEmployee->hasEnabledGravatar(),
            'avatar_url' => $editableEmployee->getAvatarUrl(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data = [
            'active' => true,
            'has_enabled_gravatar' => false,
            'avatar_url' => $this->defaultAvatarUrl,
        ];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
