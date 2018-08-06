<?php

namespace Seacommerce\Mailcamp;

use Exception;
use Seacommerce\Mailcamp\Dto\Request;
use Seacommerce\Mailcamp\Dto\Subscriber;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class Mailcamp
 * @package Seacommerce\Mailcamp
 */
class Mailcamp
{

    const CUSTOMFIELD_FIRSTNAME = 2;
    const CUSTOMFIELD_LASTNAME = 4;
    /**
     * @var integer
     */
    private $ownerid;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var array
     */
    private $mailinglists;

    /**
     * Mailcamp constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public
    function getOwnerId()
    {

        if (!$this->ownerid) {
            $request = $this->createRequest("authentication", "xmlapitest");

            $details = array(
                "" => ""
            );
            $request->details = $details;

            $response = $this->send($request);
            $this->ownerid = $response->userid;

        }
        return $this->ownerid;


    }

    /**
     * @param $requestType
     * @param $requestMethod
     * @return Request
     */
    public
    function createRequest($requestType, $requestMethod)
    {
        $request = new Request();
        $request->username = $this->settings->getUsername();
        $request->usertoken = $this->settings->getUsertoken();
        $request->requesttype = $requestType;
        $request->requestmethod = $requestMethod;
        $request->details = "";
        return $request;
    }

