<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\EntityField\EntityField;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollection;
use Symfony\Contracts\Translation\TranslatorInterface;

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
            new EntityField('name', $this->trans('Name', 'Admin.Global'), '', true),
            new EntityField('description', $this->trans('Description', 'Admin.Global')),
            new EntityField('meta_title', $this->trans('Meta title', 'Admin.Global')),
            new EntityField('meta_description', $this->trans('Meta description', 'Admin.Global')),
            new EntityField('image', $this->trans('Image URL', 'Admin.Advparameters.Feature')),
            new EntityField(
                'shop',
                $this->trans('ID / Name of group shop', 'Admin.Advparameters.Feature'),
                $this->trans('Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default store will be used.', 'Admin.Advparameters.Help')
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
