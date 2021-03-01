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

namespace PrestaShopBundle\Twig\Extension;

use RuntimeException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ExistsLoaderInterface;
use Twig_SimpleFunction as SimpleFunction;

/**
 * Class GridExtension is responsible for providing grid helpers functions.
 *
 * - column_content(column, record, grid): renders column content based on column type.
 * - column_header(column, grid): renders column header based on column type.
 */
class GridExtension extends AbstractExtension
{
    public const BASE_COLUMN_CONTENT_TEMPLATE_PATH = '@PrestaShop/Admin/Common/Grid/Columns/Content';
    public const BASE_COLUMN_HEADER_TEMPLATE_PATH = '@PrestaShop/Admin/Common/Grid/Columns/Header/Content';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @param Environment $twig
     * @param AdapterInterface $cache
     */
    public function __construct(Environment $twig, AdapterInterface $cache)
    {
        $this->twig = $twig;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new SimpleFunction('column_content', [$this, 'renderColumnContent'], [
                'is_safe' => ['html'],
            ]),
            new SimpleFunction('column_header', [$this, 'renderColumnHeader'], [
                'is_safe' => ['html'],
            ]),
            new SimpleFunction('is_ordering_column', [$this, 'isOrderingColumn'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * Render column content.
     *
     * @param array $record
     * @param array $column
     * @param array $grid
     *
     * @return string
     *
     * @throws RuntimeException when template cannot be found for column
     */
    public function renderColumnContent(array $record, array $column, array $grid)
    {
        $templateCacheKey = sprintf('column_%s_%s_%s_content', $grid['id'], $column['id'], $column['type']);

        if (false === $this->cache->hasItem($templateCacheKey)) {
            $template = $this->getTemplatePath(
                $column,
                $grid,
                self::BASE_COLUMN_CONTENT_TEMPLATE_PATH
            );

            if (null === $template) {
                throw new RuntimeException(sprintf('Content template for column type "%s" was not found', $column['type']));
            }

            $this->cache->save(
                $this->cache
                    ->getItem($templateCacheKey)
                    ->set($template)
            );
        }

        return $this->twig->render($this->cache->getItem($templateCacheKey)->get(), [
            'column' => $column,
            'record' => $record,
            'grid' => $grid,
        ]);
    }

    /**
     * Render column header.
     *
     * @param array $column
     * @param array $grid
     *
     * @return string
     */
    public function renderColumnHeader(array $column, array $grid)
    {
        $templateCacheKey = sprintf(
            'column_%s_%s_%s_header',
            $grid['id'],
            $column['id'],
            $column['type']
        );

        if (!$this->cache->hasItem($templateCacheKey)) {
            $template = $this->getTemplatePath(
                $column,
                $grid,
                self::BASE_COLUMN_HEADER_TEMPLATE_PATH,
                'default.html.twig'
            );

            $this->cache->save(
                $this->cache
                    ->getItem($templateCacheKey)
                    ->set($template)
            );
        }

        return $this->twig->render($this->cache->getItem($templateCacheKey)->get(), [
            'column' => $column,
            'grid' => $grid,
        ]);
    }

    /**
     * @param array $grid
     *
     * @return bool
     */
    public function isOrderingColumn(array $grid)
    {
        if (empty($grid['columns'])
            || empty($grid['sorting']['order_by'])
            || empty($grid['sorting']['order_way'])) {
            return false;
        }

        foreach ($grid['columns'] as $column) {
            if ('position' == $column['type']) {
                $positionField = $column['id'];
                if (strtolower($positionField) == strtolower($grid['sorting']['order_by'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get template for column.
     *
     * @param array $column
     * @param array $grid
     * @param string $basePath
     * @param string|null $defaultTemplate
     *
     * @return string|null
     */
    private function getTemplatePath(array $column, array $grid, $basePath, $defaultTemplate = null)
    {
        $gridId = $grid['id'];
        $columnId = $column['id'];
        $columnType = $column['type'];

        $columnGridTemplate = sprintf('%s/%s_%s_%s.html.twig', $basePath, $gridId, $columnId, $columnType);
        $gridTemplate = sprintf('%s/%s_%s.html.twig', $basePath, $gridId, $columnType);
        $columnTemplate = sprintf('%s/%s.html.twig', $basePath, $columnType);

        $loader = $this->twig->getLoader();
        if (!($loader instanceof ExistsLoaderInterface)) {
            return null;
        }

        if ($loader->exists($columnGridTemplate)) {
            return $columnGridTemplate;
        }

        if ($loader->exists($gridTemplate)) {
            return $gridTemplate;
        }

        if ($loader->exists($columnTemplate)) {
            return $columnTemplate;
        }

        if (null !== $defaultTemplate) {
            return sprintf('%s/%s', $basePath, $defaultTemplate);
        }

        return null;
    }
}
