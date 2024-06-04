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

namespace PrestaShopBundle\Form\Admin\Type;

use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used as a container of sub forms, each sub form will be rendered as a part of navigation tab
 * component. Each first level child is used as a different tab, its label is used for the tab name, and it's widget
 * as the tab content.
 */
class NavigationTabType extends AbstractType
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @param LoggerInterface $logger
     * @param bool $isDebug
     */
    public function __construct(
        LoggerInterface $logger,
        bool $isDebug
    ) {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        if (!empty($options['toolbar_buttons'])) {
            $this->addButtonCollection($builder, '_toolbar_buttons', $options['toolbar_buttons'], $options['toolbar_options']);
        }
        if (!empty($options['footer_buttons'])) {
            $this->addButtonCollection($builder, '_footer_buttons', $options['footer_buttons'], $options['footer_options']);
        }
    }

    protected function addButtonCollection(FormBuilderInterface $builder, string $buttonCollectionName, array $buttons, array $buttonCollectionOptions): void
    {
        if ($builder->has($buttonCollectionName)) {
            if ($this->isDebug) {
                throw new InvalidConfigurationException(sprintf('You cannot add a field which name is %s on this component as it is used internally.', $buttonCollectionName));
            } else {
                $this->logger->warning(sprintf('You should not add a field which name is %s on this component as it is used internally.', $buttonCollectionName));
            }
        }

        $builder->add($buttonCollectionName, ButtonCollectionType::class, array_merge([
            'buttons' => $buttons,
        ], $buttonCollectionOptions));
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'toolbar_buttons' => [],
                'toolbar_options' => [],
                'footer_buttons' => [],
                'footer_options' => [],
            ])
            ->setAllowedTypes('toolbar_buttons', 'array')
            ->setAllowedTypes('toolbar_options', 'array')
            ->setAllowedTypes('footer_buttons', 'array')
            ->setAllowedTypes('footer_options', 'array')
        ;
    }
}
