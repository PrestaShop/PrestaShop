<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Product search input dedicated to free gift selection in discounts.
 * Uses a dedicated endpoint that returns eligibility information for each product.
 */
class FreeGiftProductSearchType extends TranslatorAwareType
{
    private RouterInterface $router;

    private ProductRepository $productRepository;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        ProductRepository $productRepository,
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->productRepository = $productRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $router = $this->router;
        $productRepository = $this->productRepository;

        $resolver->setDefaults([
            'error_bubbling' => false,
            'required' => false,
            'label' => false,
            'layout' => EntitySearchInputType::LIST_LAYOUT,
            'placeholder' => $this->trans('Select free product', 'Admin.Catalog.Feature'),
            'min_length' => 3,
            'limit' => 1,
            'identifier_field' => 'id',
            'remote_url' => $router->generate('admin_discounts_search_gift_products', [
                'query' => '__QUERY__',
            ]),
            'allow_search' => function (Options $options) use ($productRepository): bool {
                return $productRepository->hasAnyProduct();
            },
            'empty_state' => function (Options $options) use ($productRepository): string {
                if (!$productRepository->hasAnyProduct()) {
                    $url = $this->router->generate('admin_products_index');

                    return sprintf(
                        '<span>%s</span><a href="%s" class="btn btn-outline-secondary btn-sm float-right">%s</a>',
                        $this->trans('To offer a free gift, you first need to add a product to your catalog.', 'Admin.Catalog.Notification'),
                        $url,
                        $this->trans('Manage products', 'Admin.Navigation.Menu'),
                    );
                }

                return $this->trans('No product selected', 'Admin.Catalog.Feature');
            },
        ]);
    }

    public function getParent(): string
    {
        return EntitySearchInputType::class;
    }
}
