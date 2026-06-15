<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\BusinessEntityFormDataProvider;
use PrestaShopBundle\Form\Admin\Type\ShopSelectorType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\DataTransformer\BusinessEntityCommandTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class BusinessEntityType extends TranslatorAwareType
{
    public const BILLING_ADDRESS_TYPE = 'billing_address';
    public const SHIPPING_ADDRESS_TYPE = 'shipping_address';

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly BusinessEntityCommandTransformer $commandTransformer
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();

        $billingAddressCountries = $this->getDataForBusinessEntityAddressType(
            $data,
            self::BILLING_ADDRESS_TYPE
        );
        $shippingAddressCountries = $this->getDataForBusinessEntityAddressType(
            $data,
            self::SHIPPING_ADDRESS_TYPE
        );
        // $data will always contain the result of getDefaultData
        $defaultAddressData = $data[self::BILLING_ADDRESS_TYPE][BusinessEntityFormDataProvider::DEFAULT_BILLING_ADDRESS_INDEX] ?? [];

        $builder
            ->add('general_information', BusinessEntityGeneralInformationType::class)
            ->add(self::BILLING_ADDRESS_TYPE, CollectionType::class, [
                'entry_type' => BusinessEntityAddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => $this->trans('Billing addresses', 'Admin.Global'),
                'entry_options' => [
                    'data' => $billingAddressCountries === [] ? $defaultAddressData : $billingAddressCountries,
                ],
                'prototype_data' => $defaultAddressData,
            ])
            ->add(self::SHIPPING_ADDRESS_TYPE, CollectionType::class, [
                'entry_type' => BusinessEntityAddressType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => $this->trans('Shipping addresses', 'Admin.Global'),
                'entry_options' => [
                    'data' => $shippingAddressCountries,
                ],
                'prototype_data' => $defaultAddressData,
            ])
            ->add('billingAddressAsShippingAddress', SwitchType::class, [
                'label' => $this->trans('Use default billing address as shipping address', 'Admin.Global'),
            ])
            ->add('default_billing_address', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'd-none'],
            ])
            ->add('default_shipping_address', TextType::class, [
                'label' => false,
                'attr' => ['class' => 'd-none'],
            ])
            ->add('shop_id', HiddenType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $data = $event->getData();
            if (!empty($data['shop_id'])) {
                return;
            }

            $event->getForm()->add('shop_id', ShopSelectorType::class);
        });

        $builder->addModelTransformer($this->commandTransformer);
    }

    protected function getDataForBusinessEntityAddressType(array $data, string $addressType): array
    {
        $dataForBusinessEntityAddressType = [];
        foreach ($data[$addressType] as $formIndex => $address) {
            if (!isset($address['countryId'])) {
                continue;
            }
            $dataForBusinessEntityAddressType[$formIndex] = [
                'countryId' => (int) $address['countryId'],
            ];
        }

        return $dataForBusinessEntityAddressType;
    }
}
