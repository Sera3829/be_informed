<?php

namespace App\Form;

use App\Entity\Conference;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ConferenceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Les classes CSS (form-control / form-select / form-check-input) sont
        // appliquées automatiquement par le thème bootstrap_5_layout.html.twig.
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre de la conférence'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4, 'placeholder' => 'Décrivez la conférence…'],
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'attr' => ['placeholder' => 'Ex. Ouagadougou, Salle A'],
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => true,
            ]);

        if (!$options['hide_owner']) {
            $builder->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $u) => $u->getPrenom() . ' ' . $u->getNom(),
                'label' => 'Conférencier',
                'placeholder' => '— Choisir un conférencier —',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conference::class,
            'hide_owner' => false,
        ]);
    }
}
