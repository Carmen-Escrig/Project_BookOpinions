<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/profile/{slug}/follow', name: 'user_follow')]
    public function userFollow(ManagerRegistry $doctrine, $slug): JsonResponse
    {
        $userRepository = $doctrine->getRepository(User::class);
        $profile = $repository->findOneBy(["slug" => $slug]);
        $user = $userRepository->find($this->getUser()->getId());

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
    }

    #[Route('/user/{slug}', name: 'user_profile')]
    public function userProfile(): Response
    {
        $userRepository = $doctrine->getRepository(User::class);
        $user = $userRepository->findOneBy(["slug" => $slug]);

        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }
}
