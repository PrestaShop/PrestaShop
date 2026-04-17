<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResizableTextType adds new sizing options to TextType.
 */
class ResizableTextTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [TextType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefined('size')
            ->setAllowedValues(
                'size',
                [
                    'small',
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['size'])) {
            $sizeClass = 'size-' . $options['size'];

            if (!isset($view->vars['attr']['class'])) {
                $view->vars['attr']['class'] = '';
            }

            $view->vars['attr']['class'] = trim($view->vars['attr']['class'] . ' ' . $sizeClass);
        }
    }
}
