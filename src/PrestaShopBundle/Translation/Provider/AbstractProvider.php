<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Finder\TranslationFinder;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

abstract class AbstractProvider implements ProviderInterface, XliffCatalogueInterface, DatabaseCatalogueInterface
{
    public const DEFAULT_LOCALE = 'en-US';

    /**
     * @var LoaderInterface the loader interface
     */
    private $databaseLoader;

    /**
     * @var string the resource directory
     */
    protected $resourceDirectory;

    /**
     * @var string the Catalogue locale
     */
    protected $locale;

    /**
     * @var string the Catalogue domain
     */
    protected $domain;

    public function __construct(LoaderInterface $databaseLoader, $resourceDirectory)
    {
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->locale = self::DEFAULT_LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectories()
    {
        return [$this->getResourceDirectory()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return [''];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        $xlfCatalogue = $this->getXliffCatalogue();
        $messageCatalogue->addCatalogue($xlfCatalogue);

        $databaseCatalogue = $this->getDatabaseCatalogue();

        // Merge database catalogue to xliff catalogue
        $messageCatalogue->addCatalogue($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws TranslationFilesNotFoundException
     */
    public function getDefaultCatalogue($empty = true)
    {
        $defaultCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                [$this->getDefaultResourceDirectory()],
                $this->locale,
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        if ($empty && $this->locale !== self::DEFAULT_LOCALE) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws TranslationFilesNotFoundException
     */
    public function getXliffCatalogue()
    {
        $xlfCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                $this->getDirectories(),
                $this->locale,
                $filter
            );
            $xlfCatalogue->addCatalogue($filteredCatalogue);
        }

        return $xlfCatalogue;
    }

    /**
     * Get the Catalogue from database only.
     *
     * @param string|null $theme
     *
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function getDatabaseCatalogue($theme = null)
    {
        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            if (!($this->getDatabaseLoader() instanceof DatabaseTranslationLoader)) {
                continue;
            }
            $domainCatalogue = $this->getDatabaseLoader()->load(null, $this->locale, $translationDomain, $theme);

            if ($domainCatalogue instanceof MessageCatalogue) {
                $databaseCatalogue->addCatalogue($domainCatalogue);
            }
        }

        return $databaseCatalogue;
    }

    /**
     * @return string Path to app/Resources/translations/{locale}
     */
    public function getResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->locale;
    }

    /**
     * @return LoaderInterface
     */
    public function getDatabaseLoader()
    {
        return $this->databaseLoader;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogueInterface $messageCatalogue
     *
     * @return MessageCatalogueInterface Empty the catalogue
     */
    public function emptyCatalogue(MessageCatalogueInterface $messageCatalogue)
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }

    /**
     * @param string|array<string> $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws TranslationFilesNotFoundException
     */
    public function getCatalogueFromPaths($paths, $locale, $pattern = null)
    {
        return (new TranslationFinder())->getCatalogueFromPaths($paths, $locale, $pattern);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getDefaultResourceDirectory();
}
