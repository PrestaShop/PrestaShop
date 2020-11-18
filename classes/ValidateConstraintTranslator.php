<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ValidateConstraintTranslatorCore.
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
                'Invalid name',
                [],
                'Shop.Forms.Errors'
            );
        }

        if ($validator === 'isCustomerName') {
            return $this->translator->trans(
                'Invalid name',
                [],
                'Shop.Forms.Errors'
            ) . PHP_EOL .
            $this->translator->trans(
                'Invalid characters: 0-9!<>,;?=+()@#"°{}_$%/\^*`',
                [],
                'Shop.Forms.Errors'
            ) . PHP_EOL .
            $this->translator->trans(
                'A space is required after "." and "。"',
                [],
                'Shop.Forms.Errors'
            );
        }

        if ($validator === 'isBirthDate') {
            return $this->translator->trans(
                'Format should be %s.',
                [Tools::formatDateStr('31 May 1970')],
                'Shop.Forms.Errors'
            );
        }

        if ($validator === 'required') {
            return $this->translator->trans(
                'Required field',
                [],
                'Shop.Forms.Errors'
            );
        }

        return $this->translator->trans(
            'Invalid format.',
            [],
            'Shop.Forms.Errors'
        );
    }
}
