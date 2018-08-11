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

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ValidateConstraintTranslatorCore
 */
class ValidateConstraintTranslatorCore
{
    private $translator;

    /**
     * ValidateConstraintTranslatorCore constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $validator
     *
     * @return string
     */
    public function translate($validator)
    {
        if ($validator === 'isName') {
            return $this->translator->trans(
                'Invalid name', array(), 'Shop.Forms.Errors'
            );
        } elseif ($validator === 'isBirthDate') {
            return $this->translator->trans(
                'Format should be %s.', array(Tools::formatDateStr('31 May 1970')), 'Shop.Forms.Errors'
            );
        } elseif ($validator === 'required') {
            return $this->translator->trans(
                'Required field', array(), 'Shop.Forms.Errors'
            );
        }

        return $this->translator->trans(
            'Invalid format.',
            array(),
            'Shop.Forms.Errors'
        );
    }
}
