<?php

namespace App\Form;

use App\Entity\PublicUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PublicUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => $options['require_password'],
                'attr' => ['class' => 'form-control'],
                'constraints' => $options['require_password'] ? [
                    new Length(['min' => 6, 'minMessage' => 'Minimum {{ limit }} caractères']),
                ] : [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicUser::class,
            'require_password' => false,
        ]);
    }
}
