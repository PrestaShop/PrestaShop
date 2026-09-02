<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\Repository\ShopRepository;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used to select one or multiple shops, it is used with the
 */
class ShopSelectorType extends AbstractType
{
    public function __construct(
        private readonly ShopRepository $shopRepository,
        /**
         * @var ShopGroup[]
         */
        private readonly array $shopGroups,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => false,
            'choices' => $this->getShopChoices(),
            'choice_label' => 'name',
            'choice_value' => 'id',
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/multishop.html.twig',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'shop_selector';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['contextShopId'] = $this->shopContext->getShopConstraint()->getShopId()?->getValue();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->addModelTransformer(new CallbackTransformer(
            function ($selection) {
                if (is_array($selection)) {
                    return array_map(function (int $shopId) {
                        return $this->shopRepository->find($shopId);
                    }, $selection);
                } elseif (!empty($selection)) {
                    $this->shopRepository->find($selection);
                }

                return null;
            },
            function ($selection) {
                if (is_array($selection)) {
                    return array_map(function (Shop $shop) {
                        return $shop->getId();
                    }, $selection);
                }

                return $selection instanceof Shop ? $selection->getId() : null;
            }
        ));
    }

    private function getShopChoices(): array
    {
        $groups = [];
        /** @var ShopGroup $shopGroup */
        foreach ($this->shopGroups as $shopGroup) {
            if (!$shopGroup->getShops()->count()) {
                continue;
            }
            $groupShops = [];
            /** @var Shop $shop */
            foreach ($shopGroup->getShops() as $shop) {
                if ($shop->hasMainUrl()) {
                    $groupShops[] = $shop;
                }
            }

            $groups[$shopGroup->getName()] = $groupShops;
        }

        return $groups;
    }
}
