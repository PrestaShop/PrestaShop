<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Geolocation;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GeolocationIpAddressWhitelistType is responsible for handling "Improve > International > Localization > Geolocation"
 * IP addresses whitelist form.
 */
class GeolocationIpAddressWhitelistType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('geolocation_whitelist', TextareaType::class, [
                'required' => false,
                'label' => $this->trans(
                    'Whitelisted IP addresses',
                    'Admin.International.Feature'
                ),
                'attr' => [
                    'col' => 15,
                    'rows' => 30,
                ],
            ]);

        $builder->get('geolocation_whitelist')
            ->addModelTransformer(new CallbackTransformer(
                function ($ipWhitelistTextWithSemiColons) {
                    return str_replace(';', "\n", $ipWhitelistTextWithSemiColons);
                },
                function ($ipWhitelistTextWithNewLines) {
                    return str_replace(["\r\n", "\r", "\n"], ';', $ipWhitelistTextWithNewLines);
                }
            ));
    }
}
