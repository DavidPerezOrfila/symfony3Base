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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class postController extends Controller
{

    /**
     * @Route("/post", name="app_post_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAction()
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Post');

        $posts = $repo->findAll();

        return $this->render(':index:index.html.twig',
            [
                'posts' => $posts,
            ]
        );
    }

    /**
     * @Route("/insert", name="app_post_insert")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function insertAction()
    {
        $post = new Post();
        $post->setAuthor($this->getUser());
        $form = $this->createForm(PostType::class, $post);
        return $this->render(':Post:AddPost.html.twig',
            [
                'form' => $form->createView(),
                'action' => $this->generateUrl('app_post_doInsert')
            ]

        );
    }

    /**
     * @Route("/doInsert", name="app_post_doInsert")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */
    public function doInsert(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            $post = new Post();
            $form = $this->createForm(postType::class, $post);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();
                $post->setAuthor($user);
                $m = $this->getDoctrine()->getManager();

                $m->persist($post);
                $m->flush();
                $this->addFlash('messages', 'Post añadido');
                return $this->redirectToRoute('app_index_index');
            }
            $this->addFlash('messages', 'Revisa los datos introducidos');
            return $this->render(':Post:AddPost.html.twig',
                [
                    'form' => $form->createView(),
                    'action' => $this->generateUrl('app_post_doInsert')
                ]

            );
        }
        return;
    }


    /**
     * @Route(path="/updatePost/{id}", name="app_post_update")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Post');

        $post = $repository->find($id);
        $form = $this->createForm(postType::class, $post);

        return $this->render(':Post:updPost.html.twig', [
            'form' => $form->createView(),
            'action' => $this->generateUrl('app_post_doUpdate', ['id' => $id])
        ]);
    }

    /**
     * @Route(path="/doUpdateProd/{id}", name="app_post_doUpdate")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function doUpdateAction($id, Request $request)
    {
        $m = $this->getDoctrine()->getManager();
        $repository = $m->getRepository('AppBundle:Post');

        $post = $repository->find($id);
        $form = $this->createForm(postType::class, $post);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $m->flush();
            $this->addFlash('messages', 'Post Actualizado');
            return $this->redirectToRoute('app_index_index');
        }
        $this->addFlash('messages', 'Revisa tu formulario');
        return $this->render(':Post:updPost.html.twig',
            [
                'form' => $form->createView(),
                'action' => $this->generateUrl('app_post_doUpdate', ['id' => $id]),
            ]
        );
    }

    /**
     * @Route(
     *     path="/remove/{id}",
     *     name="app_post_remove"
     * )
     * @ParamConverter(name="post", class="AppBundle:Post")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Post $post)
    {
        $m = $this->getDoctrine()->getManager();
        $m->remove($post);
        $m->flush();
        $this->addFlash('messages', 'Post borrado');
        return $this->redirectToRoute('app_post_index');
    }



}