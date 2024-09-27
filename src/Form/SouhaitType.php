<?php

namespace App\Form;

use App\Entity\Partie;
use App\Entity\PartieRejoint;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SouhaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('souhaits', TextareaType::class, [
                'label' => 'Souhaits',
                
                'attr' => [
                    'rows' => 3,  // Ajuste la taille du textarea
                    'placeholder' => 'Entrez vos souhaits ici, un par ligne ou séparés par des virgules.'
                ],
                'required' => false, // Si les souhaits ne sont pas obligatoires
            ])
        
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
        ;

        $builder->get('souhaits')
        ->addModelTransformer(new CallbackTransformer(
            // Transforme l'array en string pour l'affichage dans le formulaire
            function ($souhaitsAsArray) {
                return is_array($souhaitsAsArray) ? implode(", ", $souhaitsAsArray) : '';
            },
            // Transforme la string en array pour la sauvegarde dans la BDD
            function ($souhaitsAsString) {
                return array_map('trim', explode(',', $souhaitsAsString));
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PartieRejoint::class,
        ]);
    }
}
