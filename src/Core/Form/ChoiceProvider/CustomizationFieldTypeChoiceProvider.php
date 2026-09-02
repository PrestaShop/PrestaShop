<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CustomizationFieldTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return [
            $this->translator->trans('Text', [], 'Admin.Global') => CustomizationFieldType::TYPE_TEXT,
            $this->translator->trans('File', [], 'Admin.Global') => CustomizationFieldType::TYPE_FILE,
        ];
    }
}
