<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TranslationTypeByNameChoiceProvider provides translation type choices.
 */
final class TranslationTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return [
            $this->translator->trans('Back office translations', [], 'Admin.International.Feature') => 'back',
            $this->translator->trans('Front office Translations', [], 'Admin.International.Feature') => 'themes',
            $this->translator->trans('Installed modules translations', [], 'Admin.International.Feature') => 'modules',
            $this->translator->trans('Email translations', [], 'Admin.International.Feature') => 'mails',
            $this->translator->trans('Other translations', [], 'Admin.International.Feature') => 'others',
        ];
    }

    public function getExportCoreChoices(): array
    {
        return [
            $this->translator->trans('Back office', [], 'Admin.International.Feature') => ProviderDefinitionInterface::TYPE_BACK,
            $this->translator->trans('Front office', [], 'Admin.International.Feature') => ProviderDefinitionInterface::TYPE_FRONT,
            $this->translator->trans('Email', [], 'Admin.International.Feature') => ProviderDefinitionInterface::TYPE_MAILS,
            $this->translator->trans('Other', [], 'Admin.International.Feature') => ProviderDefinitionInterface::TYPE_OTHERS,
        ];
    }
}
