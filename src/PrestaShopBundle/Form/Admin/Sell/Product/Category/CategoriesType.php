<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Category;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoriesType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $defaultCategoryChoiceProvider;

    /**
     * @var EventSubscriberInterface
     */
    private $eventSubscriber;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurableFormChoiceProviderInterface $defaultCategoryChoiceProvider
     * @param EventSubscriberInterface $eventSubscriber
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $defaultCategoryChoiceProvider,
        EventSubscriberInterface $eventSubscriber
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCategoryChoiceProvider = $defaultCategoryChoiceProvider;
        $this->eventSubscriber = $eventSubscriber;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_categories', CategoryTagsCollectionType::class)
            ->add('add_categories_btn', ButtonType::class, [
                'label' => $this->trans('Choose categories', 'Admin.Catalog.Feature'),
                'attr' => [
                    'class' => 'add-categories-btn btn-outline-secondary',
                ],
            ])
            ->add('default_category_id', ChoiceType::class, [
                'constraints' => [],
                'choices' => $this->defaultCategoryChoiceProvider->getChoices(['product_id' => $options['product_id']]),
                'label' => $this->trans('Default category', 'Admin.Catalog.Feature'),
                'help' => $this->trans('Defines the product\'s primary placement, usually the deepest category in the hierarchy. It\'s used in breadcrumbs, URLs, structured data and many other parts of the shop.', 'Admin.Catalog.Help'),
                'autocomplete' => true,
            ])
        ;

        $builder->addEventSubscriber($this->eventSubscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
                'required' => false,
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/categories.html.twig',
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
        ;
    }
}
