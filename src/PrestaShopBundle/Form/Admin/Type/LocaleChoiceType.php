<?php

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleChoiceType extends AbstractType
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'placeholder' => 'Language',
            'translation_domain' => 'Admin.Global',
            'choice_translation_domain' => false,
            'choices' => $this->getLocaleChoices(),
        ]);
    }

    /**
     * Get locales to be used in form type.
     *
     * @return array
     */
    protected function getLocaleChoices(): array
    {
        $locales = [];

        foreach ($this->legacyContext->getLanguages() as $locale) {
            $locales[$locale['name']] = $locale['iso_code'];
        }

        return $locales;
    }
}
