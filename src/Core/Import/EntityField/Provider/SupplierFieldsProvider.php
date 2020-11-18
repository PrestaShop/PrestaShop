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

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class SupplierFieldsProvider defines a supplier fields provider.
 */
final class SupplierFieldsProvider implements EntityFieldsProviderInterface
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
            new EntityField('active', $this->trans('Active (0/1)', 'Admin.Advparameters.Feature')),
            new EntityField('name', $this->trans('Name', 'Admin.Global')),
            new EntityField('description', $this->trans('Description', 'Admin.Global')),
            new EntityField('short_description', $this->trans('Short description', 'Admin.Catalog.Feature')),
            new EntityField('meta_title', $this->trans('Meta title', 'Admin.Global')),
            new EntityField('meta_keywords', $this->trans('Meta keywords', 'Admin.Global')),
            new EntityField('meta_description', $this->trans('Meta description', 'Admin.Global')),
            new EntityField('image', $this->trans('Image URL', 'Admin.Advparameters.Feature')),
            new EntityField(
                'shop',
                $this->trans('ID / Name of group shop', 'Admin.Advparameters.Feature'),
                $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.', 'Admin.Advparameters.Help')
            ),
        ];

        return EntityFieldCollection::createFromArray($fields);
    }

    /**
     * A shorter name method for translations.
     *
     * @param string $id translation ID
     * @param string $domain translation domain
     *
     * @return string
     */
    private function trans($id, $domain = 'Admin.Advparameters.Feature')
    {
        return $this->translator->trans($id, [], $domain);
    }
}
