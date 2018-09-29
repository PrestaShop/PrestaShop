<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Adapter\Meta\MetaFormDataValidator;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\EditableMeta;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\CannotAddMetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\CannotEditMetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetMetaForEditing;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;

/**
 * Class MetaFormDataProvider is responsible for providing data for Shop parameters ->
 * Traffic & Seo -> Seo & Urls -> add/edit form.
 */
class MetaFormDataProvider
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var MetaFormDataValidator
     */
    private $metaFormDataValidator;

    /**
     * MetaFormDataProvider constructor.
     *
     * @param CommandBusInterface $commandBus
     * @param CommandBusInterface $queryBus
     * @param MetaFormDataValidator $metaFormDataValidator
     */
    public function __construct(
        CommandBusInterface $commandBus,
        CommandBusInterface $queryBus,
        MetaFormDataValidator $metaFormDataValidator
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->metaFormDataValidator = $metaFormDataValidator;
    }

    /**
     * Gets meta data for editing.
     *
     * @param int $metaId
     *
     * @return array
     */
    public function getData($metaId)
    {
        try {
            $metaId = new MetaId($metaId);
            /** @var EditableMeta $result */
            $result = $this->queryBus->handle(new GetMetaForEditing($metaId));

            return [
                'page_name' => $result->getPageName(),
                'page_title' => $result->getPageTitle(),
                'meta_description' => $result->getMetaDescription(),
                'meta_keywords' => $result->getMetaKeywords(),
                'url_rewrite' => $result->getUrlRewrite(),
            ];
        } catch (MetaException $exception) {
            return [];
        }
    }

    /**
     * Saves meta data. Returns error if such appeared during process.
     *
     * @param array $data
     *
     * @return array
     */
    public function saveData(array $data)
    {
        $errors = $this->metaFormDataValidator->validate($data);

        if (!empty($errors)) {
            return $errors;
        }

        try {
            $command = !empty($data['id']) ? $this->getEditMetaCommand($data) : $this->getSaveMetaCommand($data);
            $this->commandBus->handle($command);
        } catch (MetaException $exception) {
            $errors[] = $this->handleException($exception);
        }

        return $errors;
    }

    /**
     * Gets save meta command.
     *
     * @param array $data
     *
     * @return AddMetaCommand
     *
     * @throws MetaException
     */
    private function getSaveMetaCommand(array $data)
    {
        return new AddMetaCommand(
            $data['page_name'],
            $data['page_title'],
            $data['meta_description'],
            (array) $data['meta_keywords'], //todo: remove casting once multilang value is fixed.
            $data['url_rewrite']
        );
    }

    /**
     * Gets meta edit command.
     *
     * @param array $data
     *
     * @return EditMetaCommand
     *
     * @throws MetaException
     */
    private function getEditMetaCommand(array $data)
    {
        return (new EditMetaCommand(new MetaId($data['id']), $data['page_name'], $data['url_rewrite']))
            ->setPageTitle($data['page_title'])
            ->setMetaDescription($data['meta_description'])
            ->setMetaKeywords((array) $data['meta_keywords']) //todo: remove casting once multilang value is fixed.
        ;
    }

    /**
     * Transform exception into translatable errors.
     *
     * @param MetaException $exception
     *
     * @return array Errors
     */
    private function handleException(MetaException $exception)
    {
        $exceptionType = get_class($exception);

        if (MetaConstraintException::class === $exceptionType) {
            return $this->getConstraintErrorByCode($exception);
        }

        return $this->getErrorByExceptionType($exception);
    }

    /**
     * Get error for constraint exception.
     *
     * @param MetaConstraintException $exception
     *
     * @return array
     */
    private function getConstraintErrorByCode(MetaConstraintException $exception)
    {
        $code = $exception->getCode();

        if (MetaConstraintException::INVALID_PAGE_NAME === $code) {
            return [
                'key' => 'The %s field is invalid.',
                'parameters' => ['page name'],
                'domain' => 'Admin.Notifications.Error',
            ];
        }

        if (MetaConstraintException::INVALID_URL_REWRITE === $code) {
            return [
                'key' => 'The URL rewrite field must be filled in either the default or English language.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];
        }

        return [
            'key' => 'Invalid data supplied.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Gets errors by exception type.
     *
     * @param MetaException $exception
     *
     * @return array
     */
    private function getErrorByExceptionType(MetaException $exception)
    {
        $exceptionDictionary = [
            MetaNotFoundException::class => [
                'key' => 'The object cannot be loaded (or found)',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ],
            CannotAddMetaException::class => [
                'key' => 'An error occurred while creating an object.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ],
            CannotEditMetaException::class => [
                'key' => 'An error occurred while updating an object.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ],
        ];

        $exceptionType = get_class($exception);

        if (isset($exceptionDictionary[$exceptionType])) {
            return $exceptionDictionary[$exceptionType];
        }

        return [
            'key' => 'Unexpected error occurred.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }
}
