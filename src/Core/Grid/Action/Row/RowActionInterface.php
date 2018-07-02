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

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface RowActionInterface defines contract for grid's row action
 */
interface RowActionInterface
{
    /**
     * Get unique row identifier for grid row's action
     *
     * @return string
     */
    public function getId();

    /**
     * Get translated row action name
     *
     * @return string
     */
    public function getName();

    /**
     * Set row action name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Get row action type
     *
     * @return string
     */
    public function getType();

    /**
     * Get row action related options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set row action options
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Configure row action options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);
}
