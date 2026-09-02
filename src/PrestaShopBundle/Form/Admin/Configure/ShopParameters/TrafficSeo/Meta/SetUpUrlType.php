<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SetUpUrlType is responsible for providing form fields for Set up urls block located in
 * Shop parameters -> Traffic & Seo -> Seo & urls page.
 */
class SetUpUrlType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $canonicalUrlChoices;

    /**
     * @var bool
     */
    private $isHtaccessFileWritable;

    /**
     * @var bool
     */
    private $isModRewriteActive;

    /**
     * @var bool
     */
    private $doesMainShopUrlExist;

    /**
     * SetUpUrlType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $canonicalUrlChoices
     * @param bool $isHtaccessFileWritable
     * @param bool $doesMainShopUrlExist
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $canonicalUrlChoices,
        $isHtaccessFileWritable,
        $isModRewriteActive,
        $doesMainShopUrlExist
    ) {
        parent::__construct($translator, $locales);
        $this->canonicalUrlChoices = $canonicalUrlChoices;
        $this->isHtaccessFileWritable = $isHtaccessFileWritable;
        $this->isModRewriteActive = $isModRewriteActive;
        $this->doesMainShopUrlExist = $doesMainShopUrlExist;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $friendlyUrlHelp = $this->trans(
            'Enable this option only if your server allows URL rewriting (recommended).',
            'Admin.Shopparameters.Help'
        );

        if (!$this->isModRewriteActive) {
            $friendlyUrlHelp .=
                '<br/>' . $this->trans(
                    'URL rewriting (mod_rewrite) is not active on your server, or it is not possible to check your server configuration. If you want to use Friendly URLs, you must activate this mod.',
                    'Admin.Shopparameters.Help'
                );
        }

        $builder
            ->add('friendly_url', SwitchType::class, [
                'label' => $this->trans('Friendly URL', 'Admin.Global'),
                'help' => $friendlyUrlHelp,
                'multistore_configuration_key' => 'PS_REWRITING_SETTINGS',
            ])
            ->add('default_language_url_prefix', SwitchType::class, [
                'label' => $this->trans('Use prefix for default language', 'Admin.Global'),
                'help' => $this->trans(
                    'If there are multiple languages enabled on the shop, all URLs are prefixed with the ISO code of the language. Do you want to do this also for the default language, or keep it un-prefixed? If adding a language to a well established single language shop, it\'s a good idea to disable this and avoid changing the URLs.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_DEFAULT_LANGUAGE_URL_PREFIX',
            ])
            ->add('accented_url', SwitchType::class, [
                'label' => $this->trans('Accented URL', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Enable this option if you want to allow accented characters in your friendly URLs. You should only activate this option if you are using non-Latin characters; for all the Latin charsets, your SEO will be better without this option.',
                    'Admin.Shopparameters.Help'
                ),
                'multistore_configuration_key' => 'PS_ALLOW_ACCENTED_CHARS_URL',
            ])
            ->add(
                'canonical_url_redirection',
                ChoiceType::class,
                [
                    'choices' => $this->canonicalUrlChoices,
                    'translation_domain' => false,
                    'label' => $this->trans('Redirect to the canonical URL', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'If a visitor accesses a page using a non-canonical URL, this option defines how the shop should respond. For SEO reasons, we recommend choosing "301 Moved Permanently".',
                        'Admin.Shopparameters.Help'
                    ),
                    'multistore_configuration_key' => 'PS_CANONICAL_REDIRECT',
                ]
            );

        if ($this->isHtaccessFileWritable && $this->doesMainShopUrlExist) {
            $builder
                ->add('disable_apache_multiview', SwitchType::class, [
                    'label' => $this->trans('Disable Apache\'s MultiViews option', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Enable this option only if you have problems with URL rewriting.',
                        'Admin.Shopparameters.Help'
                    ),
                    'multistore_configuration_key' => 'PS_HTACCESS_DISABLE_MULTIVIEWS',
                ])
                ->add('disable_apache_mod_security', SwitchType::class, [
                    'label' => $this->trans('Disable Apache\'s mod_security module', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Some of PrestaShop\'s features might not work correctly with a specific configuration of Apache\'s mod_security module. We recommend to turn it off.',
                        'Admin.Shopparameters.Help'
                    ),
                    'multistore_configuration_key' => 'PS_HTACCESS_DISABLE_MODSEC',
                ]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
