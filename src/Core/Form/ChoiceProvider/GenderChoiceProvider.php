<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class GenderProvider provides genders choices.
 */
class GenderChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get currency choices.
     *
     * @return array
     */
    public function getChoices(): array
    {
        return [
            $this->translator->trans('Male', [], 'Admin.Shopparameters.Feature') => Gender::TYPE_MALE,
            $this->translator->trans('Female', [], 'Admin.Shopparameters.Feature') => Gender::TYPE_FEMALE,
            $this->translator->trans('Other', [], 'Admin.Shopparameters.Feature') => Gender::TYPE_OTHER,
        ];
    }
}
