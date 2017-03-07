<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 28/02/17
 * Time: 20:47
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\postType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class postController extends Controller
{
    /**
     * @Route("/", name="app_post_posts")
     */
    public function postAction()
    {
        $m = $this->getDoctrine()->getManager();
        $postRepo = $m->getRepository('AppBundle:Post');
        // $posts = $postRepo->findAll() ---> Does lazy loading and it produces extra queries from the templates
        $posts = $postRepo->findAllPosts();
        return $this->render(':Post:posts.html.twig', [
            'post' => $posts,
        ]);
    }

    /**
     * @Route("/add-post", name="app_post_addPost")
     */
    public function addPostAction()
    {
        $a = new Post();
        $form = $this->createForm(new postType(), $a, ['action' => $this->generateUrl('app_post_doAdd')]);
        return $this->render(':Post:AddPost.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}