<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImportLocalizationPackType
 */
class ImportLocalizationPackType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iso_localization_pack')
            ->add('content_to_import', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    $this->trans('States', 'Admin.International.Feature') => 'states',
                    $this->trans('Taxes', 'Admin.Global') => 'taxes',
                    $this->trans('Currencies', 'Admin.Global') => 'currencies',
                    $this->trans('Languages', 'Admin.Global') => 'languages',
                    $this->trans('Units (e.g. weight, volume, distance)', 'Admin.International.Feature') => 'units',
                    $this->trans('Change the behavior of the price display for groups', 'Admin.International.Feature') => 'groups',
                ],
            ])
            ->add('download_pack_data', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    $this->trans('Yes', 'Admin.Global') => 1,
                    $this->trans('No', 'Admin.Global') => 0,
                ],
            ])
        ;
    }
}
