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

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class GeolocationOptionsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('geolocation_behaviour', ChoiceType::class, [
                'choices' => [
                    $this->trans('Visitors cannot see your catalog.', 'Admin.International.Feature') => _PS_GEOLOCATION_NO_CATALOG_,
                    $this->trans('Visitors can see your catalog but cannot place an order.', 'Admin.International.Feature') => _PS_GEOLOCATION_NO_ORDER_,
                ],
            ])
            ->add('geolocation_na_behaviour', ChoiceType::class, [
                'choices' => [
                    $this->trans('All features are available', 'Admin.International.Feature') => '-1',
                    $this->trans('Visitors cannot see your catalog.', 'Admin.International.Feature') => _PS_GEOLOCATION_NO_CATALOG_,
                    $this->trans('Visitors can see your catalog but cannot place an order.', 'Admin.International.Feature') => _PS_GEOLOCATION_NO_ORDER_,
                ],
            ])
        ;
    }
}
