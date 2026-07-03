<?php

namespace App\Controller\Conferencier;

use App\Entity\Conference;
use App\Form\ConferenceFormType;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/conferencier/conferences', name: 'conferencier_conference_')]
class ConferenceController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ConferenceRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONFERENCIER');

        return $this->render('conferencier/conference/index.html.twig', [
            'conferences' => $repo->findBy(['owner' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONFERENCIER');

        $conference = new Conference();
        $conference->setOwner($this->getUser());

        $form = $this->createForm(ConferenceFormType::class, $conference, [
            'hide_owner' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conference->setCreatedAt(new \DateTimeImmutable());
            $em->persist($conference);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Conférence créée avec succès.']);
            }
            $this->addFlash('success', 'Conférence créée avec succès.');
            return $this->redirectToRoute('conferencier_conference_index');
        }

        return $this->renderForm($request, $form, 'Nouvelle conférence', 'fa-microphone-alt', $this->generateUrl('conferencier_conference_new'));
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Conference $conference, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('edit', $conference);

        $form = $this->createForm(ConferenceFormType::class, $conference, [
            'hide_owner' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'message' => 'Conférence modifiée avec succès.']);
            }
            $this->addFlash('success', 'Conférence modifiée avec succès.');
            return $this->redirectToRoute('conferencier_conference_index');
        }

        return $this->renderForm($request, $form, 'Modifier la conférence', 'fa-microphone-alt', $this->generateUrl('conferencier_conference_edit', ['id' => $conference->getId()]), ['conference' => $conference]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Conference $conference, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('delete', $conference);

        if ($this->isCsrfTokenValid('delete' . $conference->getId(), $request->request->get('_token'))) {
            $em->remove($conference);
            $em->flush();
            $this->addFlash('success', 'Conférence supprimée.');
        }

        return $this->redirectToRoute('conferencier_conference_index');
    }

    private function renderForm(Request $request, $form, string $titre, string $icon, string $action, array $extra = []): Response
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

        return $this->render('conferencier/conference/form.html.twig', $params);
    }
}
