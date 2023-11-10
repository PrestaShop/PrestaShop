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

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CustomerFieldsProvider defines a customer fields provider.
 */
final class CustomerFieldsProvider implements EntityFieldsProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $fields = [
            new EntityField('id', $this->trans('ID', 'Admin.Global')),
            new EntityField('active', $this->trans('Active  (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('id_gender', $this->trans('Titles ID (Mr = 1, Ms = 2, else 0)', 'Admin.Advparameters.Feature')),
            new EntityField('email', $this->trans('Email', 'Admin.Global'), '', true),
            new EntityField('passwd', $this->trans('Password', 'Admin.Global'), '', true),
            new EntityField('birthday', $this->trans('Birth date (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField('lastname', $this->trans('Last name', 'Admin.Global'), '', true),
            new EntityField('firstname', $this->trans('First name', 'Admin.Global'), '', true),
            new EntityField('newsletter', $this->trans('Newsletter (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('optin', $this->trans('Partner offers (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('date_add', $this->trans('Registration date (yyyy-mm-dd)', 'Admin.Advparameters.Feature')),
            new EntityField('group', $this->trans('Groups (x,y,z...)', 'Admin.Advparameters.Feature')),
            new EntityField('id_default_group', $this->trans('Default group ID', 'Admin.Advparameters.Feature')),
            new EntityField(
                'id_shop',
                $this->trans('ID / Name of the store', 'Admin.Advparameters.Feature'),
                $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', 'Admin.Advparameters.Help')
            ),
        ];

        return EntityFieldCollection::createFromArray($fields);
    }

    /**
     * A shorter name method for translations.
     *
     * @param string $key translation ID
     * @param string $domain translation domain
     *
     * @return string
     */
    private function trans(string $key, string $domain = 'Admin.Advparameters.Feature'): string
    {
        return $this->translator->trans($key, [], $domain);
    }
}
