<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnNotFoundException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class FilterFormFactory is responsible for creating grid filter form.
 */
final class GridFilterFormFactory implements GridFilterFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function create(GridDefinitionInterface $definition)
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
            $definition->getId(),
            FormType::class,
            null,
            [
                'allow_extra_fields' => true,
            ]
        );

        /** @var FilterInterface $filter */
        foreach ($definition->getFilters()->all() as $filter) {
            $filterOptions = array_merge([
                'label' => $this->getFilterLabel($definition, $filter),
            ], $filter->getTypeOptions());
            $formBuilder->add(
                $filter->getName(),
                $filter->getType(),
                $filterOptions
            );
        }

        $this->hookDispatcher->dispatchWithParameters('action' . Container::camelize($definition->getId()) . 'GridFilterFormModifier', [
            'filter_form_builder' => $formBuilder,
        ]);

        return $formBuilder->getForm();
    }

    private function getFilterLabel(GridDefinitionInterface $definition, FilterInterface $filter): string
    {
        $filterLabel = $filter->getName();
        try {
            if ($filter->getAssociatedColumn()) {
                $column = $definition->getColumnById($filter->getAssociatedColumn());
                $filterLabel = !empty($column->getName()) ? $column->getName() : $filterLabel;
            }
        } catch (ColumnNotFoundException) {
        }

        return $filterLabel;
    }
}
