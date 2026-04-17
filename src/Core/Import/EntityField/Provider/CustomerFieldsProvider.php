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
