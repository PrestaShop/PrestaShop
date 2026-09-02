<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * Returns all the mapping for all installed languages, the returned array is indexed by Language ID,
     * it contains an array with Language info, only locale is relevant for now but it may evolve in the future.
     *
     * @return array<int, array{'locale': string}>
     */
    public function getMapping(): array
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('l.id, l.locale');
        $result = $qb->getQuery()->getArrayResult();

        $mapping = [];
        foreach ($result as $row) {
            $mapping[$row['id']] = [
                'locale' => $row['locale'],
            ];
        }

        return $mapping;
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
