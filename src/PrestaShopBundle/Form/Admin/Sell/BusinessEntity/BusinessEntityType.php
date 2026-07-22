<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\BusinessEntityFormDataProvider;
use PrestaShopBundle\Form\Admin\Type\ShopSelectorType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class BusinessEntityType extends TranslatorAwareType
{
    public const BILLING_ADDRESS_AS_SHIPPING_ADDRESS = 'billing_address_as_shipping_address';
    public const BILLING_ADDRESS_TYPE = 'billing_address';
    public const DEFAULT_BILLING_ADDRESS = 'default_billing_address';
    public const DEFAULT_SHIPPING_ADDRESS = 'default_shipping_address';
    public const GENERAL_INFORMATION = 'general_information';
    public const SHIPPING_ADDRESS_TYPE = 'shipping_address';
    public const SHOP_ID = 'shop_id';

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();

        // $data will always contain the result of getDefaultData
        $defaultAddressData = $data[self::BILLING_ADDRESS_TYPE][BusinessEntityFormDataProvider::DEFAULT_BILLING_ADDRESS_INDEX] ?? [];

        $builder
            ->add(self::GENERAL_INFORMATION, BusinessEntityGeneralInformationType::class)
            ->add(self::BILLING_ADDRESS_TYPE, CollectionType::class, [
                'entry_type' => BusinessEntityAddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => $this->trans('Billing addresses', 'Admin.Global'),
                'prototype_data' => $defaultAddressData,
            ])
            ->add(self::SHIPPING_ADDRESS_TYPE, CollectionType::class, [
                'entry_type' => BusinessEntityAddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => $this->trans('Shipping addresses', 'Admin.Global'),
                'prototype_data' => $defaultAddressData,
            ])
            ->add(self::BILLING_ADDRESS_AS_SHIPPING_ADDRESS, SwitchType::class, [
                'label' => $this->trans('Use default billing address as shipping address', 'Admin.Global'),
                'required' => false,
            ])
            ->add(self::DEFAULT_BILLING_ADDRESS, TextType::class, [
                'label' => false,
                'attr' => ['class' => 'd-none'],
            ])
            ->add(self::DEFAULT_SHIPPING_ADDRESS, TextType::class, [
                'label' => false,
                'attr' => ['class' => 'd-none'],
            ])
            ->add(self::SHOP_ID, HiddenType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $data = $event->getData();
            if (!empty($data[self::SHOP_ID])) {
                return;
            }

            $event->getForm()->add(self::SHOP_ID, ShopSelectorType::class);
        });
    }
}
