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

namespace PrestaShop\PrestaShop\Adapter\Meta\QueryHandler;

use Meta;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetMetaForEditing;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryHandler\GetMetaForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\EditableMeta;

/**
 * Class GetMetaForEditingHandler is responsible for retrieving meta data.
 *
 * @internal
 */
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
            $entity->keywords,
            $entity->url_rewrite
        );
    }
}
