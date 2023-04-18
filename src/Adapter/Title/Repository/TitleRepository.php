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

namespace PrestaShop\PrestaShop\Adapter\Title\Repository;

use Gender;
use PrestaShop\PrestaShop\Adapter\Title\Validate\TitleValidator;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\CannotAddTitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\CannotDeleteTitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\CannotUpdateTitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Provides access to Title data source
 */
class TitleRepository extends AbstractObjectModelRepository
{
    /**
     * @var TitleValidator
     */
    private $titleValidator;

    /**
     * @param TitleValidator $titleValidator
     */
    public function __construct(
        TitleValidator $titleValidator
    ) {
        $this->titleValidator = $titleValidator;
    }

    /**
     * @param TitleId $titleId
     *
     * @return Gender
     *
     * @throws CoreException
     * @throws TitleNotFoundException
     */
    public function get(TitleId $titleId): Gender
    {
        /** @var Gender $title */
        $title = $this->getObjectModel(
            $titleId->getValue(),
            Gender::class,
            TitleNotFoundException::class
        );

        return $title;
    }

    /**
     * @param Gender $title
     * @param int $errorCode
     *
     * @return TitleId
     */
    public function add(Gender $title, int $errorCode = 0): TitleId
    {
        $this->titleValidator->validate($title);
        $id = $this->addObjectModel(
            $title,
            CannotAddTitleException::class,
            $errorCode
        );

        return new TitleId($id);
    }

    /**
     * @param Gender $title
     * @param array $propertiesToUpdate
     * @param int $errorCode
     */
    public function partialUpdate(
        Gender $title,
        array $propertiesToUpdate,
        int $errorCode
    ): void {
        $this->partiallyUpdateObjectModel(
            $title,
            $propertiesToUpdate,
            CannotUpdateTitleException::class,
            $errorCode
        );
    }

    /**
     * @param Gender $title
     *
     * @throws CoreException
     * @throws TitleNotFoundException
     */
    public function delete(Gender $title): void
    {
        $this->deleteObjectModel(
            $title,
            CannotDeleteTitleException::class
        );
    }
}
