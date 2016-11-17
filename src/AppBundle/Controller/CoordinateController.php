<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Coordinate;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;


/**
 * Coordinate controller.
 */
class CoordinateController extends FOSRestController
{
    /**
     * Get coordinate for user
     *
     * @Rest\Get("coordinate/{id}", name="coordinate_index")
     */
    public function showAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $coordinate = $em->getRepository('AppBundle:Coordinate')->findOneBy(array('user_id' => $user->getId()));
        return $this->view($coordinate, Response::HTTP_OK);
    }

    /**
     * Update coordinate for user
     *
     * @Rest\Post("coordinate/{id}", name="coordinate_new")
     */
    public function updateAction(Request $request, User $user)
    {
        $data = json_decode($request->getContent(), true);

        $long = $data['long'];
        $lat = $data['lat'];

        if ($long && $lat) {
            $coordinate = new Coordinate();
            $coordinate->setUserId($user->getId());
            $coordinate->setLongitude($long);
            $coordinate->setLatitude($lat);

            $em = $this->getDoctrine()->getManager();
            $existing_coordinate = $em->getRepository('AppBundle:Coordinate')->findOneBy(array('user_id' => $user->getId()));

            if ($existing_coordinate) {
                $coordinate = $existing_coordinate;
            }

            $em->persist($coordinate);
            $em->flush($coordinate);
        } else {
            throw new HttpException(400, "No coordinates set");
        }

        return $this->view($coordinate, Response::HTTP_OK);
    }
}
