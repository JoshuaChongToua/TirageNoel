<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Voiture;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('couleur', TextType::class, [
            ])
            ->add('carburant')
            ->add('nom')
            ->add('annee',NumberType::class, [
                'constraints'=> new Length(min:4,max:4),
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie'
            ])
            ->add('image', FileType::class, array('data_class' => null))
            ->add('save', SubmitType::class,[
                'label'=> 'Envoyer'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoVoiture(...))
        ;
    }

    public function autoVoiture(PreSubmitEvent $event) : void
    {
        $data = $event->getData();
        if(empty($data['couleur'])) {
            $slugger = new AsciiSlugger();
            $data['couleur'] = strtolower($slugger->slug($data['couleur']));
            $event->setData($data);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
            
        ]);
    }
}
