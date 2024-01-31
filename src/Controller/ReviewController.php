<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Entity\Review;
use App\Entity\User;
use App\Entity\Tag;
use App\Entity\Comment;

use App\Form\ReviewFormType;
use App\Form\CommentFormType;

use App\Service\FuncCommon;

class ReviewController extends AbstractController
{
    #[Route('/profile/review/add', name: 'review_add')]
    public function addReview(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, FuncCommon $funcCommon): Response
    {
        $review = new Review();
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());

        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        $entityManager = $doctrine->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $review = $form->getData();
            $review->setCreator($user);
            $review->setSlug($slugger->slug($review->getTitle() . '-' . uniqid()));
            $review->setCreationDate(new \DateTime('@'.strtotime('now')));

            $tags = $funcCommon->findTags($review->getContent());
            
            if($tags) {
                foreach ($tags as $tagName) {
                    $tagRepository = $doctrine->getRepository(Tag::class);
                    $tag = $tagRepository->findOneBy(["name" => strtolower($tagName[0])]);
                    if ($tag) {
                        $review->addTag($tag);
                    } else {
                        $tag = new Tag();
                        $tag->setName(strtolower($tagName[0]));
                        $review->addTag($tag);
                        $entityManager->persist($tag);
                    }
                }
            }

            $review->setContent($funcCommon->mention($review->getContent()));
    
            $entityManager->persist($review);
            try {
                $entityManager->flush();
                return $this->redirectToRoute('review', ["slug" => $review->getSlug()]);
            } catch (\Exception $e) {
                return new Response("Error" . $e->getMessage());
            }
        }

        return $this->render('review/form.html.twig', [
            'form' => $form->createView(),
            'title' => "Create Review"
        ]);
    }

    #[Route('/profile/review/{slug}/edit', name: 'review_edit')]
    public function editReview(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, FuncCommon $funcCommon, $slug): Response
    {
        $reviewRepository = $doctrine->getRepository(Review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());

        if($review) {
            if ($user != $review->getCreator()) {
                throw new AccessDeniedException('You need to be the author of this review to edit it.');
            }
    
            $form = $this->createForm(ReviewFormType::class, $review);
            $form->handleRequest($request);
    
            $entityManager = $doctrine->getManager();
    
            if ($form->isSubmitted() && $form->isValid()) {
                $review = $form->getData();
                $review->setSlug($slugger->slug($review->getTitle() . '-' . uniqid()));
    
                foreach ($review->getTags() as $oldTag) {
                    $review->removeTag($oldTag);
                }
    
                $tags = $funcCommon->findTags($review->getContent());
                foreach ($tags as $tagName) {
                    $tagRepository = $doctrine->getRepository(Tag::class);
                    $tag = $tagRepository->findOneBy(["name" => strtolower($tagName[0])]);
                    if ($tag) {
                        $review->addTag($tag);
                    } else {
                        $tag = new Tag();
                        $tag->setName(strtolower($tagName[0]));
                        $review->addTag($tag);
                        $entityManager->persist($tag);
                    }
                }
    
                $review->setContent($funcCommon->mention($review->getContent()));
        
                $entityManager->persist($review);
                try {
                    $entityManager->flush();
                    return $this->redirectToRoute('review', ["slug" => $review->getSlug()]);
                } catch (\Exception $e) {
                    return new Response("Error" . $e->getMessage());
                }
            }
    
            return $this->render('review/form.html.twig', [
                'form' => $form->createView(),
                'title' => "Edit Review"
            ]);
        } else {
            return $this->render('review/review.html.twig', [
                'review' => null
            ]);
        }
    }

    #[Route('/profile/review/{slug}/delete', name: 'review_delete')]
    public function deleteReview(ManagerRegistry $doctrine, Request $request, $slug): Response
    {
        $reviewRepository = $doctrine->getRepository(Review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());

        if($review) {
            if ($user != $review->getCreator()) {
                throw new AccessDeniedException('You need to be the author of this review to edit it.');
            }
            $entityManager = $doctrine->getManager();
            try {
                $entityManager->remove($review);
                $entityManager->flush();
                
                return $this->redirectToRoute('home', []);
            } catch (\Exception $e) {
                return new Response("Error deleting the review. " . $e->getMessage());
            }
        } else {
            return $this->render('review/review.html.twig', [
                'review' => null
            ]);
        }
    }

    #[Route('/review/{slug}', name: 'review')]
    public function review(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, FuncCommon $funcCommon, $slug): Response
    {
        $reviewRepository = $doctrine->getRepository(Review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);

        if ($review) {
            $comment = new Comment();
            $userRepository = $doctrine->getRepository(User::class);
            $user = $userRepository->find($this->getUser()->getId());

            $form = $this->createForm(CommentFormType::class, $comment);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();
                $comment->setContent($funcCommon->mention($comment->getContent()));
                $comment->setReview($review);
                $comment->setUser($user);
                $comment->setSlug($slugger->slug('comment-' . uniqid()));

                $entityManager = $doctrine->getManager();
                $entityManager->persist($comment);

                try {
                    $entityManager->flush();
                    return $this->redirectToRoute("review", [
                        "slug" => $slug,
                    ]);
                } catch (\Exception $e) {
                    return new Response("Error" . $e->getMessage());
                }
            }
            return $this->render('review/review.html.twig', [
                'review' => $review,
                'commentForm' => $form->createView(),
            ]);
        }
        return $this->render('review/review.html.twig', [
            'review' => null,
            'commentForm' => null,
        ]);  
    }
}
