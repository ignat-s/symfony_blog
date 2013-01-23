<?php

namespace Acme\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Routing\Router;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Model\UserManager;
use Acme\BlogBundle\Model\User;
use Acme\BlogBundle\Form\Type\RegistrationType;
use Acme\BlogBundle\Form\Model\Registration;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Template("AcmeBlogBundle:User:login.html.twig")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        );
    }

    /**
     * @Route("/signup", name="signup")
     * @Template("AcmeBlogBundle:User:signup.html.twig")
     */
    public function signUpAction(Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');
        /** @var UserManager $userManager */
        $userManager = $this->get('acme_blog.user_manager');
        /** @var ObjectManager $objectManager */
        $objectManager = $this->get('acme_blog.object_manager');
        /** @var Router $router */
        $router = $this->get('router');

        $user = $userManager->createUser();
        $form = $this->createForm(
            new RegistrationType(),
            new Registration($user)
        );

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $userManager->updatePassword($user);

                $objectManager->persist($user);
                $objectManager->flush();

                $this->authenticateUser($user);

                $session->getFlashBag()->add('success', 'Welcome, you have successfully signed up!');
                return new RedirectResponse($router->generate('posts_index'));
            } else {
                $session->getFlashBag()->add('error', 'Sorry, sign up failed.');
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/login_check", name="_login_check")
     */
    public function loginCheckAction()
    {
        // @codeCoverageIgnoreStart
        // The security layer will intercept this request
        // @codeCoverageIgnoreEnd
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction()
    {
        // @codeCoverageIgnoreStart
        // The security layer will intercept this request
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param User $user
     */
    private function authenticateUser(User $user)
    {
        $providerKey = $this->container->getParameter('acme_blog.security.provider_key');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->get('security.context')->setToken($token);
    }
}
