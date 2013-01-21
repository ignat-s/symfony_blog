<?php

namespace Acme\BlogBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Acme\BlogBundle\Form\Type\CommentType;
use Acme\BlogBundle\Form\Type\PostType;
use Acme\BlogBundle\Document\User;
use Acme\BlogBundle\Document\Post;
use Acme\BlogBundle\Document\Comment;
use Acme\BlogBundle\Model\PostManager;
use Acme\BlogBundle\Repository\PostRepositoryInterface;

class PostController extends Controller
{
    /**
     * @Route("/", name="posts_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        /** @var ObjectManager $objectManager */
        $objectManager = $this->get('acme_blog.object_manager');
        /** @var PostRepositoryInterface $repository */
        $repository = $objectManager->getRepository('AcmeBlogBundle:Post');

        $pageNumber = $request->query->get('page', 1);
        $limitPerPage = 10;

        $pagination = $paginator->paginate($repository->createOrderedPostsQuery(), $pageNumber, $limitPerPage);

        return compact('pagination');
    }

    /**
     * @Route("/tag/{tag}", name="posts_by_tag")
     * @Template()
     */
    public function postsByTagAction(Request $request, $tag)
    {
        /** @var \Knp\Component\Pager\Paginator $paginator */
        $paginator = $this->get('knp_paginator');
        /** @var ObjectManager $objectManager */
        $objectManager = $this->get('acme_blog.object_manager');
        /** @var PostRepositoryInterface $repository */
        $repository = $objectManager->getRepository('AcmeBlogBundle:Post');

        $pageNumber = $request->query->get('page', 1);
        $limitPerPage = 10;

        $pagination = $paginator->paginate(
            $repository->createOrderedPostsWithTagQuery($tag),
            $pageNumber,
            $limitPerPage
        );

        return compact('pagination', 'tag');
    }

    /**
     * @Route("/posts/create", name="post_create")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function createAction(Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');
        /** @var SecurityContextInterface $securityContext */
        $securityContext = $this->get('security.context');
        /** @var ObjectManager $objectManager */
        $objectManager = $this->get('acme_blog.object_manager');
        /** @var PostManager $postManager */
        $postManager = $this->get('acme_blog.post_manager');
        /** @var Router $router */
        $router = $this->get('router');

        $post = new Post();
        $post->setAuthor($securityContext->getToken()->getUser());
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

                $session->getFlashBag()->add('success', 'New post created.');
                return new RedirectResponse(
                    $router->generate('post_show', array('permalink' => $post->getPermalink()))
                );
            } else {
                $session->getFlashBag()->add('error', 'Errors occurred while creating new post.');
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/posts/{permalink}", name="post_show")
     * @Method("GET")
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
            'post' => $post,
            'comment_form' => $this->createCommentForm()->createView()
        );
    }

    /**
     * @Route("/posts/{permalink}/add/comment", name="post_add_comment")
     * @Method("POST")
     * @Template()
     */
    public function addCommentAction(Request $request, $permalink)
    {
        /** @var Session $session */
        $session = $this->get('session');
        /** @var Router $router */
        $router = $this->get('router');
        /** @var ObjectManager $objectManager */
        $objectManager = $this->get('acme_blog.object_manager');
        /** @var PostRepositoryInterface $repository */
        $repository = $objectManager->getRepository('AcmeBlogBundle:Post');
        $post = $repository->findOneByPermalink($permalink);

        if (!$post) {
            throw new NotFoundHttpException('Post not found.');
        }

        $form = $this->createCommentForm();
        $form->bind($request);

        if ($form->isValid()) {
            $post->addComment($form->getData());
            $objectManager->persist($post);
            $objectManager->flush();

            $session->getFlashBag()->add('success', 'Your comment was added.');
        } else {
            $session->getFlashBag()->add('error', 'Sorry, unable to add your comment.');
        }

        return new RedirectResponse(
            $router->generate('post_show', array('permalink' => $post->getPermalink()))
        );
    }

    private function createCommentForm()
    {
        /** @var SecurityContextInterface $securityContext */
        $securityContext = $this->get('security.context');
        $comment = new Comment();
        if ($securityContext->isGranted(User::ROLE_USER)) {
            $comment->setAuthor($securityContext->getToken()->getUser()->getUsername());
            $comment->setEmail($securityContext->getToken()->getUser()->getEmail());
        }
        return $this->createForm(
            new CommentType(),
            $comment
        );
    }
}
