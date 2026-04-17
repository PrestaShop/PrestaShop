<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\Query\GetTitleForEditing;
use PrestaShop\PrestaShop\Core\Domain\Title\QueryResult\EditableTitle;
use PrestaShop\PrestaShop\Core\Domain\Title\TitleSettings;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;

/**
 * Provides data for title add/edit form.
 */
class TitleFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        /** @var EditableTitle $result */
        $result = $this->queryBus->handle(new GetTitleForEditing($id));

        return [
            'id' => $id,
            'name' => $result->getLocalizedNames(),
            'gender_type' => $result->getGender(),
            'img_height' => $result->getHeight(),
            'img_width' => $result->getWidth(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'name' => [],
            'gender_type' => Gender::TYPE_MALE,
            'img_height' => TitleSettings::DEFAULT_IMAGE_HEIGHT,
            'img_width' => TitleSettings::DEFAULT_IMAGE_WIDTH,
        ];
    }
}
