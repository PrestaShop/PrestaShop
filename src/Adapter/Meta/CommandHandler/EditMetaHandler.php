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

namespace PrestaShop\PrestaShop\Adapter\Meta\CommandHandler;

use Meta;
use PrestaShop\PrestaShop\Adapter\Meta\MetaDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\CommandHandler\EditMetaHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\CannotEditMetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class EditMetaHandler is responsible for editing meta data.
 *
 * @internal
 */
#[AsCommandHandler]
final class EditMetaHandler implements EditMetaHandlerInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var MetaDataProvider
     */
    private $metaDataProvider;

    /**
     * @param ValidatorInterface $validator
     * @param MetaDataProvider $metaDataProvider
     */
    public function __construct(
        ValidatorInterface $validator,
        MetaDataProvider $metaDataProvider
    ) {
        $this->validator = $validator;
        $this->metaDataProvider = $metaDataProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws MetaException
     */
    public function handle(EditMetaCommand $command)
    {
        try {
            $entity = new Meta($command->getMetaId()->getValue());

            if (0 >= $entity->id) {
                throw new MetaNotFoundException(sprintf('Meta with id "%s" was not found for edit', $command->getMetaId()->getValue()));
            }

            if (null !== $command->getPageName()) {
                $this->assertIsValidPageName($entity->page, $command);
                $entity->page = $command->getPageName()->getValue();
            }

            if (null !== $command->getLocalisedRewriteUrls()) {
                $entity->url_rewrite = $command->getLocalisedRewriteUrls();
            }

            if (null !== $command->getLocalisedPageTitles()) {
                $entity->title = $command->getLocalisedPageTitles();
            }

            if (null !== $command->getLocalisedMetaDescriptions()) {
                $entity->description = $command->getLocalisedMetaDescriptions();
            }

            if (null !== $command->getLocalisedMetaKeywords()) {
                $entity->keywords = $command->getLocalisedMetaKeywords();
            }

            $this->assertUrlRewriteHasDefaultLanguage($entity);
            $this->assertIsUrlRewriteValid($entity);

            if (false === $entity->update()) {
                throw new CannotEditMetaException(sprintf('Error occurred when updating Meta with id "%s"', $command->getMetaId()->getValue()));
            }
        } catch (PrestaShopException $exception) {
            throw new CannotEditMetaException(sprintf('Error occurred when updating Meta with id "%s"', $command->getMetaId()->getValue()), 0, $exception);
        }
    }

    /**
     * @param Meta $entity
     *
     * @throws MetaConstraintException
     */
    private function assertUrlRewriteHasDefaultLanguage(Meta $entity)
    {
        $urlRewriteErrors = $this->validator->validate(
            $entity->url_rewrite,
            new DefaultLanguage()
        );

        if ('index' !== $entity->page && 0 !== count($urlRewriteErrors)) {
            throw new MetaConstraintException('The url rewrite is missing for the default language when editing meta record', MetaConstraintException::INVALID_URL_REWRITE);
        }
    }

    /**
     * @param Meta $entity
     *
     * @throws MetaConstraintException
     */
    private function assertIsUrlRewriteValid(Meta $entity)
    {
        foreach ($entity->url_rewrite as $idLang => $rewriteUrl) {
            $errors = $this->validator->validate($rewriteUrl, new IsUrlRewrite());

            if (0 !== count($errors)) {
                throw new MetaConstraintException(sprintf('Url rewrite %s for language with id %s is not valid', $rewriteUrl, $idLang), MetaConstraintException::INVALID_URL_REWRITE);
            }
        }
    }

    /**
     * @param string $alreadyExistingPage
     * @param EditMetaCommand $command
     *
     * @throws MetaConstraintException
     */
    private function assertIsValidPageName($alreadyExistingPage, EditMetaCommand $command)
    {
        if ($command->getPageName()->getValue() === $alreadyExistingPage) {
            return;
        }

        $availablePages = $this->metaDataProvider->getAvailablePages();

        if (!in_array($command->getPageName()->getValue(), $availablePages, true)) {
            throw new MetaConstraintException(sprintf('Given page name %s is not available. Available values are %s', $command->getPageName()->getValue(), var_export($availablePages, true)), MetaConstraintException::INVALID_PAGE_NAME);
        }
    }
}
