<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\MailTheme;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Form\Admin\Type\LocaleChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GenerateMailsType is responsible for build the form to generate mail templates.
 */
class GenerateMailsType extends TranslatorAwareType
{
    /** @var array */
    private $mailThemes;

    /** @var array */
    private $themes;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurationInterface $configuration
     * @param array $mailThemes
     * @param array $themes
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurationInterface $configuration,
        array $mailThemes,
        array $themes
    ) {
        parent::__construct($translator, $locales);
        $this->mailThemes = $mailThemes;
        $this->themes = $themes;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noTheme = $this->trans('Core (no theme selected)', 'Admin.International.Feature');

        $builder
            ->add('mailTheme', ChoiceType::class, [
                'choices' => $this->mailThemes,
                'data' => $this->configuration->get('PS_MAIL_THEME'),
            ])
            ->add('language', LocaleChoiceType::class)
            ->add('theme', ChoiceType::class, [
                'choices' => $this->themes,
                'placeholder' => $noTheme,
                'required' => false,
                'empty_data' => '',
                'data' => '',
                'disabled' => count($this->themes) <= 0,
            ])
            ->add('overwrite', SwitchType::class, ['data' => false])
        ;
    }
}
