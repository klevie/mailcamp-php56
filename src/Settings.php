<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 20-7-2018
 * Time: 16:29
 */

namespace Seacommerce\Mailcamp;


class Settings
{
    private $endpoint;
    private $username;
    private $usertoken;

    private $owneremail;
    private $ownername;
    private $bounceemail;
    private $replytoemail;
    private $format;
    private $notifyowner = 1;
    private $imapaccount;
    private $bounceserver;
    private $bounceusername;
    private $bouncepassword;
    private $extramailsettings;
    private $companyname;
    private $companyaddress;
    private $companyphone;
    private $processbounce = 1;
    private $visiblefields = "emailaddress,subscribedate,updated,status,confirmed";

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param mixed $endpoint
     * @return Settings
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return Settings
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsertoken()
    {
        return $this->usertoken;
    }

    /**
     * @param mixed $usertoken
     * @return Settings
     */
    public function setUsertoken($usertoken)
    {
        $this->usertoken = $usertoken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwneremail()
    {
        return $this->owneremail;
    }

    /**
     * @param mixed $owneremail
     * @return Settings
     */
    public function setOwneremail($owneremail)
    {
        $this->owneremail = $owneremail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwnername()
    {
        return $this->ownername;
    }

    /**
     * @param mixed $ownername
     * @return Settings
     */
    public function setOwnername($ownername)
    {
        $this->ownername = $ownername;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBounceemail()
    {
        return $this->bounceemail;
    }

    /**
     * @param mixed $bounceemail
     * @return Settings
     */
    public function setBounceemail($bounceemail)
    {
        $this->bounceemail = $bounceemail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplytoemail()
    {
        return $this->replytoemail;
    }

    /**
     * @param mixed $replytoemail
     * @return Settings
     */
    public function setReplytoemail($replytoemail)
    {
        $this->replytoemail = $replytoemail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     * @return Settings
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getNotifyowner()
    {
        return $this->notifyowner;
    }

    /**
     * @param mixed $notifyowner
     * @return Settings
     */
    public function setNotifyowner($notifyowner)
    {
        $this->notifyowner = $notifyowner;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImapaccount()
    {
        return $this->imapaccount;
    }

    /**
     * @param mixed $imapaccount
     * @return Settings
     */
    public function setImapaccount($imapaccount)
    {
        $this->imapaccount = $imapaccount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBounceserver()
    {
        return $this->bounceserver;
    }

    /**
     * @param mixed $bounceserver
     * @return Settings
     */
    public function setBounceserver($bounceserver)
    {
        $this->bounceserver = $bounceserver;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBounceusername()
    {
        return $this->bounceusername;
    }

    /**
     * @param mixed $bounceusername
     * @return Settings
     */
    public function setBounceusername($bounceusername)
    {
        $this->bounceusername = $bounceusername;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBouncepassword()
    {
        return $this->bouncepassword;
    }

    /**
     * @param mixed $bouncepassword
     * @return Settings
     */
    public function setBouncepassword($bouncepassword)
    {
        $this->bouncepassword = $bouncepassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtramailsettings()
    {
        return $this->extramailsettings;
    }

    /**
     * @param mixed $extramailsettings
     * @return Settings
     */
    public function setExtramailsettings($extramailsettings)
    {
        $this->extramailsettings = $extramailsettings;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * @param mixed $companyname
     * @return Settings
     */
    public function setCompanyname($companyname)
    {
        $this->companyname = $companyname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyaddress()
    {
        return $this->companyaddress;
    }

    /**
     * @param mixed $companyaddress
     * @return Settings
     */
    public function setCompanyaddress($companyaddress)
    {
        $this->companyaddress = $companyaddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyphone()
    {
        return $this->companyphone;
    }

    /**
     * @param mixed $companyphone
     * @return Settings
     */
    public function setCompanyphone($companyphone)
    {
        $this->companyphone = $companyphone;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getProcessbounce()
    {
        return $this->processbounce;
    }

    /**
     * @param mixed $processbounce
     * @return Settings
     */
    public function setProcessbounce($processbounce)
    {
        $this->processbounce = $processbounce;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisiblefields()
    {
        return $this->visiblefields;
    }

    /**
     * @param mixed $visiblefields
     * @return Settings
     */
    public function setVisiblefields($visiblefields)
    {
        $this->visiblefields = $visiblefields;
        return $this;
    }

}