    /**
     * @return array
     * @throws Exception
     */
    public
    function getLists()
    {
            $request = $this->createRequest("user", "GetLists");

            $details = array(
                "lists" => null,
                "sortinfo" => null,
                "countonly" => null,
                "start" => null,
                "perpage" => null
            );
            $request->details = $details;
            $response = $this->send($request);
            $this->mailinglists = $response->item;
           return $response->item;
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public
    function createMailingList($name)
    {

        $request = $this->createRequest("lists", "Create");
        $details = array(
            "name" => $name,
            "owneremail" => $this->settings->getOwneremail(),
            "ownername" => $this->settings->getOwnername(),
            "bounceemail" => $this->settings->getBounceemail(),
            "replytoemail" => $this->settings->getReplytoemail(),
            "format" => $this->settings->getFormat(),
            "createdate" => strtotime('now'),
            "notifyowner" => $this->settings->getNotifyowner(),
            "bounceserver" => $this->settings->getBounceserver(),
            "bounceusername" => $this->settings->getBounceusername(),
            "bouncepassword" => $this->settings->getBouncepassword(),
            "extramailsettings" => $this->settings->getExtramailsettings(),
            "companyname" => $this->settings->getCompanyname(),
            "companyaddress" => $this->settings->getCompanyaddress(),
            "companyphone" => $this->settings->getCompanyphone(),
            "ownerid" => $this->getOwnerId(),
            "processbounce" => $this->settings->getProcessbounce(),
            "visiblefields" => $this->settings->getVisiblefields(),
            "customfields" => array(self::CUSTOMFIELD_FIRSTNAME, self::CUSTOMFIELD_LASTNAME),
        );
        $request->details = $details;
        $response = $this->send($request);
        // if success, returns listId
        return $response;
    }


    /**
     * @param Subscriber $subscriber
     * @param $listId
     * @return mixed
     * @throws Exception
     */
    public
    function IsSubscriberOnList(Subscriber $subscriber, $listId)
    {

        $request = $this->createRequest("subscribers", "IsSubscriberOnList");
        $details = array(
            "emailaddress" => $subscriber->getEmailaddress(),
            "listids" => $listId,
        );
        $request->details = $details;
        $response = $this->send($request);
        // if success, returns listId
        return $response;
    }


    /**
     * @param Subscriber $subscriber
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public
    function AddSubscriberToList(Subscriber $subscriber, $listid)
    {
        $request = $this->createRequest("subscribers", "AddSubscriberToList");

        $details = array(
            "emailaddress" => $subscriber->getEmailaddress(),
            "mailinglist" => $listid,
            "format" => $this->settings->getFormat(),
            "confirmed" => true,
            "ipaddress" => $_SERVER['REMOTE_ADDR'] ?? null,
            "subscribedate" => strtotime('now'),
            "autoresponder" => true,
            "customfields" => array(
                "item" => array(
                    array(
                        "fieldid" => self::CUSTOMFIELD_LASTNAME,
                        "value" => $subscriber->getLastname(),
                    ),
                    array(
                        "fieldid" => self::CUSTOMFIELD_FIRSTNAME,
                        "value" => $subscriber->getFirstname(),
                    )
                )
            ),
        );
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param Subscriber $subscriber
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public
    function EditSubscriber(Subscriber $subscriber, $listid)
    {
        $request = $this->createRequest("subscribers", "EditSubscriberCustomFields");

        $details = array(
            "emailaddress" => $subscriber->getEmailaddress(),
            "mailinglist" => $listid,
            "customfields" => array(
                "item" => array(
                    array(
                        "fieldid" => self::CUSTOMFIELD_LASTNAME,
                        "value" => $subscriber->getLastname(),
                    ),
                    array(
                        "fieldid" => self::CUSTOMFIELD_FIRSTNAME,
                        "value" => $subscriber->getFirstname(),
                    )
                )
            ),
        );
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public
    function createMailing($name, $subject, $textBody, $htmlbody)
    {
        $request = $this->createRequest("newsletters", "Create");
        $details = array(
            "name" => $name,
            //format BOTH (HTML and TEXT)
            "format" => "b",
            "subject" => $subject,
            "textbody" => $textBody,
            "htmlbody" => $htmlbody,
            "createdate" => strtotime('now'),
            "active" => 1,
            "archive" => 1,
            "ownerid" => $this->getOwnerId(),
        );
        $request->details = $details;

        $response = $this->send($request);
        return $response;
    }


    /**
     * @param $listid
     * @param $newsletterid
     * @return mixed
     * @throws Exception
     */
    public
    function sendMailing($listid, $newsletterid, $googleCampaignName = null)
    {
        $request = $this->createRequest("jobs", "Create");

        $list = $this->findListById($listid);

        $details = array(
            "jobtype" => "send",
            "jobstatus" => "w",
            "when" => strtotime('now'),
            "lists" => $listid,
            "fkid" => $listid,
            "fktype" => $listid,
            "ownerid" => $this->getOwnerId(),
            "approved" => 1,
            "details" => array(
                "NewsletterChosen" => $newsletterid,
                "Lists" => $listid,
                "SendCriteria" => array(
                    "Confirmed" => 1,
                    "Status" => "a",
                    "List" => $listid,
                ),
                "SendSize" => $list->subscribecount,
                "BackStep" => 1,
                "Multipart" => 1,
                "TrackOpens" => 1,
                "TrackLinks" => 1,
                "EmbedImages" => 0,
                "Newsletter" => $newsletterid,
                "SendFromName" => $this->settings->getOwnername(),
                "SentFromEmail" => $this->settings->getOwneremail(),
                "ReplyToEmail" => $this->settings->getReplytoemail(),
                "BounceEmail" => $this->settings->getBounceemail(),
                "To_FirstName" => self::CUSTOMFIELD_FIRSTNAME,
                "To_LastName" => self::CUSTOMFIELD_LASTNAME,
                "Charset" => "UTF-8",
                "NotifyOwner" => 1,
                "SendStartTime" => strtotime('now'),
                "module_tracker_google_options_name" => $googleCampaignName ?? $list->name ."-". $newsletterid . "-".date("d-m-Y"),
                "module_tracker_google_options_source" => "email",
                "EmailResults" => array(
                    "success" => 0,
                    "total" => 0,
                    "failure" => 0,
                ),
            ),
        );
        $request->details = $details;

        $response = $this->send($request);
        return $response;
    }

    /**
     * @param $emailadress
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public
    function DeleteSubscriberFromList($emailadress, $listid)
    {
        $request = $this->createRequest("subscribers", "DeleteSubscriber");

        $details = array(
            "emailaddress" => $emailadress,
            "listid" => $listid
        );
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }


    /**
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public
    function deleteMailingList($listid)
    {
        $request = $this->createRequest("lists", "Delete");
        $details = array(
            "listid" => $listid,
        );
        $request->details = $details;

        $response = $this->send($request);
        return $response;
    }


    /**
     * @param $listid
     * @return mixed
     */
    public
    function findListById($listid)
    {
        $lists = $this->getLists();
        if(!is_array($lists)) {
            $lists = [$lists];
        }
        $list = array_values(array_filter($lists, function ($list) use (&$listid) {
            return $list->listid === $listid;
        }));
        return $list[0];
    }

    /**
     * @param $listNamePrefix
     * @return array
     */
    public
    function findListByListNamePrefix($listNamePrefix)
    {
        $lists = $this->getLists();
        if(!is_array($lists)) {
            $lists = [$lists];
        }
        $list = array_values(array_filter($lists, function ($list) use (&$listNamePrefix) {
            return strpos($list->name, $listNamePrefix) === 0;
        }));

        return $list;
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public
    function send(Request $request)
    {

        $xml = $this->serializer->serialize($request, 'xml');
        $ch = curl_init($this->settings->getEndpoint());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $result = @curl_exec($ch);
        if ($result === false) {
            throw new Exception('Error performing request');

        } else {
            $arrayData = json_decode(json_encode((array)simplexml_load_string($result)));
            if ($arrayData->status == 'SUCCESS') {
                return $arrayData->data;
            } else {
                if( is_string($arrayData->errormessage)) {
                    throw new Exception($arrayData->errormessage);
                }
            }
        }
    }

}