<?php

namespace Seacommerce\Mailcamp;

use Exception;
use Seacommerce\Mailcamp\Dto\Request;
use Seacommerce\Mailcamp\Dto\Subscriber;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getOwnerId()
    {

        if (!$this->ownerid) {
            $request = $this->createRequest("authentication", "xmlapitest");

            $details = [
                "" => "",
            ];
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
    public function createRequest(
        $requestType,
        $requestMethod
    ) {
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
    public function getLists()
    {
        $request = $this->createRequest("user", "GetLists");

        $details = [
            "lists" => null,
            "sortinfo" => null,
            "countonly" => null,
            "start" => null,
            "perpage" => null,
        ];
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
    public function createMailingList($name)
    {

        $request = $this->createRequest("lists", "Create");
        $details = [
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
            "customfields" => [self::CUSTOMFIELD_FIRSTNAME, self::CUSTOMFIELD_LASTNAME],
        ];
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
    public function IsSubscriberOnList(Subscriber $subscriber, $listId)
    {

        $request = $this->createRequest("subscribers", "IsSubscriberOnList");
        $details = [
            "emailaddress" => $subscriber->getEmailaddress(),
            "listids" => $listId,
        ];
        $request->details = $details;
        $response = $this->send($request);
        // if success, returns listId
        return $response;
    }


    /**
     * @param Subscriber $subscriber
     * @param int $listid
     * @param array $customFieldValues
     * @return mixed
     * @throws Exception
     */
    public function AddSubscriberToList(Subscriber $subscriber, $listid, $customFieldValues = [])
    {
        $request = $this->createRequest("subscribers", "AddSubscriberToList");

        $details = [
            "emailaddress" => $subscriber->getEmailaddress(),
            "mailinglist" => $listid,
            "format" => $this->settings->getFormat(),
            "confirmed" => true,
            "ipaddress" => $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : null,
            "subscribedate" => strtotime('now'),
            "autoresponder" => true,
            "customfields" => [
                "item" => array_merge($customFieldValues,
                    [
                        [
                            "fieldid" => self::CUSTOMFIELD_LASTNAME,
                            "value" => $subscriber->getLastname(),
                        ],
                        [
                            "fieldid" => self::CUSTOMFIELD_FIRSTNAME,
                            "value" => $subscriber->getFirstname(),
                        ],
                    ]),
            ],
        ];
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param Subscriber $subscriber
     * @param int $listid
     * @return mixed
     * @throws Exception
     */
    public function ActivateSubscriberForList(Subscriber $subscriber, $listid) {
        $request = $this->createRequest("subscribers", "ActivateSubscriber");

        $details = [
            "emailaddress" => $subscriber->getEmailaddress(),
            "listid" => $listid,
        ];
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param int $listId
     * @param int $numToRetrieve
     * @return mixed
     * @throws Exception
     */
    public function GetArchiveMailings($listId, $numToRetrieve = 99)
    {

        $request = $this->createRequest("lists", "GetArchives");
        $details = [
            "listid" => $listId,
            "num_to_retrieve" => $numToRetrieve ? $numToRetrieve : 99,
        ];
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }


    public function GetNewsletters($ownerId = null)
    {
        $request = $this->createRequest("newsletters", "GetNewsletters");
        $details = [
            "ownerid" => $this->getOwnerId(),
            "sortinfo" => [
                "SortBy" => "Date",
                "direction" => "down",
            ],
            "start" => 0,
            "perpage" => 99,
            "getLastSentDetails" => false,
        ];
        $request->details = $details;
        $response = $this->send($request);
        return $response->item;
    }

    /**
     * @param Subscriber $subscriber
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public function EditSubscriber(
        Subscriber $subscriber,
        $listid
    ) {
        $request = $this->createRequest("subscribers", "EditSubscriberCustomFields");

        $details = [
            "emailaddress" => $subscriber->getEmailaddress(),
            "mailinglist" => $listid,
            "customfields" => [
                "item" => [
                    [
                        "fieldid" => self::CUSTOMFIELD_LASTNAME,
                        "value" => $subscriber->getLastname(),
                    ],
                    [
                        "fieldid" => self::CUSTOMFIELD_FIRSTNAME,
                        "value" => $subscriber->getFirstname(),
                    ],
                ],
            ],
        ];
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }

    /**
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public function createMailing(
        $name,
        $subject,
        $textBody,
        $htmlbody
    ) {
        $request = $this->createRequest("newsletters", "Create");
        $details = [
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
        ];
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
    public function sendMailing(
        $listid,
        $newsletterid,
        $googleCampaignName = null
    ) {
        $request = $this->createRequest("jobs", "Create");

        $list = $this->findListById($listid);

        $details = [
            "jobtype" => "send",
            "jobstatus" => "w",
            "when" => strtotime('now'),
            "lists" => $listid,
            "fkid" => $listid,
            "fktype" => $listid,
            "ownerid" => $this->getOwnerId(),
            "approved" => 1,
            "details" => [
                "NewsletterChosen" => $newsletterid,
                "Lists" => $listid,
                "SendCriteria" => [
                    "Confirmed" => 1,
                    "Status" => "a",
                    "List" => $listid,
                ],
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
                "module_tracker_google_options_name" => $googleCampaignName ? $googleCampaignName : $list->name . "-" . $newsletterid . "-" . date("d-m-Y"),
                "module_tracker_google_options_source" => "email",
                "EmailResults" => [
                    "success" => 0,
                    "total" => 0,
                    "failure" => 0,
                ],
            ],
        ];
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
    public function DeleteSubscriberFromList(
        $emailadress,
        $listid
    ) {
        $request = $this->createRequest("subscribers", "DeleteSubscriber");

        $details = [
            "emailaddress" => $emailadress,
            "listid" => $listid,
        ];
        $request->details = $details;
        $response = $this->send($request);
        return $response;
    }


    /**
     * @param $listid
     * @return mixed
     * @throws Exception
     */
    public function deleteMailingList(
        $listid
    ) {
        $request = $this->createRequest("lists", "Delete");
        $details = [
            "listid" => $listid,
        ];
        $request->details = $details;

        $response = $this->send($request);
        return $response;
    }


    /**
     * @param $listid
     * @return mixed
     */
    public function findListById(
        $listid
    ) {
        $lists = $this->getLists();
        if (!is_array($lists)) {
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
    public function findListByListNamePrefix(
        $listNamePrefix
    ) {
        $lists = $this->getLists();
        if (!is_array($lists)) {
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
    public function send(
        Request $request
    ) {

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
                if (is_string($arrayData->errormessage)) {
                    throw new Exception($arrayData->errormessage);
                }
            }
        }
    }
}
