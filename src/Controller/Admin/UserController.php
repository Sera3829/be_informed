<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UserManager;

#[Route('/admin/users', name: 'admin_user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/user/index.html.twig', [
            'users' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(
        Request $request,
        UserManager $userManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(\App\Form\AdminUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->createUser($user, $form->get('plainPassword')->getData(), $form->get('roles')->getData());

            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form,
            'titre' => 'Nouvel utilisateur',
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
        UserManager $userManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(\App\Form\AdminUserFormType::class, $user, [
            'require_password' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $userManager->updatePassword($user, $plainPassword);
            } else {
                $em->flush();
            }

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier l\'utilisateur',
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
