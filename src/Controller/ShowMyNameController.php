<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ShowMyNameController extends AbstractController
{
    //route towards showing name, homepage and should stay there, a form should be available to edit.


    #[Route('/show_my_name', name: 'show_my_name')]
    public function show(Request $request): Response
    {
        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session = $request->getSession();
        if ($session->has('name')) {
            $name = $session->get('name');
        }
        else {
            $name = 'unknown';
            $session->set('name', $name);
        }

        //creating a user object
        $user = new User();
        //setting user as the session getter in case of no session, unknown
        $user->setUser($session->get('name'));
        // creating the form with the usertype
        $form = $this->createForm(UserType::class, $user);

        //The recommended way of processing forms is to use a single action for both rendering the form and handling the form submit.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();

            // ... perform some action, saving the name of the user to the session

            //endpoint while submitting form
            return $this->redirectToRoute('homepage');
        }
        echo $session->get("name");
        //endpoint on load & after submitting form
        return $this->renderForm('show_my_name/index.html.twig', [
            'name' => $name,
            'form' => $form,
        ]);
    }

    #[Route('/change', name: 'change_my_name')]
    public function change(Request $request): Response
    {
        $session = $request->getSession();
        //creating a user object
        $user = new User();
        //setting user as the session getter in case of no session, unknown
        $user->setUser($session->get('name'));
        // creating the form with the usertype
        $form = $this->createForm(UserType::class, $user);

        //The recommended way of processing forms is to use a single action for both rendering the form and handling the form submit.
        $form->handleRequest($request);
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
        $user = $form->getData();

            // ... perform some action, saving the name of the user to the session

            //endpoint while submitting form

        $session->set('name', $user->getUser());

        //actual endpoint after submitting. -> throwing it back to the homepage.
        return $this->redirectToRoute('homepage');
    }

    public function homepage(): RedirectResponse
    {
        // redirects to the "homepage" route
        return $this->redirectToRoute('homepage');
    }

    #[Route('/about-becode', name: 'about-me')]
    public function aboutMe(Request $request): Response
    {
        $session = $request->getSession();
//
        if ($session->get('name') === null) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('about_me/index.html.twig', [
            'name' => $session->get('name'),
        ]);
    }
}
