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

namespace PrestaShop\PrestaShop\Twig\Extension;

use Symfony\Component\Translation\TranslatorInterface;

class TranslationExtension extends \Twig_Extension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans', array($this, 'trans')),
            new \Twig_SimpleFilter('transchoice', array($this, 'transchoice'))
        );
    }

    private function getPrestaShopTranslation($message)
    {
        return \Tools::displayError($message, false);
    }

    public function trans($message, array $arguments = array(), $domain = null, $locale = null)
    {
        return $this->translator->trans($this->getPrestaShopTranslation($message), $arguments, $domain, $locale);
    }

    public function transchoice($message, $count, array $arguments = array(), $domain = null, $locale = null)
    {
        return $this->translator->transChoice($this->getPrestaShopTranslation($message), $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
    }

    public function getName()
    {
        return 'twig_translation_extension';
    }
}
