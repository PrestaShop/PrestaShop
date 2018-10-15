<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MoneyWithSuffixType is a money type,
 * which also has a suffix string right after the currency sign.
 */
class MoneyWithSuffixType extends MoneyType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $pattern = self::getPattern($options['currency']);

        // Currency sign in the pattern string can be either to the left,
        // or to the right of the {{ widget }}, depending on the currency.
        $tokens = explode('{{ widget }}', $pattern);

        // The only non-empty token will be the currency sign - append the suffix to it
        array_walk($tokens, [$this, 'applySuffix'], $options['suffix']);

        // Restore the pattern as it was before,
        // just the suffix is already appended after the currency sign
        $view->vars['money_pattern'] = implode('{{ widget }}', $tokens);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('suffix', '');
    }

    /**
     * Appends the suffix to the value, if the value is not empty.
     * Can be used with array_walk().
     *
     * @param string $value
     * @param int $key not used, it's only here to make this method compatible with array_walk
     * @param string $suffix
     */
    private function applySuffix(&$value, $key, $suffix)
    {
        if (strlen($value) > 0) {
            $value = rtrim($value) . ' ' . $suffix;
        }
    }
}
