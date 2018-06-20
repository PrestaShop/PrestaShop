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

namespace PrestaShop\PrestaShop\Core\Grid\Action;
use PrestaShop\PrestaShop\Core\Grid\Exception\InvalidActionDataException;

/**
 * Class PanelAction is responsible for holding single grid action data
 */
final class PanelAction implements PanelActionInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $icon;

    /**
     * @param string $id   Unique action identifier
     * @param string $name Translated action name
     * @param string $icon Action icon
     * @param string $type Type of grid action
     */
    public function __construct($id, $name, $icon, $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
        $this->type = $type;
    }

    /**
     * Create grid action from array data
     *
     * @param array $data
     *
     * @return PanelAction
     *
     * @throws InvalidActionDataException
     */
    public static function fromArray(array $data)
    {
        if (false === isset($data['id'], $data['name'], $data['icon'], $data['type'])) {
            throw new InvalidActionDataException(
                'Invalid action data given. Check that action data has required attributes: "id", "name", "type" "icon".'
            );
        }

        return new self(
            $data['id'],
            $data['name'],
            $data['icon'],
            $data['type']
        );
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
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }
}
