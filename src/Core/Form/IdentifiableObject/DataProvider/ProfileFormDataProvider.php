<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Query\GetProfileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult\EditableProfile;

/**
 * Provides data for Profile form
 */
final class ProfileFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var string
     */
    private $defaultAvatarUrl;
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     * @param string $defaultAvatarUrl
     */
    public function __construct(
        CommandBusInterface $queryBus,
        string $defaultAvatarUrl
    ) {
        $this->queryBus = $queryBus;
        $this->defaultAvatarUrl = $defaultAvatarUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($profileId)
    {
        /** @var EditableProfile $editableProfile */
        $editableProfile = $this->queryBus->handle(new GetProfileForEditing($profileId));

        return [
            'name' => $editableProfile->getLocalizedNames(),
            'avatar_url' => $editableProfile->getAvatarUrl(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'avatar_url' => $this->defaultAvatarUrl,
        ];
    }
}
