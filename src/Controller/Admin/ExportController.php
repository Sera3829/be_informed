<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\Repository\PublicUserRepository;
use App\Repository\UserRepository;
use Knp\Snappy\Pdf;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/export', name: 'admin_export_')]
class ExportController extends AbstractController
{
    public function __construct(
        private ConferenceRepository $conferenceRepo,
        private UserRepository $userRepo,
        private PublicUserRepository $publicUserRepo,
        private CommentRepository $commentRepo,
    ) {
    }

    #[Route('/{type}.{format}', name: 'download', requirements: ['type' => 'conferences|users|visiteurs|commentaires', 'format' => 'xlsx|pdf'])]
    public function download(string $type, string $format, Pdf $snappy): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        [$titre, $headers, $rows] = $this->buildData($type);
        $filename = sprintf('%s_%s.%s', $type, date('Y-m-d'), $format);

        if ($format === 'xlsx') {
            return $this->xlsxResponse($headers, $rows, $filename);
        }

        return $this->pdfResponse($snappy, $titre, $headers, $rows, $filename);
    }

    /**
     * @return array{0: string, 1: string[], 2: array<int, array<int, string>>}
     */
    private function buildData(string $type): array
    {
        return match ($type) {
            'conferences' => [
                'Liste des conférences',
                ['Titre', 'Lieu', 'Date', 'Conférencier', 'Commentaires'],
                array_map(fn ($c) => [
                    $c->getTitre(),
                    $c->getLieu(),
                    $c->getDate()->format('d/m/Y H:i'),
                    $c->getOwner()->getPrenom() . ' ' . $c->getOwner()->getNom(),
                    (string) count($c->getComments()),
                ], $this->conferenceRepo->findBy([], ['date' => 'DESC'])),
            ],
            'users' => [
                'Liste des utilisateurs',
                ['Nom', 'Prénom', 'Email', 'Rôle', 'Conférences', 'Dernière connexion'],
                array_map(fn ($u) => [
                    $u->getNom(),
                    $u->getPrenom(),
                    $u->getEmail(),
                    in_array('ROLE_ADMIN', $u->getRoles(), true) ? 'Admin' : 'Conférencier',
                    (string) count($u->getConferences()),
                    $u->getLastLoginAt() ? $u->getLastLoginAt()->format('d/m/Y H:i') : 'Jamais',
                ], $this->userRepo->findAll()),
            ],
            'visiteurs' => [
                'Liste des visiteurs',
                ['Nom', 'Prénom', 'Téléphone', 'Commentaires', 'Inscrit le'],
                array_map(fn ($v) => [
                    $v->getNom(),
                    $v->getPrenom(),
                    $v->getPhone(),
                    (string) count($v->getComments()),
                    $v->getCreatedAt() ? $v->getCreatedAt()->format('d/m/Y') : '',
                ], $this->publicUserRepo->findAll()),
            ],
            'commentaires' => [
                'Liste des commentaires',
                ['Conférence', 'Auteur', 'Commentaire', 'Date'],
                array_map(fn ($c) => [
                    $c->getConference()->getTitre(),
                    $c->isAnonymous() ? 'Anonyme' : $c->getPublicUser()->getPrenom() . ' ' . $c->getPublicUser()->getNom(),
                    $c->getTexte(),
                    $c->getCreatedAt()->format('d/m/Y H:i'),
                ], $this->commentRepo->findBy([], ['createdAt' => 'DESC'])),
            ],
        };
    }

    /**
     * @param string[] $headers
     * @param array<int, array<int, string>> $rows
     */
    private function xlsxResponse(array $headers, array $rows, string $filename): Response
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'export_') . '.xlsx';

        $writer = new Writer();
        $writer->openToFile($tmpFile);
        $headerStyle = (new Style())->withFontBold(true);
        $writer->addRow(new Row(array_map(
            static fn (string $h) => Cell::fromValue($h, $headerStyle),
            $headers
        )));
        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }
        $writer->close();

        $response = new Response(file_get_contents($tmpFile));
        unlink($tmpFile);

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename));

        return $response;
    }

    /**
     * @param string[] $headers
     * @param array<int, array<int, string>> $rows
     */
    private function pdfResponse(Pdf $snappy, string $titre, array $headers, array $rows, string $filename): Response
    {
        $html = $this->renderView('admin/export/pdf.html.twig', [
            'titre' => $titre,
            'headers' => $headers,
            'rows' => $rows,
        ]);

        $response = new Response($snappy->getOutputFromHtml($html));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename));

        return $response;
    }
}
