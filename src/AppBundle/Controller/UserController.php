<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    /**
     * Creates a new user entity.
     *
     * @Rest\Post("user/new", name="user_new")
     */
    public function newAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $this->validateModel($data);

        $prename = $data['Prename'];
        $name = $data['Name'];
        $mail = $data['Mail'];
        $google_id_token = $data['TokenId'];
        $image_url = $data['ImageUrl'];
        $user = new User();

        $user->setName($name);
        $user->setPrename($prename);
        $user->setEmail($mail);
        $user->setGoogleIdToken($google_id_token);
        $user->setImageUrl($image_url);
        $user->setFriends(array());


        $em = $this->getDoctrine()->getManager();

        if ($mail) {
            $existing_user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $mail));
            if ($existing_user) {
                //if ($existing_user->getGoogleIdToken() == $this->isValidGoogleToken($existing_user->getGoogleIdToken())) {
                    $user = $existing_user;
                //}
                //else {
                //    throw new HttpException(400, "Given token is not valid");
                //}
            }

            else {
                    $em->persist($user);
                    $em->flush($user);
            }
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

    /**
     * Finds friends of a user
     *
     * @Rest\Get("friends/{id}", name="show_friends")
     *
     */
    public function getFriends(User $user)
    {
        $friends = $user->getFriends();
        return $this->view($friends, Response::HTTP_OK);
    }

    /**
     * Remove friend of a user
     *
     * @Rest\Delete("friends/{id}/{friend}", name="delete_friend")
     *
     */
    public function deleteFriend(User $user, User $friend)
    {
        $user->removeFriend($friend);
        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * Add friend to a user
     *
     * @Rest\Post("friends/{id}/{friend}", name="add_friends")
     *
     */
    public function newFriend(User $user, User $friend)
    {
        $user->addFriend($friend);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush($user);

        return $this->view($user, Response::HTTP_OK);
    }

    private  function isValidGoogleToken($token_id) {
        $client = new \Google_Client();
        $client->setApplicationName("Where_Are_You_API");
        $client->setDeveloperKey("135317923400-spd82dqbrhcbq5k6nvskhdodgtb34ana.apps.googleusercontent.com");
        $is_valid = $client->verifyIdToken($token_id);

        if ($is_valid) {
            $client->setaccesstoken($token_id);
            $is_expired = $client->isAccessTokenExpired();
            if ($is_expired) {
                //TODO revalidate login
            }
        }

        return $is_valid;
    }

    public function validateModel($data) {
        $expected_data_member = array('Prename', 'Name', 'Mail', 'TokenId', 'ImageUrl');

        foreach ($expected_data_member as $data_member) {
            if ($data[$data_member] == '') {
                throw new HttpException(400, "Bad data");
            }
        }

        return true;
    }


}
