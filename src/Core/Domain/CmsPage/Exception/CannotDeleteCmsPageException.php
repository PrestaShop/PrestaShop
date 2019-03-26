<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception;

class CannotDeleteCmsPageException extends CmsPageException
{
    /**
     * @var int
     */
    private $cmsPageId;

    /**
     * @param int $cmsPageId
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct($cmsPageId, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->cmsPageId = $cmsPageId;
    }

    /**
     * @return int
     */
    public function getCmsPageId()
    {
        return $this->cmsPageId;
    }
}
