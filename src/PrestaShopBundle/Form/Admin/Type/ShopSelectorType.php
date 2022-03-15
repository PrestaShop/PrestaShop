<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Entity\Repository\ShopRepository;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopSelectorType extends ChoiceType
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var ShopGroup[]
     */
    private $shopGroups;

    public function __construct(
        ShopRepository $shopRepository,
        array $shopGroups
    ) {
        parent::__construct();
        $this->shopRepository = $shopRepository;
        $this->shopGroups = $shopGroups;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'multiple' => false,
            'choices' => $this->getShopChoices(),
            'choice_label' => 'name',
            'choice_value' => 'id',
            'block_prefix' => 'shop_selector',
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addModelTransformer(new CallbackTransformer(
            function (int $shopId) {
                return $this->shopRepository->find($shopId);
            },
            function (Shop $shop) {
                return $shop->getId();
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['groupList'] = $this->shopGroups;
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
