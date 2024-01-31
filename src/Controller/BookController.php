<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Review;

use App\Form\BookFormType;

class BookController extends AbstractController
{
    #[Route('/book/add', name: 'book_add')]
    public function addBook(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $book = new Book();

        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        $entityManager = $doctrine->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $form->get('cover')->getData();
            if ($cover) { 
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cover->guessExtension();
                try {
                    $cover->move(
                        $this->getParameter('covers_directory'), $newFilename);
                } catch (FileException $e) {
                    new Response("Error uploading the file. " . $e->getMessage());
                }
                $book->setCover($newFilename);
            }
            $book = $form->getData();
            $book->setSlug($slugger->slug($book->getTitle() . '-' . uniqid()));
    
            $entityManager->persist($book);
            try {
                $entityManager->flush();
                return $this->redirectToRoute('book', ["slug" => $book->getSlug()]);
            } catch (\Exception $e) {
                return new Response("Error" . $e->getMessage());
            }
        }

        return $this->render('book/form.html.twig', [
            'form' => $form->createView(),
            'title' => "Create Book"
        ]);
    }

    #[Route('/admin/book/{slug}/edit', name: 'book_edit')]
    public function editBook(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, $slug): Response
    {
        $bookRepository = $doctrine->getRepository(Book::class);
        $book = $bookRepository->findOneBy(["slug" => $slug]);

        if($book) {
            $form = $this->createForm(BookFormType::class, $book);
            $form->handleRequest($request);
    
            $entityManager = $doctrine->getManager();
    
            if ($form->isSubmitted() && $form->isValid()) {
                $cover = $form->get('cover')->getData();
                if ($cover) { 
                    $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$cover->guessExtension();
                    try {
                        $cover->move(
                            $this->getParameter('covers_directory'), $newFilename);
                    } catch (FileException $e) {
                        new Response("Error uploading the file. " . $e->getMessage());
                    }
                    $book->setCover($newFilename);
                }
                $book = $form->getData();
                $book->setSlug($slugger->slug($book->getTitle() . '-' . uniqid()));
        
                $entityManager->persist($book);
                try {
                    $entityManager->flush();
                    return $this->redirectToRoute('book', ["slug" => $book->getSlug()]);
                } catch (\Exception $e) {
                    return new Response("Error" . $e->getMessage());
                }
            }
    
            return $this->render('book/form.html.twig', [
                'form' => $form->createView(),
                'title' => "Edit Book"
            ]);
        } else {
            return $this->render('book/book.html.twig', [
                'book' => null
            ]);
        }
    }

    #[Route('/admin/book/{slug}/delete', name: 'book_delete')]
    public function deleteBook(ManagerRegistry $doctrine, Request $request, $slug): Response
    {
        $bookRepository = $doctrine->getRepository(Book::class);
        $book = $bookRepository->findOneBy(["slug" => $slug]);

        if($book) {
            $entityManager = $doctrine->getManager();
            try {
                $entityManager->remove($book);
                $entityManager->flush();
                
                return $this->redirectToRoute('home', []);
            } catch (\Exception $e) {
                return new Response("Error deleting the book. " . $e->getMessage());
            }
        } else {
            return $this->render('book/book.html.twig', [
                'book' => null
            ]);
        }
    }

    #[Route('/book/{slug}', name: 'book')]
    public function book(ManagerRegistry $doctrine, $slug): Response
    {
        $bookRepository = $doctrine->getRepository(Book::class);
        $book = $bookRepository->findOneBy(["slug" => $slug]);

        return $this->render('book/book.html.twig', [
            'book' => $book,
        ]);
    }
}
