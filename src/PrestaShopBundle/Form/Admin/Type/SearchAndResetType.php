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

namespace PrestaShopBundle\Form\Admin\Type;

use LogicException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * FormType used in rendering of "Search and Reset" action in Grids.
 */
class SearchAndResetType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $showResetButton = false;

        if (null !== $form->getParent()) {
            $configuredTypeNames = array_keys($form->getParent()->all());
            $availableValueNames = array_keys($form->getParent()->getData());

            $configuredData = array_intersect($configuredTypeNames, $availableValueNames);
            if (!empty($configuredData)) {
                $showResetButton = true;
            }
        }

        $resetUrl = isset($options['attr']['data-url']) ? $options['attr']['data-url'] : null;
        $redirectUrl = isset($options['attr']['data-redirect']) ? $options['attr']['data-redirect'] : null;

        if (null !== $options['reset_route']) {
            $resetUrl = $this->urlGenerator->generate(
                $options['reset_route'],
                $options['reset_route_params']
            );
        }

        if (null !== $options['redirect_route']) {
            $redirectUrl = $this->urlGenerator->generate(
                $options['redirect_route'],
                $options['redirect_route_params']
            );
        }

        if (in_array(null, [$resetUrl, $redirectUrl])) {
            throw new LogicException(sprintf('You must configure "reset_route" and "redirect_route" options for "%s" type.', self::class));
        }

        $view->vars['show_reset_button'] = $showResetButton;
        $view->vars['redirect_url'] = $redirectUrl;
        $view->vars['reset_url'] = $resetUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'reset_route' => null,
                'reset_route_params' => [],
                'redirect_route' => null,
                'redirect_route_params' => [],
                'allow_extra_fields' => true,
            ])
            ->setAllowedTypes('reset_route', ['string', 'null'])
            ->setAllowedTypes('reset_route_params', 'array')
            ->setAllowedTypes('redirect_route', ['string', 'null'])
            ->setAllowedTypes('redirect_route_params', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'search_and_reset';
    }
}
