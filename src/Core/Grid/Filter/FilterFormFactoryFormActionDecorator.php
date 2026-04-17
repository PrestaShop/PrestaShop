<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Decorates grid filter form action.
 */
final class FilterFormFactoryFormActionDecorator implements GridFilterFormFactoryInterface
{
    /**
     * @var GridFilterFormFactoryInterface
     */
    private $delegate;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $formActionRoute;

    /**
     * @param GridFilterFormFactoryInterface $delegate
     * @param UrlGeneratorInterface $urlGenerator
     * @param string $formActionRoute will change the form action of filters form to this
     */
    public function __construct(
        GridFilterFormFactoryInterface $delegate,
        UrlGeneratorInterface $urlGenerator,
        string $formActionRoute
    ) {
        $this->delegate = $delegate;
        $this->urlGenerator = $urlGenerator;
        $this->formActionRoute = $formActionRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function create(GridDefinitionInterface $definition)
    {
        $filterForm = $this->delegate->create($definition);

        $formBuilder = $filterForm->getConfig()->getFormFactory()->createNamedBuilder(
            $definition->getId(),
            FormType::class
        );

        /** @var FormInterface $formItem */
        foreach ($filterForm as $formItem) {
            $formBuilder->add(
                $formItem->getName(),
                $formItem->getConfig()->getType()->getInnerType()::class,
                $formItem->getConfig()->getOptions()
            );
        }

        $formBuilder->setAction(
            $this->urlGenerator->generate($this->formActionRoute)
        );

        return $formBuilder->getForm();
    }
}
