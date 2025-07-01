<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
final class ExportFileController extends AbstractController
{


    #[Route('/export/sales/excel', name: 'export_sales_excel')]
    public function exportSalesExcel(ProductRepository $productRepository): Response
    {
        $date = new \DateTime();
        $products = $productRepository->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Entêtes
        $sheet->fromArray(['ID', 'date', 'nom_produit', 'prix_unitaire', 'quantite', 'categorie', 'fournisseur'], null, 'A1');

        // Données
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->getId());
            $sheet->setCellValue('B' . $row, $product->getCreatedAt()->format('Y-m-d'));
            $sheet->setCellValue('C' . $row, $product->getName());
            $sheet->setCellValue('D' . $row, $product->getUnitPrice());
            $sheet->setCellValue('E' . $row, $product->getStock()->getQuantity());
            $sheet->setCellValue('F' . $row, $product->getCategory()->getName());
            $sheet->setCellValue('G' . $row, $product->getSupplier()->getPhone());
            $row++;
        }

        // Génération du fichier
        $writer = new Xlsx($spreadsheet);

        // Stockage temporaire
        $temp_file = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($temp_file);

        return $this->file(
            $temp_file,
            'liste_produits_' .date('Y-m-d_H-i') . '.xlsx',
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );

    }

}
