<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 20-7-2018
 * Time: 10:37
 */

namespace Seacommerce\Mailcamp\Dto;


class Subscriber
{
    private $emailaddress;
    private $firstname;
    private $lastname;

    /**
     * @return mixed
     */
    public function getEmailaddress()
    {
        return $this->emailaddress;
    }

    /**
     * @param mixed $emailaddress
     */
    public function setEmailaddress($emailaddress)
    {
        $this->emailaddress = $emailaddress;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

}