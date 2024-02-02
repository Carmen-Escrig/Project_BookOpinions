<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Entity\User;
use App\Entity\Review;

class UserController extends AbstractController
{

    // Ruta para seguir o dejar de seguir a otro usuario
    #[Route('/profile/{slug}/follow', name: 'user_follow')]
    public function followUser(ManagerRegistry $doctrine, $slug): JsonResponse
    {
        $userRepository = $doctrine->getRepository(User::class);
        $profile = $repository->findOneBy(["slug" => $slug]);
        $user = $userRepository->find($this->getUser()->getId());

        if($profile != $user) {
            $entityManager = $doctrine->getManager();

            if(in_array($user, ($profile->getFollowers()->toArray()))) {
                try {
                    $profile->removeFollower($user);
                    $user->removeFollowing($profile);

                    $entityManager->flush();
                    $result = false;
                    return new JsonResponse($result, Response::HTTP_OK);           
                } catch (\Exception $e) {
                    return new JsonResponse($result, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $profile->addFollower($user);

                try {
                    $entityManager->flush();

                    $result = true;
                    return new JsonResponse($result, Response::HTTP_OK);
                } catch (\Exception $e) {
                    return new JsonResponse($result, Response::HTTP_INTERNAL_SERVER_ERROR);
                }  
            }
        } else {
            throw new AccessDeniedException('You cannot follow yourself.');
        }
    }

    //Ruta para eliminar un usuario
    #[Route('/profile/{slug}/delete', name: 'user_delete')]
    public function deleteUser(ManagerRegistry $doctrine, $slug): Response
    {
        $userRepository = $doctrine->getRepository(User::class);
        $profile = $repository->findOneBy(["slug" => $slug]);
        $user = $userRepository->find($this->getUser()->getId());
        $entityManager = $doctrine->getManager();
        
        if ($user) {
            if($user == $profile) {
                try { 
                    $entityManager->remove($user);
                    $entityManager->flush();
    
                    return $this->redirectToRoute('index', []);
                } catch (\Exception $e) {
                    return new Response("Error " . $e->getMessage());
                }
            } else {
                throw new AccessDeniedException('You cannot delete this profile.');
            }
        } else {
            return $this->render('profile/profile.html.twig', [
                "user" => $user
            ]);
        }
    }

    //Ruta para ver el perfil del usuario
    #[Route('/user/{slug}', name: 'user_profile')]
    public function userProfile(): Response
    {
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(["slug" => $slug]);
        $reviews = $user->getReviews();

        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'reviews' => $reviews
        ]);
    }
}
