<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use App\Repository\SupplierRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        ProductRepository $productRepository,
        ClientRepository $clientRepository,
        SaleRepository $saleRepository,
        SupplierRepository $supplierRepository,
        UserRepository $userRepository): Response
    {
        $products = $productRepository->findAll();
        $customers = $clientRepository->findAll();
        $sales = $saleRepository->findby([], orderBy: ['id' => 'DESC']);
        $suppliers = $supplierRepository->findAll();
        $users =  $userRepository->findAll();


        return $this->render('dashboard/index.html.twig', [
            'products' => $products,
            'customers' => $customers,
            'sales' => $sales,
            'suppliers' => $suppliers,
            'users' => $users
        ]);
    }
}
