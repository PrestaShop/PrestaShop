<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\Business\Context;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

class Translator implements TranslatorInterface
{
    private $context;
    private $locale;

    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->setLocale($this->context->language->iso_code);
        var_dump($this->context);
        die;
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $module     The domain: Legacy Admin controller name, module name or null to use the Core. Other special value: 'pdf'.
     * @param string|null $locale     The locale or null to use the default
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     *
     * @return string The translated string
     *
     * @api
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        // Very specific cases (PDF)
        if ($domain == 'pdf') {
            // Does not support overriding the language for this adapter!
            return Translate::getPdfTranslation($id, (count($parameters) === 0) ? null : $parameters);
        }

        // Search for Admin case
        $isAdmin = true; // ($this->context->controller->controller_type == 'admin' || ); // FIXME: trouver un moyen sur pour la nouvelle ARCHI !
        if ($isAdmin && $domain == null) {
            $domain = 'AdminTab'; // default class value for legacy Admin translation
        }
        if ($isAdmin) {
            $domain = preg_replace('/(c|C)ontroller$/', '', $domain); // remove trailing 'Controller'

            return Translate::getAdminTranslation($id, $domain, (count($parameters) === 0) ? null : $parameters);
        }

        // Front / Module case ?
        if ($domain !== null) {
            throw new DevelopmentErrorException('Module translation is not yet implemented. Please contact the Architect team.');
        }
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     *
     * @return string The translated string
     *
     * @api
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        // TODO : utile ?
    }

    /**
     * Sets the current locale.
     *
     * @param string $locale The locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Returns the current locale.
     *
     * @return string The locale
     */
    public function getLocale()
    {
        return $locale;
    }
}
