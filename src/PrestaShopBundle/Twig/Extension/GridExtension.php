<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Twig\Extension;

use ErrorException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Extension\AbstractExtension;
use Twig_SimpleFunction as SimpleFunction;

/**
 * Class GridExtension is responsible for providing twig functions:
 *
 * 1. column_content(record, column, grid) - renders column content based on column type
 * 2. column_header(column, grid) - renders column header based on column type
 * 3. column_filter(column, grid) - renders column filter based on column type
 */
class GridExtension extends AbstractExtension
{
    const BASE_COLUMN_CONTENT_TEMPLATE_PATH = '@PrestaShop/Admin/Common/Grid/Columns/Content';
    const BASE_COLUMN_HEADER_TEMPLATE_PATH = '@PrestaShop/Admin/Common/Grid/Columns/Header/Content';

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @param EngineInterface $templating
     * @param AdapterInterface $cache
     */
    public function __construct(EngineInterface $templating, AdapterInterface $cache)
    {
        $this->templating = $templating;
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
        ];
    }

    /**
     * Render column content
     *
     * @param array $record
     * @param array $column
     * @param array $grid
     *
     * @return string
     *
     * @throws ErrorException when template cannot be found for column
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
                throw new ErrorException(sprintf('Content template for column type "%s" was not found', $column['type']));
            }

            $this->cache->save(
                $this->cache
                    ->getItem($templateCacheKey)
                    ->set($template)
            );
        }

        return $this->templating->render($this->cache->getItem($templateCacheKey)->get(), [
            'column' => $column,
            'record' => $record,
            'grid' => $grid,
        ]);
    }

    /**
     * Render column header
     *
     * @param array $column
     * @param array $grid
     *
     * @return string
     */
    public function renderColumnHeader(array $column, array $grid)
    {
        $templateCacheKey = sprintf('column_%s_%s_%s_header', $grid['id'], $column['id'], $column['type']);

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

        return $this->templating->render($this->cache->getItem($templateCacheKey)->get(), [
            'column' => $column,
            'grid' => $grid,
        ]);
    }

    /**
     * Get template for column
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
        $columnGridSpecificTemplate = sprintf('%s/%s_%s_%s.html.twig', $basePath, $grid['id'], $column['id'], $column['type']);
        $gridSpecificTemplate = sprintf('%s/%s_%s.html.twig', $basePath, $grid['id'], $column['type']);
        $columnTemplate = sprintf('%s/%s.html.twig', $basePath, $column['type']);

        $template = null;

        if ($this->templating->exists($columnGridSpecificTemplate)) {
            $template = $columnGridSpecificTemplate;
        } elseif ($this->templating->exists($gridSpecificTemplate)) {
            $template = $gridSpecificTemplate;
        } elseif ($this->templating->exists($columnTemplate)) {
            $template = $columnTemplate;
        } elseif (null !== $defaultTemplate) {
            $template = sprintf('%s/%s', $basePath, $defaultTemplate);
        }

        return $template;
    }
}
