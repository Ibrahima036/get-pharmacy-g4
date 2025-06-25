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
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
            'carts' => $this->getCart($request, $productRepository),
            'total' => $this->getTotal($request, $productRepository),
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

   #[Route('/validate-cart', name: 'app_sale_new', methods: ['GET', 'POST'])]
    public function validateCart(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, #[CurrentUser] $currentUser,  UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $sale = new Sale();
        $total = 0;

        $data = json_decode($request->getContent(), true);
        $carts = $request->getSession()->get('cart');

        if (count($carts) === 0) {
            return new JsonResponse(['error' => 'Veuillez ajouter au moins un produit.'], 400);
        }

        if ($data['id']){
            $client = $entityManager->getRepository(Client::class)->findOneBy(['id' => $data['id']]);

            if (!$client) {
                $this->addFlash('error', 'Client non trouvé');
                return new JsonResponse(['error' => 'Client non trouvé'], 400);
            }

            $sale->setClient($client);
        }


        foreach ($carts  as $id => $quantity) {
            $product = $productRepository->find($id);
            if (!$product || $product->getStock()->getQuantity() < $quantity) {
                return new JsonResponse(['error' => 'Produit indisponible'], 400);
            }

            $saleDetail = new SaleDetails();
            $saleDetail->setProduct($product);
            $saleDetail->setQuantity($quantity);
            $saleDetail->setUnitPrice($product->getUnitPrice());
            $saleDetail->setSale($sale);
            $sale->addSaleDetail($saleDetail);

            $product->getStock()->setQuantity($product->getStock()->getQuantity() - $quantity);
            $total += $product->getUnitPrice() * $quantity;
        }

        $sale->setTotal($total)->setUser($currentUser);
        $entityManager->persist($sale);
        $entityManager->flush();

        $this->addFlash('success', 'Vente effectuée avec success');

        $request->getSession()->remove('cart');

        // Générer l'URL du ticket à retourner
        $ticketUrl = $urlGenerator->generate('app_sale_ticket', [
            'id' => $sale->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse([
            'success' => true,
            'ticketUrl' => $ticketUrl
        ]);
    }

    #[Route('/{id}', name: 'app_sale_show', requirements: ['id' => '\d+'], methods: ['GET'])]
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/ventes/{id}/facture', name: 'app_sale_invoice')]
    public function generateInvoice(Sale $sale, PdfGeneratorService $pdf): Response
    {
        return $pdf->generateInvoice('sale/invoice.html.twig', ['sale' => $sale], 'invoice_vente_' . $sale->getId() . '.pdf');
    }
    #[Route('/add/{id}', name: 'app_cart_add',  methods: ['POST'])]
    public function add(int $id, Request $request, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            return new JsonResponse([
                'error' => 'Produit non trouvé',
            ]);
        }

        $cart = $request->getSession()->get('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + 1;

        if ($cart[$id] > $product->getStock()->getQuantity()) {

            return new JsonResponse([
                'error' => 'Stock disponible',
            ]);
        }

        $request->getSession()->set('cart', $cart);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    #[Route('/remove/{id}', name: 'app_cart_remove',  methods: ['POST', 'GET'])]
    public function remove(int $id, Request $request): Response
    {
        $cart = $request->getSession()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $request->getSession()->set('cart', $cart);
        $this->addFlash('success', 'Produit supprimé avec success');
        return $this->redirectToRoute('app_sale_index');
    }

    #[Route('/decrement/{id}', name: 'app_cart_decrement',  methods: ['POST', 'GET'])]
    public function decrement(int $id, Request $request): Response
    {
        $cart = $request->getSession()->get('cart', []);
        if (!isset($cart[$id])) {
             $this->addFlash('error', 'Produit non trouvé');
            return $this->redirectToRoute('app_sale_index');
        }

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        $request->getSession()->set('cart', $cart);
        $this->addFlash('success', 'Produit decrementé avec success');
        return $this->redirectToRoute('app_sale_index');
    }

    #[Route('/remove-cart', name: 'app_clear_session',  methods: ['POST', 'GET'])]
    public function clear(Request $request): Response
    {
        if (count($request->getSession()->get('cart', [])) === 0) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('app_sale_index');
        }

        $request->getSession()->remove('cart');

        $this->addFlash('success', 'Panier  vidé avec success');
        return $this->redirectToRoute('app_sale_index');
    }

    private function getCart(Request $request, ProductRepository $productRepository): array
    {
        $cart = $request->getSession()->get('cart', []);
        $data = [];

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $data[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product->getUnitPrice() * $quantity,
                ];
            }
        }

        return $data;
    }

    private function getTotal(Request $request, ProductRepository $productRepository): float
    {
        $total = 0;
        foreach ($this->getCart($request, $productRepository) as $item) {
            $total += $item['product']->getUnitPrice() * $item['quantity'];
        }

        return $total;
    }

}
