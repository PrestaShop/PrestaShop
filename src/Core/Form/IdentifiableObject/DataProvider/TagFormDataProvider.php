<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Tag\Query\GetTagForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tag\QueryResult\EditableTag;

class TagFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus
    ) {
    }

    public function getData($id)
    {
        /** @var EditableTag $editableTag */
        $editableTag = $this->queryBus->handle(new GetTagForEditing((int) $id));

        return [
            'name' => $editableTag->getName(),
            'language' => $editableTag->getLanguageId(),
            'products' => $editableTag->getProducts(),
        ];
    }

    public function getDefaultData()
    {
        return [
            'lifetime' => 3600,
        ];
    }
}
