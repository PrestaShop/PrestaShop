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
        $columnType = $column['type'];
        $columnId = $column['id'];
        $gridId = $grid['id'];
        $templateCacheKey = sprintf('%s_%s_%s.html.twig', $gridId, $columnId, $columnType);

        if ($this->cache->hasItem($templateCacheKey)) {
            return $this->templating->render($this->cache->getItem($templateCacheKey)->get(), [
                'column' => $column,
                'row' => $record,
                'grid' => $grid,
            ]);
        }

        $columnGridSpecificTemplate = sprintf('%s/%s_%s_%s.html.twig', self::BASE_COLUMN_CONTENT_TEMPLATE_PATH, $gridId, $columnId, $columnType);
        $gridSpecificTemplate = sprintf('%s/%s_%s.html.twig', self::BASE_COLUMN_CONTENT_TEMPLATE_PATH, $gridId, $columnType);
        $columnTemplate = sprintf('%s/%s.html.twig', self::BASE_COLUMN_CONTENT_TEMPLATE_PATH, $columnType);

        $template = null;

        if ($this->templating->exists($columnGridSpecificTemplate)) {
            $template = $columnGridSpecificTemplate;
        } elseif ($this->templating->exists($gridSpecificTemplate)) {
            $template = $gridSpecificTemplate;
        } elseif ($this->templating->exists($columnTemplate)) {
            $template = $columnTemplate;
        }

        if (null === $template) {
            throw new ErrorException(sprintf('Content template for column type "%s" was not found.', $columnType));
        }

        $this->cache->save(
            $this->cache
                ->getItem($templateCacheKey)
                ->set($template)
        );

        return $this->templating->render($this->cache->getItem($templateCacheKey)->get(), [
            'column' => $column,
            'row' => $record,
            'grid' => $grid,
        ]);
    }
}
