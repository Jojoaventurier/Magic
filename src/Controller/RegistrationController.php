<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); // création d'un objet User
        $form = $this->createForm(RegistrationFormType::class, $user); // création du formulaire d'inscription associé à l'utilisateur
        $form->handleRequest($request); // gestion de la soumission du formulaire
        $currentDateTime = new \DateTime('now'); // obtention de la date et l'heure actuelle
    
        if ($form->isSubmitted() && $form->isValid()) {
            // encode le mot de passe en clair
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData() // récupération du mot de passe en clair
                )
            );
            $user->setCreationDate($currentDateTime); // enregistre la date de création de l'utilisateur
    
            $entityManager->persist($user); // prépare l'enregistrement de l'utilisateur
            $entityManager->flush(); // exécute l'insertion dans la base de données

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('admin@magic-hub.com', 'Admin from MagicHub'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre adresse email pour finaliser votre inscription')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        //TODO pop up de bienvenue lors de la premiere connection
        $this->addFlash('success', 'Merci d\'avoir rejoint la communauté Magic-Hub et bienvenue :)');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/{user}/profile/delete', name: 'app_delete_user')]
    public function deleteProfile(EntityManagerInterface $entityManager, Request $request, TokenStorageInterface $tokenStorage)
    {
       $user = $this->getUser();
       $entityManager->remove($user);
       $request->getSession()->invalidate();
       
        // Clear the security token to avoid the refresh user error
        $tokenStorage->setToken(null);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
        
    }
    
}
