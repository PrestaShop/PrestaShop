<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Description;

use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Sell\Product\Category\CategoriesType;
use PrestaShopBundle\Form\Admin\Sell\Product\Image\ImageDropzoneType;
use PrestaShopBundle\Form\Admin\Sell\Product\Image\ProductImageType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DescriptionType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $employeeIsoCode;

    /**
     * @var int
     */
    private $shortDescriptionMaxLength;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param string $employeeIsoCode
     * @param int $shortDescriptionMaxLength
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        string $employeeIsoCode,
        int $shortDescriptionMaxLength
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->employeeIsoCode = $employeeIsoCode;
        $this->shortDescriptionMaxLength = $shortDescriptionMaxLength;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productId = $options['product_id'];
        $shopId = $options['shop_id'];

        if ($this->shortDescriptionMaxLength > 0) {
            $shortDescriptionLimit = $this->shortDescriptionMaxLength;
        } else {
            $shortDescriptionLimit = ProductSettings::MAX_DESCRIPTION_SHORT_LENGTH;
        }

        $builder
            ->add('images', ImageDropzoneType::class, [
                'product_id' => $productId,
                'shop_id' => $shopId,
                'update_form_type' => ProductImageType::class,
            ])
            ->add('description_short', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Summary', 'Admin.Global'),
                'label_help_box' => $this->trans('Short description of the product. We recommend two to three sentences or a few clear phrases that describe the item. It\'s displayed near the product name, used in the meta description, and shown in several other places. Avoid duplicating this text across different products.', 'Admin.Catalog.Help'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => $shortDescriptionLimit,
                    'attr' => [
                        'class' => 'serp-default-description',
                    ],
                ],
                'label_tag_name' => 'h3',
                'modify_all_shops' => true,
            ])
            ->add('description', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Description', 'Admin.Global'),
                'label_help_box' => $this->trans('Optional detailed information about the product, such as its features, specifications, images, videos, or package contents. Use this field when you need to provide more details beyond the short summary.', 'Admin.Catalog.Help'),
                'type' => FormattedTextareaType::class,
                'options' => [
                    'limit' => ProductSettings::MAX_DESCRIPTION_LENGTH,
                ],
                'label_tag_name' => 'h3',
                'modify_all_shops' => true,
            ])
            ->add('categories', CategoriesType::class, [
                'label' => $this->trans('Categories', 'Admin.Global'),
                'label_tag_name' => 'h3',
                'product_id' => $productId,
            ])
            ->add('manufacturer', ManufacturerType::class)
            ->add('related_products', ProductSearchType::class, [
                'include_combinations' => false,
                'label' => $this->trans('Related products', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'label_help_box' => $this->trans('Products closely connected to this item, such as accessories or complementary goods. Adding them helps customers discover relevant items and improves product navigation.', 'Admin.Catalog.Help'),
                'entry_options' => [
                    'block_prefix' => 'related_product',
                ],
                'remote_url' => $this->router->generate('admin_products_search_products_for_association', [
                    'languageCode' => $this->employeeIsoCode,
                    'query' => '__QUERY__',
                ]),
                'min_length' => 3,
                'limit' => 0,
                'filtered_identities' => $productId > 0 ? [$productId] : [],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'required' => false,
                'label' => $this->trans('Description', 'Admin.Catalog.Feature'),
            ])
            ->setRequired([
                'product_id',
                'shop_id',
            ])
            ->setAllowedTypes('product_id', 'int')
            ->setAllowedTypes('shop_id', 'int')
        ;
    }
}
