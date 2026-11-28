<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ShopUrl;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopUrlType extends TranslatorAwareType
{
    private const DOMAIN_MAX_LENGTH = 255;
    private const URI_MAX_LENGTH = 64;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly FormChoiceProviderInterface $shopChoiceProvider,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $urlOptions = $builder->create('url_options', CardType::class, [
            'label' => $this->trans('URL options', 'Admin.Advparameters.Feature'),
            'icon' => 'settings',
            'inherit_data' => true,
        ])
            ->add('shop_id', ChoiceType::class, [
                'label' => $this->trans('Store', 'Admin.Global'),
                'choices' => $this->shopChoiceProvider->getChoices(),
                'translation_domain' => false,
                'attr' => [
                    'data-shop-ids-with-url' => json_encode($options['shop_ids_with_url']),
                    'data-main-url-shop-id' => $options['main_url_shop_id'],
                ],
            ])
            ->add('main', SwitchType::class, [
                'label' => $this->trans('Is it the main URL for this store?', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('If you set this URL as the Main URL for the selected shop, all URLs set to this shop will be redirected to this URL (you can only have one Main URL per shop).', 'Admin.Advparameters.Help'),
                'alert_message' => [
                    $this->trans('Since the selected shop has no main URL, you have to set this URL as the Main URL.', 'Admin.Advparameters.Help'),
                    $this->trans('The selected shop already has a Main URL. Therefore, if you set this one as the Main URL, the older Main URL will be set as a regular URL.', 'Admin.Advparameters.Help'),
                ],
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Enabled', 'Admin.Global'),
            ]);

        $storeUrl = $builder->create('store_url', CardType::class, [
            'label' => $this->trans('Store URL', 'Admin.Advparameters.Feature'),
            'icon' => 'link',
            'inherit_data' => true,
        ])
            ->add('domain', TextType::class, [
                'label' => $this->trans('Domain', 'Admin.Advparameters.Feature'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Domain', 'Admin.Advparameters.Feature'))]
                        ),
                    ]),
                    $this->getLengthConstraint(self::DOMAIN_MAX_LENGTH),
                ],
            ])
            ->add('domain_ssl', TextType::class, [
                'label' => $this->trans('SSL Domain', 'Admin.Advparameters.Feature'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [$this->getLengthConstraint(self::DOMAIN_MAX_LENGTH)],
            ])
            ->add('physical_uri', TextType::class, [
                'label' => $this->trans('Physical URL', 'Admin.Advparameters.Feature'),
                'required' => false,
                'empty_data' => '',
                'help' => $this->trans('This is the physical folder for your store on the web server. Leave this field empty if your store is installed on the root path. For instance, if your store is available at www.example.com/my-store/, you must input my-store/ in this field.', 'Admin.Advparameters.Help'),
                'constraints' => [$this->getLengthConstraint(self::URI_MAX_LENGTH)],
            ])
            ->add('virtual_uri', TextType::class, [
                'label' => $this->trans('Virtual URL', 'Admin.Advparameters.Feature'),
                'required' => false,
                'empty_data' => '',
                'help' => $this->trans('You can use this option if you want to create a store with a URL that doesn\'t exist on your server (e.g. if you want your store to be available with the URL www.example.com/my-store/shoes/, you have to set shoes/ in this field, assuming that my-store/ is your Physical URL).', 'Admin.Advparameters.Help')
                    . ' ' . $this->trans('URL rewriting must be activated on your server to use this feature.', 'Admin.Advparameters.Help'),
                'constraints' => [$this->getLengthConstraint(self::URI_MAX_LENGTH)],
            ])
            ->add('final_url', TextType::class, [
                'label' => $this->trans('Final URL', 'Admin.Advparameters.Feature'),
                'mapped' => false,
                'required' => false,
                'attr' => ['readonly' => true],
            ]);

        $builder
            ->add($urlOptions)
            ->add($storeUrl);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'shop_ids_with_url' => [],
                'main_url_shop_id' => null,
            ])
            ->setAllowedTypes('shop_ids_with_url', 'int[]')
            ->setAllowedTypes('main_url_shop_id', ['null', 'int']);
    }

    private function getLengthConstraint(int $maxLength): Length
    {
        return new Length([
            'max' => $maxLength,
            'maxMessage' => $this->trans(
                'This field cannot be longer than %limit% characters',
                'Admin.Notifications.Error',
                ['%limit%' => $maxLength]
            ),
        ]);
    }
}
