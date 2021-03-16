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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder;

use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Gets catalogue translated by the user himself ans stored in the database.
 */
class UserTranslatedCatalogueFinder extends AbstractCatalogueFinder
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationReader;

    /**
     * @var array<int, string>
     */
    private $translationDomains;

    /**
     * @var string|null
     */
    private $themeName;

    /**
     * You will need to give theme if you want only the translations linked to a specific theme.
     * If not given, the translations returns will be the ones with 'theme IS NULL'
     *
     * @param DatabaseTranslationLoader $databaseTranslationReader
     * @param array<int, string> $translationDomains
     * @param string|null $themeName
     */
    public function __construct(
        DatabaseTranslationLoader $databaseTranslationReader,
        array $translationDomains,
        ?string $themeName = null
    ) {
        if (!$this->assertIsArrayOfString($translationDomains)) {
            throw new \InvalidArgumentException('Given translation domains are invalid. An array of strings was expected.');
        }

        $this->databaseTranslationReader = $databaseTranslationReader;
        $this->translationDomains = $translationDomains;
        $this->themeName = $themeName;
    }

    /**
     * Returns the translation catalogue for the provided locale
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    public function getCatalogue(string $locale): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);

        foreach ($this->translationDomains as $translationDomain) {
            $domainCatalogue = $this->databaseTranslationReader->load(
                $locale,
                $translationDomain,
                $this->themeName
            );

            $catalogue->addCatalogue($domainCatalogue);
        }

        return $catalogue;
    }
}
