<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Event\Dispatcher\NullDispatcher;
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
     * @param HookDispatcherInterface|null $hookDispatcher
     */
    public function __construct(HookDispatcherInterface $hookDispatcher = null)
    {
        if (null === $hookDispatcher) {
            @trigger_error('The $hookDispatcher parameter should not be null, inject your main HookDispatcherInterface service, or NullDispatcher if you don\'t need hooks.', E_USER_DEPRECATED);
        }
        $this->hookDispatcher = $hookDispatcher ? $hookDispatcher : new NullDispatcher();
    }

    /**
     * Set hook dispatcher.
     *
     * @param HookDispatcherInterface $hookDispatcher
     *
     * @deprecated
     */
    final public function setHookDispatcher(HookDispatcherInterface $hookDispatcher)
    {
        @trigger_error('The AbstractGridDefinitionFactory::setHookDispatcher method is deprecated as of 1.7.5.1 Please use the constructor instead', E_USER_DEPRECATED);

        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    final public function getDefinition()
    {
        $definition = new GridDefinition(
            $this->getId(),
            $this->getName(),
            $this->getColumns(),
            $this->getFilters(),
            $this->getGridActions(),
            $this->getBulkActions()
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
