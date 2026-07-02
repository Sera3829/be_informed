<?php

namespace App\Controller\Admin;

use App\Entity\PublicUser;
use App\Repository\PublicUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UserManager;

#[Route('/admin/visiteurs', name: 'admin_public_user_')]
class PublicUserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PublicUserRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/public_user/index.html.twig', [
            'public_users' => $repo->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(
        PublicUser $public_user,
        Request $request,
        EntityManagerInterface $em,
        UserManager $userManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(\App\Form\PublicUserFormType::class, $public_user, [
            'require_password' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $userManager->updatePassword($public_user, $plainPassword);
            } else {
                $em->flush();
            }

            $this->addFlash('success', 'Visiteur modifié avec succès.');
            return $this->redirectToRoute('admin_public_user_index');
        }

        return $this->render('admin/public_user/form.html.twig', [
            'form' => $form,
            'titre' => 'Modifier le visiteur',
            'user' => $public_user,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(PublicUser $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Visiteur supprimé.');
        }

        return $this->redirectToRoute('admin_public_user_index');
    }
}
