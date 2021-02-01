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

namespace PrestaShopBundle\Translation;

use Exception;
use PrestaShopBundle\Translation\DTO\DomainTranslation;
use PrestaShopBundle\Translation\DTO\MessageTranslation;
use PrestaShopBundle\Translation\DTO\Translations;
use PrestaShopBundle\Translation\Exception\UnexpectedTranslationTypeException;
use PrestaShopBundle\Translation\Provider\CatalogueProviderFactory;

class TranslationCatalogueBuilder
{
    public const TYPE_BACK = 'back';
    public const TYPE_FRONT = 'front';
    public const TYPE_MAILS = 'mails';
    public const TYPE_MAILS_BODY = 'mails_body';
    public const TYPE_OTHERS = 'others';
    public const TYPE_MODULES = 'modules';
    public const TYPE_THEMES = 'themes';

    public const ALLOWED_TYPES = [
        self::TYPE_BACK,
        self::TYPE_FRONT,
        self::TYPE_MAILS,
        self::TYPE_MAILS_BODY,
        self::TYPE_OTHERS,
        self::TYPE_MODULES,
        self::TYPE_THEMES,
    ];
    /**
     * @var CatalogueProviderFactory
     */
    private $catalogueProviderFactory;

    public function __construct(CatalogueProviderFactory $catalogueProviderFactory)
    {
        $this->catalogueProviderFactory = $catalogueProviderFactory;
    }

    /**
     * @param string $type
     * @param string $locale
     * @param string $domain
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     *
     * @return array
     *
     * @throws Exception
     */
    public function getDomainCatalogue(
        string $type,
        string $locale,
        string $domain,
        array $search,
        ?string $theme,
        ?string $module
    ): array {
        $this->validateParameters($type, $locale, $search, $theme, $module, $domain);

        $provider = $this->catalogueProviderFactory->getProvider($type);

        $defaultCatalogue = $provider->getDefaultCatalogue($locale)->all($domain);
        $fileTranslatedCatalogue = $provider->getFileTranslatedCatalogue($locale)->all($domain);
        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($locale)->all($domain);

        return $this->normalizeCatalogue(
            $defaultCatalogue,
            $fileTranslatedCatalogue,
            $userTranslatedCatalogue,
            $locale,
            $domain,
            $search,
            $theme
        );
    }

    /**
     * @param string $type
     * @param string $locale
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     *
     * @return array
     *
     * @throws Exception
     */
    public function getCatalogue(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module
    ): array {
        return $this->getCatalogueObject(
            $type,
            $locale,
            $search,
            $theme,
            $module
        )->toArray();
    }

    /**
     * @param string $type
     * @param string $locale
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     *
     * @return Translations
     *
     * @throws Exception
     */
    public function getCatalogueObject(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module
    ): Translations {
        $this->validateParameters($type, $locale, $search, $theme, $module);

        $provider = $this->catalogueProviderFactory->getProvider($type);

        $defaultCatalogue = $provider->getDefaultCatalogue($locale);
        $defaultCatalogueMessages = $defaultCatalogue->all();
        if (empty($defaultCatalogueMessages)) {
            return new Translations();
        }
        $fileTranslatedCatalogue = $provider->getFileTranslatedCatalogue($locale);
        $userTranslatedCatalogue = $provider->getUserTranslatedCatalogue($locale);

        $translations = new Translations();

        foreach ($defaultCatalogueMessages as $domain => $messages) {
            $domainTranslation = new DomainTranslation($domain);

            foreach ($messages as $translationKey => $translationValue) {
                $messageTranslation = new MessageTranslation($translationKey);
                if ($fileTranslatedCatalogue->defines($translationKey, $domain)) {
                    $messageTranslation->setFileTranslation($fileTranslatedCatalogue->get($translationKey, $domain));
                }
                if ($userTranslatedCatalogue->defines($translationKey, $domain)) {
                    $messageTranslation->setUserTranslation($userTranslatedCatalogue->get($translationKey, $domain));
                }
                // if search is empty or is in catalog default|xliff|database
                if (empty($search) || $messageTranslation->contains($search)) {
                    $domainTranslation->addMessageTranslation($messageTranslation);
                }
            }

            $translations->addDomainTranslation($domainTranslation);
        }

        return $translations;
    }

    /**
     * @param string $type
     * @param string $locale
     * @param array $search
     * @param string|null $theme
     * @param string|null $module
     * @param string|null $domain
     *
     * @throws UnexpectedTranslationTypeException
     */
    private function validateParameters(
        string $type,
        string $locale,
        array $search,
        ?string $theme,
        ?string $module,
        ?string $domain = null
    ): void {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new UnexpectedTranslationTypeException('This \'type\' param is not valid.');
        }
        if (self::TYPE_MODULES === $type && empty($module)) {
            throw new \InvalidArgumentException('This \'selected\' param is not valid. Module must be given.');
        }
        if (self::TYPE_THEMES === $type && empty($theme)) {
            throw new \InvalidArgumentException('This \'selected\' param is not valid. Theme must be given.');
        }
        if (null !== $domain && empty($domain)) {
            throw new \InvalidArgumentException('The given \'domain\' is not valid.');
        }
    }

    /**
     * @param array $defaultCatalogue
     * @param array $fileTranslatedCatalogue
     * @param array $userTranslatedCatalogue
     * @param string $locale
     * @param string $domain
     * @param array $search
     * @param string $theme
     *
     * @return array[]
     */
    private function normalizeCatalogue(
        array $defaultCatalogue,
        array $fileTranslatedCatalogue,
        array $userTranslatedCatalogue,
        string $locale,
        string $domain,
        array $search,
        string $theme
    ): array {
        $domainTranslation = new DomainTranslation($domain);

        foreach ($defaultCatalogue as $key => $message) {
            $messageTranslation = new MessageTranslation($key);

            if (array_key_exists($key, (array) $fileTranslatedCatalogue)) {
                $messageTranslation->setFileTranslation($fileTranslatedCatalogue[$key]);
            }
            if (array_key_exists($key, (array) $userTranslatedCatalogue)) {
                $messageTranslation->setUserTranslation($userTranslatedCatalogue[$key]);
            }

            // if search is empty or is in catalog default|xlf|database
            if (empty($search) || $messageTranslation->contains($search)) {
                $domainTranslation->addMessageTranslation($messageTranslation);
            }
        }

        return [
            'info' => [
                'locale' => $locale,
                'domain' => $domain,
                'theme' => $theme,
                'total_translations' => $domainTranslation->getTranslationsCount(),
                'total_missing_translations' => $domainTranslation->getMissingTranslationsCount(),
            ],
            'data' => $domainTranslation->toArray(false),
        ];
    }
}
