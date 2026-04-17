<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AbstractGridDefinitionFactory implements grid definition creation.
 */
abstract class AbstractGridDefinitionFactory implements GridDefinitionFactoryInterface
{
    use TranslatorAwareTrait;

    /**
     * @var HookDispatcherInterface
     */
    protected $hookDispatcher;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(HookDispatcherInterface $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $definition = new GridDefinition(
            $this->getId(),
            $this->getName(),
            $this->getColumns(),
            $this->getFilters(),
            $this->getGridActions(),
            $this->getBulkActions(),
            $this->getViewOptions()
        );

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($definition->getId()) . 'GridDefinitionModifier', [
            'definition' => $definition,
        ]);

        return $definition;
    }

    /**
     * Get unique grid identifier.
     *
     * @return string
     */
    abstract protected function getId();

    /**
     * Get translated grid name.
     *
     * @return string
     */
    abstract protected function getName();

    /**
     * Get defined columns for grid.
     *
     * @return ColumnCollectionInterface
     */
    abstract protected function getColumns();

    /**
     * Get defined grid actions.
     * Override this method to define custom grid actions collection.
     *
     * @return GridActionCollectionInterface
     */
    protected function getGridActions()
    {
        return new GridActionCollection();
    }

    /**
     * Get defined bulk actions.
     * Override this method to define custom bulk actions collection.
     *
     * @return BulkActionCollectionInterface
     */
    protected function getBulkActions()
    {
        return new BulkActionCollection();
    }

    /**
     * Get defined grid view options.
     * Override this method to define custom view options collection.
     *
     * @return ViewOptionsCollectionInterface
     */
    protected function getViewOptions()
    {
        return new ViewOptionsCollection();
    }

    /**
     * Get defined filters.
     * Override this method to define custom filters collection.
     *
     * @return FilterCollectionInterface
     */
    protected function getFilters()
    {
        return new FilterCollection();
    }
}
