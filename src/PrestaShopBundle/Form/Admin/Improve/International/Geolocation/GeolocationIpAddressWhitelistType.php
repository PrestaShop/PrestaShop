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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class GeolocationIpAddressWhitelistType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('geolocation_whitelist', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'col' => 15,
                    'rows' => 30,
                ],
            ])
        ;

        $builder->get('geolocation_whitelist')
            ->addModelTransformer(new CallbackTransformer(
                function ($ipWhitelistTextWithSemiColons) {
                    return str_replace(';', "\n", $ipWhitelistTextWithSemiColons);
                },
                function ($ipWhitelistTextWithNewLines) {
                    return preg_replace('/\r\n|\r|\n/', ';', $ipWhitelistTextWithNewLines);
                }
            ))
        ;
    }
}
