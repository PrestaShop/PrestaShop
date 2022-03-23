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

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CityName is responsible for providing valid city name value.
 */
class CityName extends ValueObject
{
    /**
     * @var string
     */
    private $cityName;

    /**
     * @throws DomainConstraintException
     */
    public function __construct(string $cityName)
    {
        parent::__construct();

        $this->validate($cityName, [
            new NotBlank(),
            new TypedRegex(TypedRegex::TYPE_CITY_NAME),
        ], "$cityName is not a valid city name");

        $this->cityName = $cityName;
    }

    public function getValue(): string
    {
        return $this->cityName;
    }
}
