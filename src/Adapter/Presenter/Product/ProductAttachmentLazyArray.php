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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use Tools;

class ProductAttachmentLazyArray extends AbstractLazyArray
{
    /**
     * @var array
     */
    private $attachment;

    /**
     * ProductAttachmentLazyArray constructor.
     * @param array $attachment
     * @throws \ReflectionException
     */
    public function __construct($attachment)
    {
        $this->attachment = $attachment;
        parent::__construct();
        $this->appendArray($attachment);
    }

    /**
     * @arrayAccess
     * @return null|string
     */
    public function getFileSizeFormatted()
    {
        return Tools::formatBytes($this->attachment['file_size'], 2);
    }
}