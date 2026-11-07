<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\AccordionType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * For combination update in bulk action
 */
class BulkCombinationType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private ?FeatureFlagStateCheckerInterface $featureFlagStateChecker = null
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', BulkCombinationStockType::class)
            ->add('price', BulkCombinationPriceType::class, [
                'product_id' => $options['product_id'],
                'country_id' => $options['country_id'],
                'shop_id' => $options['shop_id'],
            ])
            ->add('references', BulkCombinationReferencesType::class)
            ->add('images', BulkCombinationImagesType::class, [
                'label' => $this->trans('Images', 'Admin.Global'),
                'product_id' => $options['product_id'],
            ])
        ;

        if ($this->featureFlagStateChecker?->isEnabled(FeatureFlagSettings::FEATURE_FLAG_COMBINATION_STATUS)) {
            $builder->add('status', BulkCombinationStatusType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
                'label_subtitle' => $this->trans('You can bulk edit the selected combinations by enabling and filling each field that needs to be updated.', 'Admin.Catalog.Feature'),
                'expand_first' => false,
                'display_one' => false,
                'required' => false,
                'attr' => [
                    'class' => 'bulk-combination-form',
                ],
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig',
            ])
            ->setRequired([
                'product_id',
                'country_id',
                'shop_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('country_id', 'int')
            ->setAllowedTypes('shop_id', 'int')
        ;
    }

    public function getParent()
    {
        return AccordionType::class;
    }
}
