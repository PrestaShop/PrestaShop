<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Meta\CommandHandler;

use Meta;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\CommandHandler\AddMetaHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\CannotAddMetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SaveMetaHandler is responsible for saving meta data.
 *
 * @internal
 */
final class AddMetaHandler implements AddMetaHandlerInterface
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param ValidatorInterface $validator
     * @param int $defaultLanguageId
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ValidatorInterface $validator,
        $defaultLanguageId
    ) {
        $this->hookDispatcher = $hookDispatcher;
        $this->defaultLanguageId = $defaultLanguageId;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotAddMetaException
     * @throws MetaException
     */
    public function handle(AddMetaCommand $command)
    {
        $this->assertUrlRewriteHasDefaultLanguage($command);
        $this->assertIsUrlRewriteValid($command);

        try {
            $entity = new Meta();
            $entity->page = $command->getPageName()->getValue();
            $entity->title = $command->getLocalisedPageTitles();
            $entity->description = $command->getLocalisedMetaDescription();
            $entity->keywords = $command->getLocalisedMetaKeywords();

            $rewriteUrls = $command->getLocalisedRewriteUrls();
            foreach ($rewriteUrls as $idLang => $rewriteUrl) {
                if (!$rewriteUrl) {
                    $rewriteUrls[$idLang] = $rewriteUrls[$this->defaultLanguageId];
                }
            }

            $entity->url_rewrite = $rewriteUrls;
            $entity->add();

            if (0 >= $entity->id) {
                throw new CannotAddMetaException(
                    sprintf('Invalid entity id after creation: %s', $entity->id)
                );
            }
        } catch (PrestaShopException $exception) {
            throw new MetaException(
                'Failed to create meta entity',
                0,
                $exception
            );
        }

        $this->hookDispatcher->dispatchWithParameters('actionAdminMetaSave');

        return new MetaId((int) $entity->id);
    }

    /**
     * @param AddMetaCommand $command
     *
     * @throws MetaConstraintException
     */
    private function assertUrlRewriteHasDefaultLanguage(AddMetaCommand $command)
    {
        $urlRewriteErrors = $this->validator->validate(
            $command->getLocalisedRewriteUrls(),
            new DefaultLanguage()
        );

        if (0 !== count($urlRewriteErrors) && 'index' !== $command->getPageName()->getValue()) {
            throw new MetaConstraintException(
                'The url rewrite is missing for the default language when creating new meta record',
                MetaConstraintException::INVALID_URL_REWRITE
            );
        }
    }

    /**
     * @param AddMetaCommand $command
     *
     * @throws MetaConstraintException
     */
    private function assertIsUrlRewriteValid(AddMetaCommand $command)
    {
        foreach ($command->getLocalisedRewriteUrls() as $idLang => $rewriteUrl) {
            $errors = $this->validator->validate($rewriteUrl, new IsUrlRewrite());

            if (0 !== count($errors)) {
                throw new MetaConstraintException(
                    sprintf(
                        'Url rewrite %s for language with id %s is not valid',
                        $rewriteUrl,
                        $idLang
                    ),
                    MetaConstraintException::INVALID_URL_REWRITE
                );
            }
        }
    }
}
