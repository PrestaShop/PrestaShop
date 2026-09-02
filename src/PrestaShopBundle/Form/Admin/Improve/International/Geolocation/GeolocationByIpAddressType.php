<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GeolocationByIpAddressType is responsible for handling "Improve > International > Localization > Geolocation"
 * IP addresses whitelist form.
 */
class GeolocationByIpAddressType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('geolocation_enabled', SwitchType::class, [
                'label' => $this->trans(
                    'Geolocation by IP address',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'This option allows you, among other things, to restrict access to your shop for certain countries. See below.',
                    'Admin.International.Help'
                ),
            ]);
    }
}
