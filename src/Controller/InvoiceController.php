<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\NewInvoiceType;
use App\Repository\BookRepository;
use App\Repository\GameRepository;
use App\Repository\InvoiceRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/Invoice')]
class InvoiceController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/new', name: 'new_invoice')]
    public function new(InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager, Request $request, BookRepository $bookRepository): Response
    {
        // just set up a fresh $task object (remove the example data)
        $invoice = new Invoice();

        if (isset($_POST['addInvoice'])) {

            $filename = $_POST['fileName'];
            $invoice_date = new \DateTime($_POST['invoiceDate']);
            $invoice->setFileName($filename);
            $invoice->setInvoiceDate($invoice_date);

            $now = new \DateTime();
            $now_last = new \DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $invoice->setAddDate($now);

            $first_day = $now->setISODate((int)$now->format('o'), (int)$now->format('W'), 1);
            $first_day->setTime('00','00','00');

            $last_day = $now_last->setISODate((int)$now_last->format('o'), (int)$now_last->format('W'), 1)->modify("+ 6 day");
            $last_day->setTime(23, 59, 59);
            $check_invoice = $invoiceRepository->Verification($first_day, $last_day);


            if (count($check_invoice) === 0) {
                $books = $bookRepository->findBy(['isCheckBilling' => true]);
                foreach ($books as $book) {
                    $invoice->addBook($book);
                    $book->setIsBilled(true);
                    $book->setModificationdate($now);
                    $book->setBillingDate($invoice_date);
                    $book->setIsCheckBilling(null);
                    $book->setIsCheckActivation(null);
                    $entityManager->persist($book);
                }
                $entityManager->persist($invoice);

                try {
                    $entityManager->flush();
                } catch (\Exception $exception) {

                } finally {
                    if (isset($exception)) {
                        $this->addFlash('warning', 'Erreur cela n\'a pas fonctionné');
                    } else {
                        $this->addFlash('success', 'Mise à jour des données effectué');
                    }
                }
                return $this->redirectToRoute('billing', [

                ]);

            }else{
                $this->addFlash('warning', 'Facturation déjà effectué');
            }



        }
        return $this->redirectToRoute('billing', [

        ]);
    }
}
