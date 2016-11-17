<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $prename;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $friends;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $google_id_token;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $image_url;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prename
     *
     * @param string $prename
     *
     * @return User
     */
    public function setPrename($prename)
    {
        $this->prename = $prename;

        return $this;
    }

    /**
     * Get prename
     *
     * @return string
     */
    public function getPrename()
    {
        return $this->prename;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * add friend
     *
     * @param User $user
     *
     * @return array
     */
    public function addFriend(User $user) {
        $this->friends[] = $user;
        return $this->friends;
    }

    /**
     * add friend
     *
     * @param User $user
     *
     * @return array
     */
    public function removeFriend(User $user) {
        $user_index = array_search($user, $this->friends);
        unset($this->friends[$user_index]);
        return $this->friends;
    }

    /**
     * Set friends
     *
     * @param string $friends
     *
     * @return array
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;

        return $this->friends;
    }

    /**
     * Get friends
     *
     * @return array
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * Set google_id_token
     *
     * @param string
     *
     * @return User
     */
    public function setGoogleIdToken($google_id_token)
    {
        $this->google_id_token = $google_id_token;

        return $this;
    }

    /**
     * Get google_id_token
     *
     * @return string
     */
    public function getGoogleIdToken()
    {
        return $this->google_id_token;
    }

    /**
     * Set image_url
     *
     * @param string
     *
     * @return User
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;

        return $this;
    }

    /**
     * Get image_url
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }
}
