<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Lang;

class LangRepository extends EntityRepository implements LanguageRepositoryInterface
{
    const ISO_CODE = 'isoCode';
    const LOCALE = 'locale';

    /**
     * Stores language instances in different arrays to match them quickly
     * via a criteria and avoid multiple database queries.
     *
     * @var array
     */
    private $matches;

    /**
     * @param EntityManager $em
     * @param Mapping\ClassMetadata $class
     */
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->matches = [
            static::ISO_CODE => [],
            static::LOCALE => [],
        ];
    }

    /**
     * @param string $isoCode
     *
     * @return string
     */
    public function getLocaleByIsoCode($isoCode)
    {
        $language = $this->searchLanguage(static::ISO_CODE, $isoCode);

        return $language->getLocale();
    }

    /**
     * @inheritDoc
     */
    public function getByLocale($locale)
    {
        return $this->searchLanguage(static::LOCALE, $locale);
    }

    /**
     * @inheritDoc
     */
    public function getByIsoCode($isoCode)
    {
        return $this->searchLanguage(static::ISO_CODE, $isoCode);
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return Lang
     */
    private function searchLanguage($key, $value)
    {
        if (isset($this->matches[$key][$value])) {
            return $this->matches[$key][$value];
        }

        /** @var Lang $language */
        $language = $this->findOneBy([$key => $value]);
        if ($language) {
            $this->matches[static::ISO_CODE][$language->getIsoCode()] = $language;
            $this->matches[static::LOCALE][$language->getLocale()] = $language;
        }

        return $language;
    }
}
