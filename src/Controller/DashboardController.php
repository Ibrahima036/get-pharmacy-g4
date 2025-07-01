<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\SaleDetailsRepository;
use App\Repository\SaleRepository;
use App\Repository\SupplierRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


final class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard_admin')]
    public function AdminDashboard(
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
        $stats = $saleRepository->getAdminStats();



        return $this->render('dashboard/_partial/admin.html.twig', [
            'products' => $products,
            'customers' => $customers,
            'sales' => $sales,
            'suppliers' => $suppliers,
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    #[Route('/pharmacist/dashboard', name: 'app_dashboard_pharmacist')]
    public function PharmacistDashboard(
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


        return $this->render('dashboard/_partial/_pharmacist.html.twig', [
            'products' => $products,
            'customers' => $customers,
            'sales' => $sales,
            'suppliers' => $suppliers,
            'users' => $users
        ]);
    }
    #[Route('/seller/dashboard', name: 'app_dashboard_seller')]
    public function SellerDashboard(
        SaleRepository $saleRepo,
        SaleDetailsRepository $saleDetailRepo,
        #[CurrentUser]  $currentUser
    ): Response {
        $salesCount = $saleRepo->countSalesBySeller($currentUser);
        $totalRevenue = $saleRepo->getTotalRevenueBySeller($currentUser);
        $topProducts = $saleDetailRepo->getTopProductsBySeller($currentUser);


        return $this->render('dashboard/_partial/_seller.html.twig', [
            'salesCount' => $salesCount,
            'totalRevenue' => $totalRevenue,
            'topProducts' => $topProducts,
            'sales' => $saleRepo->findBy(['user' => $currentUser], orderBy: ['id' => 'DESC']),
        ]);
    }

}
