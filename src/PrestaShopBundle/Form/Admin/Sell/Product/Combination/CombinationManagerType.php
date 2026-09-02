<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This form type is not really used for product form data. It is actually rendered specifically via its form theme
 * (src/PrestaShopBundle/Resources/views/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig) to include the
 * layout to render all the combination management controls. Among which the combination paginated list which is itself
 * rendered via a controller action from the CombinationController.
 *
 * Some form inputs will be rendered in this sub form but they actually belong to another form (CombinationListType) so
 * they have different property path from the product form and won't be handled by it (since Form::getData is based on
 * the form naming structure it will naturally ignore all those fields).
 */
class CombinationManagerType extends TranslatorAwareType
{
    /**
     * @var FeatureInterface
     */
    private $multiStoreFeature;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FeatureInterface $multiStoreFeature
    ) {
        parent::__construct($translator, $locales);
        $this->multiStoreFeature = $multiStoreFeature;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['productId'] = $options['product_id'];
        $view->vars['isMultiStoreActive'] = $this->multiStoreFeature->isActive();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label' => $this->trans('Manage product combinations', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/combination.html.twig',
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
        ;
    }
}
