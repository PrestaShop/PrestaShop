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

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface BulkActionInterface defines contract for single grid bulk action
 */
interface BulkActionInterface
{
    /**
     * Get unique bulk action identifier for grid
     *
     * @return string
     */
    public function getId();

    /**
     * Get translated bulk action name
     *
     * @return string
     */
    public function getName();

    /**
     * Set trasnlated bulk action name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Get bulk action type
     *
     * @return string
     */
    public function getType();

    /**
     * Set options for bulk action
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Configure options for bulk action
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Get bulk action options
     *
     * @return array
     */
    public function getOptions();
}
