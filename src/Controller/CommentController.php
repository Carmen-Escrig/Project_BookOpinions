<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Entity\Comment;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\Tag;

use App\Form\CommentFormType;

use App\Service\FuncCommon;

class CommentController extends AbstractController
{
    #[Route('/review/{slug}/comment/{commentSlug}/edit', name: 'comment_edit')]
    public function editReview(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, FuncCommon $funcCommon, $slug, $commentSlug): Response
    {
        $reviewRepository = $doctrine->getRepository(Review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());
        $commentRepository = $doctrine->getRepository(Comment::class);
        $comment = $commentRepository->findOneBy(["slug" => $commentSlug]);

        if($comment) {
            if ($user != $comment->getuser()) {
                throw new AccessDeniedException('You need to be the author of this comment to edit it.');
            }
    
            $form = $this->createForm(CommentFormType::class, $comment);
            $form->handleRequest($request);
    
            $entityManager = $doctrine->getManager();
    
            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();
                $comment->setContent($funcCommon->mention($comment->getContent()));
        
                $entityManager->persist($comment);
                try {
                    $entityManager->flush();
                    return $this->redirectToRoute('comment', ["slug" => $review->getSlug(), "commentSlug" => $commentSlug]);
                } catch (\Exception $e) {
                    return new Response("Error" . $e->getMessage());
                }
            }
    
            return $this->render('comment/form.html.twig', [
                'form' => $form->createView(),
                'title' => "Edit Comment"
            ]);
        } else {
            return $this->render('comment/comment.html.twig', [
                'comment' => null
            ]);
        }
    }

    #[Route('/review/{slug}/comment/{commentSlug}/delete', name: 'comment_delete')]
    public function deleteReview(ManagerRegistry $doctrine, Request $request, $slug, $commentSlug): Response
    {
        $reviewRepository = $doctrine->getRepository(Review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->find($this->getUser()->getId());
        $commentRepository = $doctrine->getRepository(Comment::class);
        $comment = $commentRepository->findOneBy(["slug" => $commentSlug]);

        if($comment) {
            if ($user != $comment->getUser()) {
                throw new AccessDeniedException('You need to be the author of this comment to delete it.');
            }
            $entityManager = $doctrine->getManager();
            try {
                $entityManager->remove($comment);
                $entityManager->flush();
                
                return $this->redirectToRoute('home', []);
            } catch (\Exception $e) {
                return new Response("Error deleting the comment. " . $e->getMessage());
            }
        } else {
            return $this->render('comment/comment.html.twig', [
                'comment' => null
            ]);
        }
    }

    #[Route('/review/{slug}/comment/{commentSlug}', name: 'comment')]
    public function review(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, FuncCommon $funcCommon, $slug, $commentSlug): Response
    {
        $reviewRepository = $doctrine->getRepository(Review::class);
        $review = $reviewRepository->findOneBy(["slug" => $slug]);
        $commentRepository = $doctrine->getRepository(Comment::class);
        $comment = $commentRepository->findOneBy(["slug" => $commentSlug]);

        if ($review) {
            $newComment = new Comment();
            $userRepository = $doctrine->getRepository(User::class);
            $user = $userRepository->find($this->getUser()->getId());

            $form = $this->createForm(CommentFormType::class, $newComment);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $newComment = $form->getData();
                $newComment->setContent($funcCommon->mention($newComment->getContent()));
                $newComment->setReview($review);
                $newComment->setUser($user);
                $newComment->setSlug($slugger->slug('new$newComment-' . uniqid()));
                $newComment->setReplyTo($comment);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($newComment);

                try {
                    $entityManager->flush();
                    return $this->redirectToRoute('comment', ["slug" => $review->getSlug(), "commentSlug" => $commentSlug]);
                } catch (\Exception $e) {
                    return new Response("Error" . $e->getMessage());
                }
            }
            return $this->render('comment/comment.html.twig', [
                'comment' => $comment,
                'commentForm' => $form->createView(),
            ]);
        }
        return $this->render('comment/comment.html.twig', [
            'comment' => null,
            'commentForm' => null,
        ]);  
    }

}
