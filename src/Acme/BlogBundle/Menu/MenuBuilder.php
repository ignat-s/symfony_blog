<?php
namespace Acme\BlogBundle\Menu;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Menu\FactoryInterface;
use Mopa\Bundle\BootstrapBundle\Navbar\AbstractNavbarMenuBuilder;

class MenuBuilder extends AbstractNavbarMenuBuilder
{
    private $securityContext;

    public function __construct(FactoryInterface $factory, SecurityContextInterface $securityContext)
    {
        parent::__construct($factory);
        $this->securityContext = $securityContext;
    }

    public function createMainMenu(Request $request)
    {
        $menu = $this->createNavbarMenuItem();

        $menu->addChild('Posts', array('route' => 'posts_index'));
        if ($this->securityContext->isGranted('ROLE_USER')) {
            $menu->addChild('Create post', array('route' => 'post_create'));
            $menu->addChild('Logout', array('route' => '_logout'));
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
            $menu->addChild('Sign up', array('route' => 'signup'));
            $menu->addChild('Sign in', array('route' => 'login'));
        }

        return $menu;
    }
}