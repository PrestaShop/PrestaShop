<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;

class TranslationsProvider
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string
     */
    private $resourceDirectory;

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory
    ) {
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
    }

    /**
     * @param string $type
     * @param string $locale
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue(string $type, string $locale, bool $empty = true): MessageCatalogueInterface
    {
        if (!in_array($type, ['front', 'modules', 'mails', 'mails_body', 'back', 'others'])) {
            throw new \LogicException("The 'type' parameter is not valid. $type given");
        }

        $provider = new DefaultCatalogueProvider(
            $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
            $this->getFilenameFilters($type)
        );

        return $provider->getDefaultCatalogue($locale, $empty);
    }

    /**
     * @param string $type
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getFileTranslatedCatalogue(string $type, string $locale): MessageCatalogueInterface
    {
        if (!in_array($type, ['front', 'modules', 'mails', 'mails_body', 'back', 'others'])) {
            throw new \LogicException("The 'type' parameter is not valid. $type given");
        }

        $provider = new FileTranslatedCatalogueProvider(
            $this->resourceDirectory,
            $this->getFilenameFilters($type)
        );

        return $provider->getFileTranslatedCatalogue($locale);
    }

    /**
     * @param string $type
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $type, string $locale): MessageCatalogueInterface
    {
        if (!in_array($type, ['front', 'modules', 'mails', 'mails_body', 'back', 'others'])) {
            throw new \LogicException("The 'type' parameter is not valid. $type given");
        }

        $provider = new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $this->getTranslationDomains($type)
        );

        return $provider->getUserTranslatedCatalogue($locale);
    }

    /**
     * @param string $type
     *
     * @return array|string[]
     */
    private function getFilenameFilters(string $type): array
    {
        $filenameFilters = [];

        switch ($type) {
            case 'back':
                $filenameFilters = [
                    '#^Admin[A-Z]#',
                    '#^Modules[A-Z](.*)Admin#',
                ];
                break;
            case 'front':
                $filenameFilters = [
                    '#^Shop*#',
                    '#^Modules(.*)Shop#',
                ];
                break;
            case 'modules':
                $filenameFilters = ['#^Modules[A-Z]#'];
                break;
            case 'mails':
                $filenameFilters = ['#EmailsSubject*#'];
                break;
            case 'mails_body':
                $filenameFilters = ['#EmailsBody*#'];
                break;
            case 'others':
                $filenameFilters = ['#^messages*#'];
                break;
        }

        return $filenameFilters;
    }

    /**
     * @param string $type
     *
     * @return array|string[]
     */
    private function getTranslationDomains(string $type): array
    {
        $translationDomains = [];

        switch ($type) {
            case 'back':
                $translationDomains = [
                    '^Admin[A-Z]',
                    '^Modules[A-Z](.*)Admin',
                ];
                break;
            case 'front':
                $translationDomains = [
                    '^Shop*',
                    '^Modules(.*)Shop',
                ];
                break;
            case 'modules':
                $translationDomains = ['^Modules[A-Z]'];
                break;
            case 'mails':
                $translationDomains = ['EmailsSubject*'];
                break;
            case 'mails_body':
                $translationDomains = ['EmailsBody*'];
                break;
            case 'others':
                $translationDomains = ['^messages*'];
                break;
        }

        return $translationDomains;
    }
}
