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

namespace PrestaShopBundle\Translation;

use Symfony\Component\Translation\DataCollectorTranslator as BaseTranslator;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * This is the decorator of the framework DataCollectorTranslator service, it is required mostly
 * for the PrestaShopTranslatorTrait that handles fallback on legacy translation system when useful.
 *
 * We need to explicitly implement some method even if they are just proxies because the TranslatorLanguageLoader
 * checks their presence before calling them.
 */
class DataCollectorTranslator extends BaseTranslator implements TranslatorInterface
{
    use PrestaShopTranslatorTrait;

    public function addLoader(string $format, LoaderInterface $loader)
    {
        return $this->__call('addLoader', [$format, $loader]);
    }

    public function addResource(string $format, mixed $resource, string $locale, ?string $domain = null)
    {
        return $this->__call('addResource', [$format, $resource, $locale, $domain]);
    }

    /**
     * {@inheritdoc}
     */
    public function isLanguageLoaded($locale)
    {
        return $this->__call('isLanguageLoaded', [$locale]);
    }

    /**
     * {@inheritdoc}
     */
    public function clearLanguage($locale)
    {
        return $this->__call('clearLanguage', [$locale]);
    }
}
