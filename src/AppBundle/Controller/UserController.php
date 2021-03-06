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
            //check if there's already a user
            $existing_user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $mail));
            if ($existing_user) {
                //check if google token was updated if so check
                //check if the new one is valid
                //if yes return that user with new token
                //else throw http exeption
                if ($existing_user->getGoogleIdToken() != $google_id_token) {
                    $existing_user->setGoogleIdToken($google_id_token);
                    $em->persist($existing_user);
                    $em->flush($existing_user);
                }

                if ($this->isValidGoogleToken($existing_user->getGoogleIdToken())) {
                    $user = $existing_user;
                }

                else {
                    throw new HttpException(400, "Given token is not valid");
                }

            }
            //save new user to database if it's not already existent
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
     * Finds available friends for a user
     *
     * @Rest\Get("friends/available/{id}", name="show_available_friends")
     *
     */
    public function getAvailableFriends(User $user)
    {

        $user_ids = $user->getFriends();

        $user_repo = $this->getDoctrine()
            ->getRepository('AppBundle:User');

        $query = $user_repo->createQueryBuilder('f')
            ->where('f.id != :user_ids')
            ->andWhere('f.id != :user_id')
            ->setParameters(array('user_ids' => $user_ids, 'user_id' => $user->getId()))
            ->getQuery();
        $available_users = $query->getResult();

        return $this->view($available_users, Response::HTTP_OK);
    }

    /**
     * Finds friends of a user
     *
     * @Rest\Get("friends/{id}", name="show_friends")
     *
     */
    public function getFriends(User $user)
    {
        $friend_ids = $user->getFriends();
        $em = $this->getDoctrine()->getManager();
        //get all users which for the defined friend_ids
        $friends = $em->getRepository('AppBundle:User')->findBy(array('id' => $friend_ids));

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
        if ($user->friendExists($friend->getId())) {
            $user->removeFriend($friend->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush($user);
        }
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
        if (!$user->friendExists($friend->getId())) {
            $user->addFriend($friend->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush($user);
        }

        return $this->view($user, Response::HTTP_OK);
    }

    private  function isValidGoogleToken($token_id) {
        $client = new \Google_Client();
        $client->setApplicationName("WhereAreYou");
        $client->setDeveloperKey("135317923400-spd82dqbrhcbq5k6nvskhdodgtb34ana.apps.googleusercontent.com");
        $client->setClientSecret('dMl4EsItwxf5BRiz-diaeAJl');
        $is_valid = $client->verifyIdToken($token_id);

        return $is_valid;
    }

    private function validateModel($data) {
        $expected_data_member = array('Prename', 'Name', 'Mail', 'TokenId', 'ImageUrl');

        foreach ($expected_data_member as $data_member) {
            if ($data[$data_member] == '') {
                throw new HttpException(400, "Bad data");
            }
        }
        return true;
    }


}
