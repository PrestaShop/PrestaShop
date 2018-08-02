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

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

use PrestaShop\PrestaShop\Adapter\Language\LanguagePack;
use PrestaShop\PrestaShop\Adapter\Validate;

/**
 * Class LanguagePackImporter is responsible for importing language pack
 */
class LanguagePackImporter implements LanguagePackImporterInterface
{
    /**
     * @var Validate
     */
    private $validate;
    /**
     * @var LanguagePack
     */
    private $languagePack;

    public function __construct(Validate $validate, LanguagePack $languagePack)
    {
        $this->validate = $validate;
        $this->languagePack = $languagePack;
    }

    /**
     * @inheritDoc
     */
    public function import(LanguagePackImportConfigInterface $config)
    {
        $isoCode = $config->getIsoCode();
        $isValidIso = $this->validate->isLangIsoCode($isoCode);

        if (!$isValidIso) {
            return [];
        }

        $result = $this->languagePack->downloadAndInstallLanguagePack($isoCode);
        return is_array($result) && !empty($result) ? $result : [];
    }

}
