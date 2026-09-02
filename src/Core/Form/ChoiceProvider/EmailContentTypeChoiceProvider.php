<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmailContentTypeChoiceProvider provides email content type choices.
 */
final class EmailContentTypeChoiceProvider implements FormChoiceProviderInterface
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
     * Get email content type choices.
     *
     * @return array
     */
    public function getChoices()
    {
        return [
            $this->translator->trans('Subject', [], 'Admin.Global') => 'subject',
            $this->translator->trans('Body', [], 'Admin.International.Feature') => 'body',
        ];
    }
}
