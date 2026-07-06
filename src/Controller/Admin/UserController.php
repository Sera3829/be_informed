<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function new(Request $request, UserManager $userManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(\App\Form\AdminUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->createUser($user, $form->get('plainPassword')->getData(), $form->get('roles')->getData());

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Utilisateur créé avec succès.']);
            }
            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->renderUserForm($request, $form, 'Nouvel utilisateur', 'fa-user-plus', $this->generateUrl('admin_user_new'));
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserManager $userManager): Response
    {
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

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Utilisateur modifié avec succès.']);
            }
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->renderUserForm($request, $form, "Modifier l'utilisateur", 'fa-user-edit', $this->generateUrl('admin_user_edit', ['id' => $user->getId()]), ['user' => $user]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            if ($user === $this->getUser()) {
                $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
                return $this->redirectToRoute('admin_user_index');
            }

            try {
                $em->remove($user);
                $em->flush();
                $this->addFlash('success', 'Utilisateur supprimé avec succès.');
            } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException) {
                $this->addFlash('danger', 'Impossible de supprimer cet utilisateur : des données y sont encore rattachées.');
            }
        }

        return $this->redirectToRoute('admin_user_index');
    }

    private function renderUserForm(Request $request, $form, string $titre, string $icon, string $action, array $extra = []): Response
    {
        $params = array_merge($extra, [
            'form' => $form,
            'titre' => $titre,
            'icon' => $icon,
            'form_action' => $action,
        ]);

        if ($request->isXmlHttpRequest()) {
            $response = $this->render('partials/_ajax_form.html.twig', $params);
            if ($form->isSubmitted()) {
                $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return $response;
        }

        return $this->render('admin/user/form.html.twig', $params);
    }
}
