<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CanonicalRedirectTypeChoiceProvider is responsible for providing choices for
 * redirect to the canonical URL form field selection.
 */
final class CanonicalRedirectTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CanonicalRedirectTypeChoiceProvider constructor.
     *
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
        $noRedirectionMessage = $this->translator->trans(
            'No redirection (you may have duplicate content issues)',
            [],
            'Admin.Shopparameters.Feature'
        );

        $movedTemporaryMessage = $this->translator->trans(
            '302 Moved Temporarily (recommended while setting up your store)',
            [],
            'Admin.Shopparameters.Feature'
        );

        $movedPermanentlyMessage = $this->translator->trans(
            '301 Moved Permanently (recommended once you have gone live)',
            [],
            'Admin.Shopparameters.Feature'
        );

        return [
            $noRedirectionMessage => 0,
            $movedTemporaryMessage => 1,
            $movedPermanentlyMessage => 2,
        ];
    }
}
