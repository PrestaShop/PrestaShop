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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class LanguageChoiceProvider provides languages choices with ID values.
 *
 * @todo this class could be merged with \PrestaShop\PrestaShop\Core\Form\ChoiceProvider\LanguageByIdChoiceProvider
 *       as this class can fully achieve the same behavior as the LanguageByIdChoiceProvider.
 *       It would break BC though, so couldn't be done at the moment.
 */
final class LanguageChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $languages;

    /**
     * @param array $languages
     */
    public function __construct(array $languages)
    {
        $this->languages = $languages;
    }

    /**
     * Get active language choices for form.
     *
     * @return array
     */
    public function getChoices()
    {
        $choices = [];

        foreach ($this->languages as $language) {
            $choices[$language['name']] = $language['id_lang'];
        }

        return $choices;
    }
}
