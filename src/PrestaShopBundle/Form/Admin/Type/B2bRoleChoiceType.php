<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\DefaultRole;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\Repository\B2bRoleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class B2bRoleChoiceType extends AbstractType
{
    public function __construct(
        private readonly LanguageContext $languageContext,
    ) {
    }

    public function getParent(): string
    {
        return EntityChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => B2bRole::class,
            'query_builder' => fn (B2bRoleRepository $repository) => $repository->createByLanguageIdQueryBuilder($this->languageContext->getId()),
            'choice_label' => fn (B2bRole $role) => $role->translate($this->languageContext->getId())?->getName()
                ?? self::humanizeRole($role->getRole()),
        ]);
    }

    private static function humanizeRole(string $role): string
    {
        if (str_starts_with($role, DefaultRole::PREFIX)) {
            $role = \substr($role, \strlen(DefaultRole::PREFIX));
        }

        return ucfirst(strtolower(str_replace('_', ' ', $role)));
    }
}
