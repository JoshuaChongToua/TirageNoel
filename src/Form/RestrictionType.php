<?php

namespace App\Form;

use App\Entity\Partie;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestrictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('joueur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',  // Le nom d'utilisateur s'affichera dans la sélection
                'label' => 'Utilisateur',
                'query_builder' => function (UserRepository $userRepository) use ($options) {
                    return $userRepository->createQueryBuilder('u')
                        ->join('u.partieRejoints', 'pr')
                        ->where('pr.partie = :partie')
                        ->setParameter('partie', $options['partie'])
                        ->orderBy('u.username', 'ASC');
                },
            ])

            // Sélection de l'utilisateur interdit
            ->add('interdit', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Utilisateur interdit',
                'query_builder' => function (UserRepository $userRepository) use ($options) {
                    return $userRepository->createQueryBuilder('u')
                        ->join('u.partieRejoints', 'pr')
                        ->where('pr.partie = :partie')
                        ->setParameter('partie', $options['partie'])
                        ->orderBy('u.username', 'ASC');
                },
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Ajouter Restriction',
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'partie' => null,
        ]);
    }
}
