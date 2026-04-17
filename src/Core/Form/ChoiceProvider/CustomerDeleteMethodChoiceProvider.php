<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides choices in which customer can be deleted.
 */
final class CustomerDeleteMethodChoiceProvider implements FormChoiceProviderInterface
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
        $allowRegistrationLabel = $this->translator->trans(
            'I want my customers to be able to register again with the same email address. All data will be removed from the database.',
            [],
            'Admin.Orderscustomers.Notification'
        );

        $denyRegistraionLabel = $this->translator->trans(
            'I do not want my customer(s) to register again with the same email address. All selected customer(s) will be removed from this list but their corresponding data will be kept in the database.',
            [],
            'Admin.Orderscustomers.Notification'
        );

        return [
            $allowRegistrationLabel => CustomerDeleteMethod::ALLOW_CUSTOMER_REGISTRATION,
            $denyRegistraionLabel => CustomerDeleteMethod::DENY_CUSTOMER_REGISTRATION,
        ];
    }
}
