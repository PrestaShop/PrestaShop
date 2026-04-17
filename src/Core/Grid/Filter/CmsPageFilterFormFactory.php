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
 * Class CmsPageCategoryFilterFormFactory is responsible for changing form action to the custom one.
 */
final class CmsPageFilterFormFactory implements GridFilterFormFactoryInterface
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
        $cmsPageCategoryFilterForm = $this->formFactory->create($definition);

        $newCmsPageCategoryFormBuilder = $cmsPageCategoryFilterForm->getConfig()->getFormFactory()->createNamedBuilder(
            $definition->getId(),
            FormType::class
        );

        /** @var FormInterface $categoryFormItem */
        foreach ($cmsPageCategoryFilterForm as $categoryFormItem) {
            $newCmsPageCategoryFormBuilder->add(
                $categoryFormItem->getName(),
                $categoryFormItem->getConfig()->getType()->getInnerType()::class,
                $categoryFormItem->getConfig()->getOptions()
            );
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $newActionUrl = $this->urlGenerator->generate('admin_cms_pages_search', [
                'id_cms_category' => $request->query->getInt('id_cms_category'),
            ]);

            $newCmsPageCategoryFormBuilder->setAction($newActionUrl);
        }

        return $newCmsPageCategoryFormBuilder->getForm();
    }
}
