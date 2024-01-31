<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Tag;

class TagController extends AbstractController
{
    #[Route('/tag/{tagName}', name: 'tag')]
    public function tag(ManagerRegistry $doctrine, $tagName): Response
    {
        $tagRepository = $doctrine->getRepository(Tag::class);
        $tag = $tagRepository->findOneBy(["name" => $tagName]);
        
        return $this->render('tag/tag.html.twig', [
            'tag' => $tag,
        ]);
    }

    #[Route('/admin/tag/{tagName}/delete', name: 'tag_delete')]
    public function deleteTag(ManagerRegistry $doctrine, $tagName): Response
    {
        $tagRepository = $doctrine->getRepository(Tag::class);
        $tag = $tagRepository->findOneBy(["name" => $tagName]);
        
        if($tag) {
            $entityManager = $doctrine->getManager();
            try {
                $entityManager->remove($tag);
                $entityManager->flush();
                
                return $this->redirectToRoute('home', []);
            } catch (\Exception $e) {
                return new Response("Error deleting the tag. " . $e->getMessage());
            }
        } else {
            return $this->render('tag/tag.html.twig', [
                'tag' => null
            ]);
        }
    }
}
