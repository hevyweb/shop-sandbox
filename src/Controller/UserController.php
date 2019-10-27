<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * List of all users
     *
     * @Route("/users", name="users")
     */
    public function indexAction(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'title' => 'Users'
        ]);
    }

    /**
     * Page to edit user
     *
     * @Route("/users/{id}", name="user-edit", requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request): Response
    {
        $userId = $request->get('id');
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /**
         * @var User|null $user
         */
        $user = $userRepository->find($userId);
        if (empty($user)) {
            throw new NotFoundHttpException('User with id "' . $userId . '" not found.');
        }

        $userEditForm = $this->createForm(EditUserType::class, $user, [
            'action' => $this->generateUrl('user-edit', ['id' => $user->getId()])
        ]);
        $userEditForm->handleRequest($request);
        if ($userEditForm->isSubmitted() && $userEditForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('user/edit.html.twig', [
                'title' => 'Update user data',
                'form' => $userEditForm->createView(),
                'submit' => 'Save'
            ]
        );
    }

    /**
     * Register page
     *
     * @Route("/register", name="user-registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/register.html.twig', [
                'title' => 'Registration',
                'form' => $form->createView(),
                'submit' => 'Register'
            ]
        );
    }

    /**
     * User login page
     *
     * @Route("/login", name="user-login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('user-edit', ['id' => $this->getUser()->getId()]);
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'title' => 'Login!',
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Log out page
     *
     * @Route("/logout", name="app_logout")
     * @return Response
     */
    public function logout(): Response
    {
        return $this->redirectToRoute('home');
    }
}
