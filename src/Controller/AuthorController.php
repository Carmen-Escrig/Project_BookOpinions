<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Review;

use App\Form\AuthorFormType;

class AuthorController extends AbstractController
{
    #[Route('/author/add', name: 'author_add')]
    public function addAuthor(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $author = new Author();

        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        $entityManager = $doctrine->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $author = $form->getData();
            $author->setSlug($slugger->slug($author->getName() . '-' . uniqid()));
    
            $entityManager->persist($author);
            try {
                $entityManager->flush();
                return $this->redirectToRoute('author', ["slug" => $author->getSlug()]);
            } catch (\Exception $e) {
                return new Response("Error" . $e->getMessage());
            }
        }

        return $this->render('author/form.html.twig', [
            'form' => $form->createView(),
            'title' => "Create Author"
        ]);
    }

    #[Route('/admin/author/{slug}/edit', name: 'author_edit')]
    public function editAuthor(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, $slug): Response
    {
        $authorRepository = $doctrine->getRepository(Author::class);
        $author = $authorRepository->findOneBy(["slug" => $slug]);

        if($author) {
            $form = $this->createForm(AuthorFormType::class, $author);
            $form->handleRequest($request);
    
            $entityManager = $doctrine->getManager();
    
            if ($form->isSubmitted() && $form->isValid()) {
                $author = $form->getData();
                $author->setSlug($slugger->slug($author->getName() . '-' . uniqid()));
        
                $entityManager->persist($author);
                try {
                    $entityManager->flush();
                    return $this->redirectToRoute('author', ["slug" => $author->getSlug()]);
                } catch (\Exception $e) {
                    return new Response("Error" . $e->getMessage());
                }
            }
    
            return $this->render('author/form.html.twig', [
                'form' => $form->createView(),
                'title' => "Edit Author"
            ]);
        } else  {
            return $this->render('author/author.html.twig', [
                'author' => null
            ]);
        }
    }

    #[Route('/admin/author/{slug}/delete', name: 'author_delete')]
    public function deleteAuthor(ManagerRegistry $doctrine, Request $request, $slug): Response
    {
        $authorRepository = $doctrine->getRepository(Author::class);
        $author = $authorRepository->findOneBy(["slug" => $slug]);

        if($author) {
            $entityManager = $doctrine->getManager();
            try {
                $entityManager->remove($author);
                $entityManager->flush();
                
                return $this->redirectToRoute('home', []);
            } catch (\Exception $e) {
                return new Response("Error deleting the author. " . $e->getMessage());
            }
        } else {
            return $this->render('author/author.html.twig', [
                'author' => null
            ]);
        }
    }

    #[Route('/author/{slug}', name: 'author')]
    public function author(ManagerRegistry $doctrine, $slug): Response
    {
        $authorRepository = $doctrine->getRepository(Author::class);
        $author = $authorRepository->findOneBy(["slug" => $slug]);

        return $this->render('author/author.html.twig', [
            'author' => $author,
        ]);
    }
}
