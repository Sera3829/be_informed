<?php

namespace App\Controller\Admin;

use App\Entity\PublicUser;
use App\Repository\PublicUserRepository;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function edit(PublicUser $public_user, Request $request, EntityManagerInterface $em, UserManager $userManager): Response
    {
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

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Visiteur modifié avec succès.']);
            }
            $this->addFlash('success', 'Visiteur modifié avec succès.');
            return $this->redirectToRoute('admin_public_user_index');
        }

        $params = [
            'form' => $form,
            'titre' => 'Modifier le visiteur',
            'icon' => 'fa-user-edit',
            'form_action' => $this->generateUrl('admin_public_user_edit', ['id' => $public_user->getId()]),
            'user' => $public_user,
        ];

        if ($request->isXmlHttpRequest()) {
            $response = $this->render('partials/_ajax_form.html.twig', $params);
            if ($form->isSubmitted()) {
                $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return $response;
        }

        return $this->render('admin/public_user/form.html.twig', $params);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(PublicUser $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            try {
                $em->remove($user);
                $em->flush();
                $this->addFlash('success', 'Visiteur supprimé avec succès.');
            } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException) {
                $this->addFlash('danger', 'Impossible de supprimer ce visiteur : des données y sont encore rattachées.');
            }
        }

        return $this->redirectToRoute('admin_public_user_index');
    }
}
