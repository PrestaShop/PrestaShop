<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BusinessEntityGeneralInformationType extends TranslatorAwareType
{
    public const FIELD_NAME = 'name';
    public const FIELD_LEGAL_NAME = 'legal_name';
    public const FIELD_ENTERPRISE_ID = 'enterprise_id';
    public const FIELD_EXTERNAL_REF = 'external_ref';
    public const FIELD_FLAG_DELIVERY_AUTHORIZED = 'flag_delivery_authorized';
    public const FIELD_STATUS = 'status';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::FIELD_NAME, TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $this->trans('The display name of the business entity.', 'Admin.Catalog.Feature'),
            ])
            ->add(self::FIELD_LEGAL_NAME, TextType::class, [
                'label' => $this->trans('Legal Name', 'Admin.Global'),
                'help' => $this->trans('The official registered name of the company.', 'Admin.Catalog.Feature'),
            ])
            ->add(self::FIELD_ENTERPRISE_ID, TextType::class, [
                'label' => $this->trans('Enterprise ID', 'Admin.Global'),
                'required' => false,
            ])
            ->add(self::FIELD_EXTERNAL_REF, TextType::class, [
                'label' => $this->trans('External Reference', 'Admin.Global'),
            ])
            ->add(self::FIELD_FLAG_DELIVERY_AUTHORIZED, SwitchType::class, [
                'label' => $this->trans('Delivery authorized', 'Admin.Global'),
                'help' => $this->trans('Allow the B2B customer to order using an address that does not belong to the business entity', 'Admin.Catalog.Feature'),
            ])
            ->add(self::FIELD_STATUS, EnumType::class, [
                'label' => $this->trans('Status', 'Admin.Global'),
                'class' => BusinessEntityStatus::class,
            ]);
    }
}
