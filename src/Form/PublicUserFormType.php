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
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['placeholder' => 'Ex. +226 70 00 00 00'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => $options['require_password'],
                'help' => $options['require_password'] ? null : 'Laisser vide pour conserver le mot de passe actuel.',
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
