<?php

namespace Acme\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\BlogBundle\Document\Post;

class PostController extends Controller
{
    /**
     * @Route("/posts/create")
     * @Template()
     */
    public function createAction()
    {
        $post = new Post();
        $post->setBody('A Foo Bar');

        /** @var \Doctrine\Bundle\MongoDBBundle\ManagerRegistry $dm */
        $objectManager = $this->getManager();
        $objectManager->persist($post);
        $objectManager->flush();

        return array('post' => $post);
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function getManager()
    {
        /** @var \Doctrine\Bundle\MongoDBBundle\ManagerRegistry $dm  */
        $dm = $this->get('doctrine_mongodb');
        return $dm->getManager();
    }
}
