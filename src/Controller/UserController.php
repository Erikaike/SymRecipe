<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{

    /**
     * Edit User Profile
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        //l'annotation Security rend cette portion de code inutile
        //Verif que l'user est bien connecté, sinon redirection vers loginpage
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('security.login');
        // }
        //Verif que l'user qui fait l'action est bien l'user qui est connecté sinon redirection vers l'index des recettes
        // if ($this->getUser() !== $user) {
        //     return $this->redirectToRoute('recipe.index');
        // }

        //Sinon l'user peut acceder au formulaire :)  
        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Demande à l'user d'entrer son mdp pour confirmer que c'est bien lui => condition : le "plain password" entré correspond à celui de l'user
            if ($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'succes',
                    'Les infos de votre compte ont bien été modifiées'
                );
                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect '
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * 
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('utlisateur/edition-mot-de-pass/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(User $choosenUser, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {
                $choosenUser->setPassword(
                    $hasher->hashpassword(
                        $choosenUser,
                        $form->getData()['newPassword']
                    )
                );

                $manager->persist($choosenUser);
                $manager->flush();

                $this->addFlash(
                    'succes',
                    'le mdp a été modifié'
                );

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'succes',
                    'le mdp a été incorrect'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
