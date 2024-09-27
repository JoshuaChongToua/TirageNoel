<?php

namespace App\Form;

use App\Entity\Partie;
use App\Entity\user;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('password', PasswordType::class, [
                'required' => false, // Rend le champ non obligatoire
            ])
            ->add('no_password', CheckboxType::class, [
                'label' => 'Aucun mot de passe',
                'required' => false, // La case à cocher est optionnelle
                'mapped' => false, // Ne lie pas directement à une propriété de l'entité
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partie::class,
        ]);
    }
}
