<?php

namespace Acme\BlogBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Acme\BlogBundle\Form\Type\PostType;
use Acme\BlogBundle\Repository\PostRepositoryInterface;
use Acme\BlogBundle\Document\Post;
use Acme\BlogBundle\Model\PostManager;

class PostController extends Controller
{
    /**
     * @Route("/posts/create", name="post_create")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function createAction(Request $request)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = $this->get('acme_blog.object_manager');
        /** @var PostManager $postManager */
        $postManager = $this->get('acme_blog.post_manager');
        /** @var Router $router */
        $router = $this->get('router');

        $post = new Post();
        $form = $this->createForm(
            new PostType(),
            $post
        );

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $postManager->updatePermalink($post);

                $objectManager->persist($post);
                $objectManager->flush();

                return new RedirectResponse(
                    $router->generate('post_show', array('permalink' => $post->getPermalink()))
                );
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/posts/{permalink}", name="post_show")
     * @Template()
     */
    public function showAction($permalink)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = $this->get('acme_blog.object_manager');
        /** @var PostRepositoryInterface $repository */
        $repository = $objectManager->getRepository('AcmeBlogBundle:Post');
        $post = $repository->findOneByPermalink($permalink);

        if (!$post) {
            throw new NotFoundHttpException('Post not found.');
        }

        return array(
            'post' => $post
        );
    }
}
