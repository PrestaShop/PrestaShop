<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Type\B2b;

use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\B2bRole\Role;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\Repository\B2bRoleRepository;
use PrestaShopBundle\Form\DataTransformer\IdToEntityTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class B2bRoleChoiceType extends AbstractType
{
    public function __construct(
        private readonly LanguageContext $languageContext,
    ) {
    }

    public function getParent(): string
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'class' => B2bRole::class,
                'input' => 'entity',
                'query_builder' => fn (B2bRoleRepository $repository) => $repository->createByLanguageIdQueryBuilder($this->languageContext->getId()),
                'choice_label' => fn (B2bRole $role) => $role->translate($this->languageContext->getId())?->getName()
                    ?? self::humanizeRole($role->getRole()),
            ])
            ->setAllowedValues('input', ['entity', 'id'])
            ->setNormalizer('input', static function (Options $options, string $value) {
                if ('id' !== $value || !$options['multiple']) {
                    return $value;
                }

                throw new InvalidOptionsException('The "id" input is not supported for "multiple" choice.');
            });
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ('id' !== $options['input']) {
            return;
        }

        $builder->addModelTransformer(new IdToEntityTransformer($options['em'], B2bRole::class));
    }

    private static function humanizeRole(string $role): string
    {
        if (str_starts_with($role, Role::PREFIX)) {
            $role = \substr($role, \strlen(Role::PREFIX));
        }

        return ucfirst(strtolower(str_replace('_', ' ', $role)));
    }
}
