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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Adapter\SqlManager\SqlRequestFormDataValidator;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Creates or updates SqlRequest objects using form data.
 */
final class SqlRequestFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var SqlRequestFormDataValidator
     */
    private $requestSqlFormDataValidator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param CommandBusInterface $bus
     */
    public function __construct(
        CommandBusInterface $bus,
        SqlRequestFormDataValidator $requestSqlFormDataValidator,
        TranslatorInterface $translator
    ) {
        $this->commandBus = $bus;
        $this->requestSqlFormDataValidator = $requestSqlFormDataValidator;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $this->assertRequestIsValid($data);

        /** @var SqlRequestId $sqlRequestId */
        $sqlRequestId = $this->commandBus->handle(new AddSqlRequestCommand($data['name'], $data['sql']));

        return $sqlRequestId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $this->assertRequestIsValid($data);

        $sqlRequestId = new SqlRequestId($id);

        $command = (new EditSqlRequestCommand($sqlRequestId))
            ->setName($data['name'])
            ->setSql($data['sql']);

        $this->commandBus->handle($command);
    }

    private function assertRequestIsValid(array $data): void
    {
        $errors = $this->requestSqlFormDataValidator->validate($data);
        if (0 !== count($errors)) {
            $message = $this->translator->trans(
                $errors[0]['key'],
                $errors[0]['parameters'],
                $errors[0]['domain']
            );

            throw new SqlRequestConstraintException(
                $message,
                SqlRequestConstraintException::INVALID_SQL_QUERY
            );
        }
    }
}
