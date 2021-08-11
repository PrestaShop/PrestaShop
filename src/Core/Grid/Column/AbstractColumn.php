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

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbtractColumn implements reusable column methods.
 */
abstract class AbstractColumn implements ColumnInterface
{
    /**
     * @const string default column content template path
     */
    public const BASE_COLUMN_CONTENT_TEMPLATE_PATH = '@PrestaShop/Admin/Common/Grid/Columns/Content';

    /**
     * @const string default column header template path
     */
    public const BASE_COLUMN_HEADER_TEMPLATE_PATH = '@PrestaShop/Admin/Common/Grid/Columns/Header/Content';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->resolveOptions($options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (null === $this->options) {
            $this->resolveOptions();
        }

        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(string $name)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        throw new NoSuchOptionException(sprintf('Option "%s" does not exist in "%s"', $name, get_class($this)));
    }

    /**
     * Column content template path
     *
     * @return string
     */
    protected function getContentTemplatePath(): string
    {
        return self::BASE_COLUMN_CONTENT_TEMPLATE_PATH;
    }

    /**
     * Column header template path
     *
     * @return string
     */
    protected function getHeaderTemplatePath(): string
    {
        return self::BASE_COLUMN_HEADER_TEMPLATE_PATH;
    }

    /**
     * Default column options configuration. You can override or extend it needed options.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'sortable' => true,
                'clickable' => false,
                'alignment' => 'left',
                'content_template_path' => $this->getContentTemplatePath(),
                'header_template_path' => $this->getHeaderTemplatePath(),
            ])
            ->setAllowedTypes('sortable', 'bool')
            ->setAllowedTypes('clickable', 'bool')
            ->setAllowedTypes('alignment', 'string')
            ->setAllowedValues('alignment', ['center', 'left', 'right', 'justify'])
            ->setAllowedTypes('content_template_path', 'string')
            ->setAllowedTypes('header_template_path', 'string');
    }

    /**
     * Resolve column options.
     *
     * @param array $options
     */
    private function resolveOptions(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }
}
