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

namespace PrestaShop\PrestaShop\Core\Search;

class SearchPanel implements SearchPanelInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $buttonLabel;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var array
     */
    protected $queryParams;
    /**
     * @var bool|null
     */
    private $isExternalLink;

    public function __construct(
        string $title,
        string $buttonLabel,
        string $link,
        array $queryParams,
        ?bool $isExternalLink = true
    ) {
        $this->title = $title;
        $this->buttonLabel = $buttonLabel;
        $this->link = $link;
        $this->queryParams = $queryParams;
        $this->isExternalLink = $isExternalLink;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getButtonLabel(): string
    {
        return $this->buttonLabel;
    }

    public function getLink(): string
    {
        return sprintf('%s?%s', $this->link, http_build_query($this->queryParams));
    }

    public function isExternalLink(): bool
    {
        return $this->isExternalLink;
    }
}
