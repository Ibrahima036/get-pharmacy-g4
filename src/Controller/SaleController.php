<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Sale;
use App\Entity\SaleDetails;
use App\Form\SaleForm;
use App\Form\SearchProductForm;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use App\Service\PdfGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('admin/sale')]
final class SaleController extends AbstractController
{
    #[Route(name: 'app_sale_index', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request  $request): Response
    {
        $qb = $productRepository->createQueryBuilder('p')->orderBy('p.id', 'DESC');

        $formSale = $this->createForm(SaleForm::class, null, []);


        $form = $this->createForm(SearchProductForm::class, null, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchItem = $form->get('search')->getData();
            $qb->andWhere('p.name LIKE :searchItem')
                ->setParameter('searchItem', '%' . $searchItem . '%');
        }

        $pagination = $paginator->paginate(
            $qb->getQuery()->getResult(),
            $request->query->getInt('page', 1),
            10);

        return $this->render('sale/index.html.twig', [
            'products' => $pagination,
            'form' => $form->createView(),
            'formSale' => $formSale->createView(),
        ]);
    }

    #[Route(path: '/list',name: 'app_sale_list', methods: ['GET'])]
    public function saleList(SaleRepository $saleRepository, PaginatorInterface $paginator, Request  $request): Response
    {

        $pagination = $paginator->paginate(
            $saleRepository->findBy([], ['id' => 'DESC']),
            $request->query->getInt('page', 1),
            10);

        return $this->render('sale/list_sale.html.twig', [
            'sales' => $pagination,
        ]);
    }

   #[Route('/new', name: 'app_sale_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, #[CurrentUser] $currentUser,  UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data['id']){
            $this->addFlash('error', 'Veuillez selectionner un client');
            return new JsonResponse(['error' => 'Veuillez selectionner un client'], 400);
        }

        $client = $entityManager->getRepository(Client::class)->findOneBy(['id' => $data['id']]);

        if (!$client) {
            $this->addFlash('error', 'Client non trouvé');
            return new JsonResponse(['error' => 'Client non trouvé'], 400);
        }

        $sale = new Sale();
        $total = 0;

        foreach ($data['items'] as $item) {
            $product = $productRepository->find($item['id']);
            if (!$product || $product->getStock()->getQuantity() < $item['quantity']) {
                return new JsonResponse(['error' => 'Produit indisponible'], 400);
            }

            $saleDetail = new SaleDetails();
            $saleDetail->setProduct($product);
            $saleDetail->setQuantity($item['quantity']);
            $saleDetail->setUnitPrice($product->getUnitPrice());
            $saleDetail->setSale($sale);
            $sale->addSaleDetail($saleDetail);

            $product->getStock()->setQuantity($product->getStock()->getQuantity() - $item['quantity']);
            $total += $product->getUnitPrice() * $item['quantity'];
        }

        $sale->setTotal($total)->setUser($currentUser)->setClient($client);
        $entityManager->persist($sale);
        $entityManager->flush();

        $this->addFlash('success', 'Vente effectuée avec success');

        // Générer l'URL du ticket à retourner
        $ticketUrl = $urlGenerator->generate('app_sale_ticket', [
            'id' => $sale->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse([
            'success' => true,
            'ticketUrl' => $ticketUrl
        ]);
    }


    #[Route('/{id}', name: 'app_sale_show', methods: ['GET'])]
    public function show(Sale $sale): Response
    {
        return $this->render('sale/show.html.twig', [
            'sale' => $sale,
        ]);
    }


    #[Route('/ventes/{id}/ticket', name: 'app_sale_ticket')]
    public function ticket(Sale $sale, PdfGeneratorService $pdf): Response
    {
        return $pdf->generateTicket('sale/ticket.html.twig', [
            'sale' => $sale,
        ], 'ticket_vente_' . $sale->getId() . '.pdf');
    }


    #[Route('/ventes/{id}/facture', name: 'app_sale_invoice')]
    public function generateInvoice(Sale $sale): Response
    {
        // Options PDF
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);

        // Rendu HTML
        $html = $this->renderView('sale/pdf.html.twig', [
            'sale' => $sale,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="facture_vente_' . $sale->getId() . '.pdf"'
            ]
        );
    }

}
