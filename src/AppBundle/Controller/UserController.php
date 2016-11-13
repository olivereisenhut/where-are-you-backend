<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;


/**
 * User controller.
 */
class UserController extends FOSRestController
{
    /**
     * Lists all user entities.
     *
     * @Rest\Get("user/", name="user_index")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->view($users, Response::HTTP_OK);
    }

    //TODO Implement google api client => https://github.com/google/google-api-php-client
    //TODO validate token with api client and save the state from api client call
    /**
     * Creates a new user entity.
     *
     * @Rest\Post("user/new", name="user_new")
     */
    public function newAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $prename = $data['Prename'];
        $name = $data['Name'];
        $mail = $data['Mail'];
        $user = new User();

        $user->setName($name);
        $user->setPrename($prename);
        $user->setEmail($mail);
        $user->setFriends(array());


        $em = $this->getDoctrine()->getManager();

        if ($mail) {
            $existing_user = $em->getRepository('AppBundle:User')->findBy(array('email' => $mail));
            if ($existing_user) {
                $user = $existing_user;
            }
        }
        else {
            $em->persist($user);
            $em->flush($user);
        }

        return $this->view($user,Response::HTTP_OK);
    }

    /**
     * Finds and displays a user entity.
     *
     * @Rest\Get("user/{id}", name="user_show")
     */
    public function showAction(User $user)
    {
        return $this->view($user,Response::HTTP_OK);
    }


    /**
     * Deletes a user entity.
     *
     * @Rest\Delete("user/{id}", name="user_delete")
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush($user);

        return $this->view($user,Response::HTTP_OK);
    }

}
