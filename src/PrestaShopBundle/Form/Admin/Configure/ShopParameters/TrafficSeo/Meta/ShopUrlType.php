<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ShopUrlType is responsible for providing form fields for
 * Shop parameters -> Traffic & Seo -> Seo & Urls -> Shop urls block.
 */
class ShopUrlType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isShopFeatureActive;

    /**
     * @var bool
     */
    private $doesMainShopUrlExist;

    /**
     * ShopUrlType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isShopFeatureActive
     * @param bool $doesMainShopUrlExist
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isShopFeatureActive,
        bool $doesMainShopUrlExist
    ) {
        parent::__construct($translator, $locales);
        $this->isShopFeatureActive = $isShopFeatureActive;
        $this->doesMainShopUrlExist = $doesMainShopUrlExist;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->isShopFeatureActive && $this->doesMainShopUrlExist) {
            $builder
                ->add('domain', TextType::class, [
                    'label' => $this->trans(
                        'Shop domain',
                        'Admin.Shopparameters.Feature'
                    ),
                ])
                ->add('domain_ssl', TextType::class, [
                    'label' => $this->trans(
                        'SSL domain',
                        'Admin.Shopparameters.Feature'
                    ),
                ])
                ->add('physical_uri', TextType::class, [
                    'label' => $this->trans(
                        'Base URI',
                        'Admin.Shopparameters.Feature'
                    ),
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }
}
