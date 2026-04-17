<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder;

use InvalidArgumentException;
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
            throw new InvalidArgumentException('Given translation domains are invalid. An array of strings was expected.');
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
