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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Adds the "external_link" option to all Form Types.
 *
 * You can use it together with the UI kit form theme to add external links:
 *
 * ```
 * 'external_link' => [
 *   'link' => 'foo bar',
 *   'text' => 'foo bar',
 * ],
 * ```
 */
class ExternalLinkExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'external_link' => null,
            ])
            ->setAllowedTypes('external_link', ['null', 'array'])
            ->setNormalizer('external_link', function (Options $options, $value) {
                if (null === $value) {
                    return null;
                }

                $resolver = $this->getExternalLinkResolver();

                return $resolver->resolve($value);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!empty($options['external_link'])) {
            $view->vars['external_link'] = $options['external_link'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    /**
     * @return OptionsResolver
     */
    private function getExternalLinkResolver(): OptionsResolver
    {
        $externalLinkResolver = new OptionsResolver();
        $externalLinkResolver
            ->setRequired(['href', 'text'])
            ->setDefaults([
                'attr' => [],
                'align' => 'left',
                'position' => 'append',
                'open_in_new_tab' => true,
            ])
            ->setAllowedTypes('href', 'string')
            ->setAllowedTypes('text', 'string')
            ->setAllowedTypes('align', 'string')
            ->setAllowedTypes('position', 'string')
            ->setAllowedTypes('attr', ['null', 'array'])
            ->setAllowedValues('position', ['append', 'prepend', 'below'])
            ->setAllowedTypes('open_in_new_tab', 'bool')
        ;

        return $externalLinkResolver;
    }
}
