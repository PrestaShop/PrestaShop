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

namespace PrestaShop\PrestaShop\Core\Language\Copier;

/**
 * Class LanguageCopierConfig provides configuration for language copier.
 */
final class LanguageCopierConfig implements LanguageCopierConfigInterface
{
    /**
     * @var string the theme name from which the language will be copied
     */
    private $themeFrom;

    /**
     * @var string the language iso code, which will be copied from
     */
    private $languageFrom;

    /**
     * @var string the theme name to which the language will be copied
     */
    private $themeTo;

    /**
     * @var string the language iso code, which will be copied to
     */
    private $languageTo;

    /**
     * @param string $themeFrom
     * @param string $languageFrom
     * @param string $themeTo
     * @param string $languageTo
     */
    public function __construct($themeFrom, $languageFrom, $themeTo, $languageTo)
    {
        $this->themeFrom = $themeFrom;
        $this->languageFrom = $languageFrom;
        $this->themeTo = $themeTo;
        $this->languageTo = $languageTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeFrom()
    {
        return $this->themeFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageFrom()
    {
        return $this->languageFrom;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeTo()
    {
        return $this->themeTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageTo()
    {
        return $this->languageTo;
    }
}
