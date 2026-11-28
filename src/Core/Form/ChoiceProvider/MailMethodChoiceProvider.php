<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MailMethodChoiceProvider provides choices for mail methods.
 */
final class MailMethodChoiceProvider implements FormChoiceProviderInterface
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
        // Offer sendmail, PHP's native mail transport, SMTP and disabled delivery
        return [
            $this->trans('Use /usr/sbin/sendmail (recommended; works in most cases)', [], 'Admin.Advparameters.Feature') => MailOption::METHOD_NATIVE,
            $this->trans('Use PHP mail() function', [], 'Admin.Advparameters.Feature') => MailOption::METHOD_PHP_MAIL,
            $this->trans('Set my own SMTP parameters (for advanced users ONLY)', [], 'Admin.Advparameters.Feature') => MailOption::METHOD_SMTP,
            $this->trans('Never send emails (may be useful for testing purposes)', [], 'Admin.Advparameters.Feature') => MailOption::METHOD_NONE,
        ];
    }

    /**
     * @param string $key
     * @param array $params
     * @param string $domain
     *
     * @return string
     */
    private function trans($key, array $params, $domain)
    {
        return $this->translator->trans($key, $params, $domain);
    }
}
