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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class postController extends Controller
{


    /**
     * @Route("/new", name="app_post_new")
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction(Request $request)
    {
        $a = new Post();
        $form = $this->createForm(postType::class, $a);

        return $this->render(':Post:AddPost.html.twig', [
            'form' => $form->createView(),
            'title' => 'New Post',
        ]);
    }

    /**
     * @Route("/post", name="app_post_posts")
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


}