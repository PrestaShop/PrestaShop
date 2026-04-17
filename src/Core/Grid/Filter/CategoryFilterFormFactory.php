<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CategoryFilterFormFactory decorates original filter factory to add custom submit action.
 */
final class CategoryFilterFormFactory implements GridFilterFormFactoryInterface
{
    /**
     * @var GridFilterFormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param GridFilterFormFactoryInterface $formFactory
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     */
    public function __construct(
        GridFilterFormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function create(GridDefinitionInterface $definition)
    {
        $categoryFilterForm = $this->formFactory->create($definition);

        $newCategoryFormBuilder = $categoryFilterForm->getConfig()->getFormFactory()->createNamedBuilder(
            $definition->getId(),
            FormType::class
        );

        /** @var FormInterface $categoryFormItem */
        foreach ($categoryFilterForm as $categoryFormItem) {
            $newCategoryFormBuilder->add(
                $categoryFormItem->getName(),
                $categoryFormItem->getConfig()->getType()->getInnerType()::class,
                $categoryFormItem->getConfig()->getOptions()
            );
        }

        $queryParams = [];
        $request = $this->requestStack->getCurrentRequest();

        if ((null !== $request) && $request->attributes->has('categoryId')) {
            $queryParams['categoryId'] = $request->attributes->get('categoryId');
        }

        $newCategoryFormBuilder->setAction(
            $this->urlGenerator->generate('admin_categories_search', $queryParams)
        );

        return $newCategoryFormBuilder->getForm();
    }
}
