<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminUserFormType extends AbstractType
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
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Administrateur' => 'ROLE_ADMIN',
                    'Conférencier' => 'ROLE_CONFERENCIER',
                ],
                'multiple' => true,
                'expanded' => true,
                'mapped' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => $options['require_password'],
                'attr' => ['class' => 'form-control'],
                'constraints' => $options['require_password'] ? [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length(['min' => 6, 'minMessage' => 'Minimum {{ limit }} caractères']),
                ] : [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'require_password' => true,
        ]);
    }
}
