<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Meta\QueryHandler;

use Meta;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetMetaForEditing;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryHandler\GetMetaForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\EditableMeta;

/**
 * Class GetMetaForEditingHandler is responsible for retrieving meta data.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetMetaForEditingHandler implements GetMetaForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws MetaNotFoundException
     */
    public function handle(GetMetaForEditing $query)
    {
        $metaId = $query->getMetaId();

        $entity = new Meta($metaId->getValue());

        if (0 >= $entity->id) {
            throw new MetaNotFoundException(sprintf('Meta with id "%s" cannot be found', $metaId->getValue()));
        }

        if ((int) $entity->id !== $metaId->getValue()) {
            throw new MetaNotFoundException(sprintf('The retrieved id "%s" does not match requested Meta id "%s"', $entity->id, $metaId->getValue()));
        }

        return new EditableMeta(
            $metaId->getValue(),
            $entity->page,
            $entity->title,
            $entity->description,
            $entity->url_rewrite
        );
    }
}
