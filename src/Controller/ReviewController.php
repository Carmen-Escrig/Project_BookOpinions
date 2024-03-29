<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Entity\Review;
use App\Entity\User;
use App\Entity\Tag;
use App\Entity\Comment;
use App\Entity\Like;

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
            $review->setNumLikes(0);
            $review->getBook()->addNumReviews();

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

    #[Route('/review/{slug}/like', name: 'review_like')]
    public function likeReview(ManagerRegistry $doctrine, $slug): JsonResponse
    {
        $reviewRepository = $doctrine->getRepository(review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());
        $likeRepository = $doctrine->getRepository(Like::class);
        $like = $likeRepository->findLikeByUserReview($user, $review);
    
        $entityManager = $doctrine->getManager();

        if($like) {
            try {
                $entityManager->remove($like);
                $review->subNumLikes();
                $entityManager->flush();
                $result = false;
                return new JsonResponse($result, Response::HTTP_OK);                     
            } catch (\Exception $e) {
                return new JsonResponse($result, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $like = new Like();
            $like->setUser($user);
            $like->setReview($review);

            $entityManager->persist($like);
            $review->addNumLikes();
            try {
                $entityManager->flush();
                $result = true;
                return new JsonResponse($result, Response::HTTP_OK);
            } catch (\Exception $e) {
                return new JsonResponse($result, Response::HTTP_INTERNAL_SERVER_ERROR);
            }   
        }
    }

    #[Route('/review/{slug}', name: 'review')]
    public function review(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, FuncCommon $funcCommon, $slug): Response
    {
        $reviewRepository = $doctrine->getRepository(Review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);

        if ($review) {
            if ($this->getUser()) {
                $userRepository = $doctrine->getRepository(User::class);
                $user = $userRepository->find($this->getUser()->getId());
                $likeRepository = $doctrine->getRepository(Like::class);
                $like = $likeRepository->findLikeByUserReview($user, $review);
                $liked = $like ? true : false;
            } else {
                $liked = false;
            }

            $comment = new Comment();
            $userRepository = $doctrine->getRepository(User::class);
            $user = $userRepository->find($this->getUser()->getId());

            $form = $this->createForm(CommentFormType::class, $comment);
            $form->handleRequest($request);

            //Controlar que el usuario esté registrado
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
                'liked' => $liked
            ]);
        }
        return $this->render('review/review.html.twig', [
            'review' => null,
            'commentForm' => null,
            'liked' => false
        ]);  
    }
}
