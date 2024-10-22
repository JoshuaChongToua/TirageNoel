<?php

namespace App\Form;

use App\Entity\Choix;
use App\Entity\user;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('personneChoisie', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username', // Utilisez le champ approprié pour afficher le nom de l'utilisateur
                'query_builder' => function (UserRepository $userRepository) use ($options) {
                    // Récupérez uniquement les utilisateurs qui participent à la partie
                    return $userRepository->createQueryBuilder('u')
                        ->join('u.partieRejoints', 'pr') 
                        ->where('pr.partie = :partie')
                        ->setParameter('partie', $options['partie'])
                        ->orderBy('u.username', 'ASC');
                },
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Choix::class,
            'partie' => null,
        ]);
    }
}
