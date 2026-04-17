<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Adds the "columns_number" option to all Form Types.
 *
 * You can use it together with the UI kit form theme to adapt the display of a form group into columns,
 * the form theme will add a class that affects the display into flex container with fixed size for sub elements.
 *
 * ```
 * 'columns_number' => 4,
 * ```
 */
class ColumnsNumberExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'columns_number' => null,
                'column_breaker' => false,
            ])
            ->setAllowedTypes('columns_number', ['null', 'int'])
            ->setAllowedTypes('column_breaker', ['bool'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!empty($options['columns_number'])) {
            $view->vars['columns_number'] = $options['columns_number'];
        }
        $view->vars['column_breaker'] = $options['column_breaker'];
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
