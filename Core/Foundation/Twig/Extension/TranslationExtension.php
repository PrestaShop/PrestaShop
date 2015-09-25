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

namespace PrestaShop\PrestaShop\Core\Foundation\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * This class is used by Twig_Environment and provide some methods callable from a twig template
 */
class TranslationExtension extends \Twig_Extension
{
    private $translator;

    /**
     * Constructor : Inject Symfony\Component\Translation Translator
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Define available filters
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans', array($this, 'trans')),
            new \Twig_SimpleFilter('transchoice', array($this, 'transchoice'))
        );
    }

    /**
     * This method wrap the Tools::displayError legacy method
     *
     * @param string $message The string to translate
     *
     * @return string The translated string
     */
    final private function getPrestaShopTranslation($message)
    {
        return \Tools::displayError($message, false);
        // FIXME: vers Adapter
    }

    /**
     * Translates the given message
     *
     * @param string $message The string to translate
     * @param array $arguments An array of parameters for the message
     * @param string|null $domain The domain for the message or null to use the default
     * @param string|null $locale The locale or null to use the default
     *
     * @return string The translated string
     */
    public function trans($message, array $arguments = array(), $domain = null, $locale = null)
    {
        return $this->translator->trans($this->getPrestaShopTranslation($message), $arguments, $domain, $locale);
    }

    /**
     * Translates the given message by choosing a translation according to a number
     *
     * @param string $message The string to translate
     * @param int $count The number to use to find the indice of the message
     * @param array $arguments An array of parameters for the message
     * @param string|null $domain The domain for the message or null to use the default
     * @param string|null $locale The locale or null to use the default
     *
     * @return string The translated string
     */
    public function transchoice($message, $count, array $arguments = array(), $domain = null, $locale = null)
    {
        return $this->translator->transChoice($this->getPrestaShopTranslation($message), $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_translation_extension';
    }
}
