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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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

/**
 * This form type is used to select one or multiple shops, it is used with the
 */
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

    /**
     * @var int|null
     */
    private $contextShopId;

    public function __construct(
        ShopRepository $shopRepository,
        array $shopGroups,
        ?int $contextShopId
    ) {
        parent::__construct();
        $this->shopRepository = $shopRepository;
        $this->shopGroups = $shopGroups;
        $this->contextShopId = $contextShopId;
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
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/multishop.html.twig',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['contextShopId'] = $this->contextShopId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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
