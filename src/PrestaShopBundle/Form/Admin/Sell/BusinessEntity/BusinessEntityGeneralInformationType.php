<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\BusinessEntity;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The two sections below carry inherit_data, so the data handled here stays a flat array keyed by
 * the FIELD_* constants; the nesting exists only to let each section be rendered on its own row.
 */
class BusinessEntityGeneralInformationType extends TranslatorAwareType
{
    public const SECTION_IDENTITY = 'identity';
    public const SECTION_SETTINGS = 'settings';

    public const FIELD_NAME = 'name';
    public const FIELD_LEGAL_NAME = 'legal_name';
    public const FIELD_EXTERNAL_REF = 'external_ref';
    public const FIELD_DELIVERY_AUTHORIZED = 'delivery_authorized';
    public const FIELD_STATUS = 'status';
    public const FIELD_CUSTOMER_GROUP_ID = 'customer_group_id';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::SECTION_IDENTITY, BusinessEntityIdentityType::class, [
                'columns_number' => 2,
            ])
            ->add(self::SECTION_SETTINGS, BusinessEntitySettingsType::class, [
                'columns_number' => 2,
            ]);
    }
}
