<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Language;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Employee\FormLanguageChangerInterface;

/**
 * Class FormLanguageChanger is responsible for changing the language,
 * which is used in forms by the employee.
 * It is not the language in which form texts are translated, but rather
 * the language, which is selected by default in the translatable fields.
 */
final class FormLanguageChanger implements FormLanguageChangerInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function changeLanguageInCookies(string $languageIsoCode): void
    {
        $this->legacyContext->getContext()->cookie->employee_form_lang = (int) Language::getIdByIso($languageIsoCode);
        $this->legacyContext->getContext()->cookie->write();
    }
}
