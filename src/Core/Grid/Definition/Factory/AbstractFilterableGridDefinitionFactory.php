<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

/**
 * Class AbstractFilterableGridDefinitionFactory implements filterable grid definition creation.
 */
abstract class AbstractFilterableGridDefinitionFactory extends AbstractGridDefinitionFactory implements FilterableGridDefinitionFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * Grid definition already has an internal id used or hooks, that can also be used as an identifier for filters
     */
    public function getFilterId(): string
    {
        return $this->getId();
    }
}
