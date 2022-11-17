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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RequestSqlSettingsType build form type for "Configure > Advanced Parameters > Database > SQL Manager" page.
 */
class SqlRequestSettingsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('default_file_encoding', ChoiceType::class, [
                'label' => $this->trans('Select your default file encoding', 'Admin.Advparameters.Feature'),
                'choices' => [
                    CharsetEncoding::UTF_8 => CharsetEncoding::UTF_8,
                    CharsetEncoding::ISO_8859_1 => CharsetEncoding::ISO_8859_1,
                ],
                'translation_domain' => false,
            ])
            ->add('enable_multi_statements', SwitchType::class, [
                'label' => $this->trans('Enable multi-statements queries', 'Admin.Advparameters.Feature'),
                'help' => $this->trans(
                    'Enabling multi-statements queries increases the risk of SQL injection vulnerability to be exploited',
                    'Admin.Advparameters.Help'
                ),
            ])
        ;
    }
}
