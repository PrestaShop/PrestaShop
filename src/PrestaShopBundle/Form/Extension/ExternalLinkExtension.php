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
    public function configureOptions(OptionsResolver $resolver): void
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
            ])
            ->setAllowedTypes('href', 'string')
            ->setAllowedTypes('text', 'string')
            ->setAllowedTypes('align', 'string')
            ->setAllowedTypes('position', 'string')
            ->setAllowedTypes('attr', ['null', 'array'])
            ->setAllowedValues('position', ['append', 'prepend'])
        ;

        return $externalLinkResolver;
    }
}
