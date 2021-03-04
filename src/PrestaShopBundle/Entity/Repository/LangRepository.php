<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Lang;

class LangRepository extends EntityRepository implements LanguageRepositoryInterface
{
    public const ISO_CODE = 'isoCode';
    public const LOCALE = 'locale';

    /**
     * Stores language instances in different arrays to match them quickly
     * via a criteria and avoid multiple database queries.
     *
     * @var array
     */
    private $matches = [
        self::ISO_CODE => [],
        self::LOCALE => [],
    ];

    /**
     * @param string $isoCode
     *
     * @return string
     */
    public function getLocaleByIsoCode($isoCode)
    {
        $language = $this->searchLanguage(self::ISO_CODE, $isoCode);

        return $language->getLocale();
    }

    /**
     * @param string $locale
     *
     * @return Lang|null
     */
    public function getOneByLocale($locale)
    {
        return $this->searchLanguage(self::LOCALE, $locale);
    }

    /**
     * @param string $isoCode
     *
     * @return Lang|null
     */
    public function getOneByIsoCode($isoCode)
    {
        return $this->searchLanguage(self::ISO_CODE, $isoCode);
    }

    /**
     * @param string $locale
     *
     * @return Lang|null
     */
    public function getOneByLocaleOrIsoCode($locale)
    {
        $language = $this->getOneByLocale($locale);
        if (!$language) {
            $localeParts = explode('-', $locale);
            $isoCode = strtolower($localeParts[0]);
            $language = $this->getOneByIsoCode($isoCode);
        }

        return $language;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return Lang|null
     */
    private function searchLanguage($key, $value)
    {
        if (isset($this->matches[$key][$value])) {
            return $this->matches[$key][$value];
        }

        $language = $this->findOneBy([$key => $value]);
        if ($language) {
            $this->matches[self::ISO_CODE][$language->getIsoCode()] = $language;
            $this->matches[self::LOCALE][$language->getLocale()] = $language;
        }

        return $language;
    }
}
