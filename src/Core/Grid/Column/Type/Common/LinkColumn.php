<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LinkColumn is used to define a column in which there is a link targeting an action route (view, edit, add...).
 *
 * Example:
 * new LinkColumn('name'))
 *  ->setName('Name')
 *   ->setOptions([
 *       'field' => 'name',
 *       'route' => 'admin_edit',
 *       'route_param_name' => 'myId',
 *       'route_param_field' => 'id',
 *   ])
 */
final class LinkColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'link';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'icon' => null,
                'route_fragment' => null,
                'button_template' => false,
                'color_template' => 'primary',
                'color_template_field' => null,
            ])
            ->setRequired([
                'field',
                'route',
                'route_param_name',
                'route_param_field',
            ])
            ->setDefined([
                'icon',
                'target',
            ])
            ->setAllowedTypes('field', ['string', 'null'])
            ->setAllowedTypes('icon', ['string', 'null'])
            ->setAllowedTypes('target', ['string', 'null'])
            ->setAllowedTypes('color_template_field', ['string', 'null'])
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_fragment', ['string', 'null'])
            ->setAllowedTypes('route_param_name', 'string')
            ->setAllowedTypes('route_param_field', 'string')
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedValues('color_template', [
                'primary',
                'secondary',
                'success',
                'danger',
                'warning',
                'info',
            ])
            ->setAllowedValues('button_template', [
                false,
                'outline',
                'normal',
            ])
        ;
    }
}
