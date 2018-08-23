<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Core\Language\Pack\Loader;

use PrestaShop\PrestaShop\Core\Foundation\Version;

/**
 * Class RemoteLanguagePackLoader is responsible for retrieving language pack data from remote host
 */
final class RemoteLanguagePackLoader implements LanguagePackLoaderInterface
{
    /**
     * The link from which available languages are retrieved
     */
    const PACK_LINK = 'http://i18n.prestashop.com/translations/%ps_version%/available_languages.json';

    /**
     * @var Version
     */
    private $version;

    /**
     * @param Version $version
     */
    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    /**
     * @inheritDoc
     */
    public function getLanguagePackList()
    {
        $normalizedLink = str_replace('%ps_version%', $this->version->getVersion(), self::PACK_LINK);
        $jsonResponse = file_get_contents($normalizedLink);

        $result = [];
        if ($jsonResponse) {
            $result = json_decode($jsonResponse, true);
        }

        return $result;
    }
}
