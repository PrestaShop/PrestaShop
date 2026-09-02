<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Employee;

/**
 * Interface FormLanguageSwitcherInterface describes an employee form language changer.
 */
interface FormLanguageChangerInterface
{
    /**
     * Change employee form language to given one and save the selection in the cookies.
     *
     * @param string $languageIsoCode two letter iso code of the language
     */
    public function changeLanguageInCookies(string $languageIsoCode): void;
}
