<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 24-7-2018
 * Time: 10:53
 */

namespace Seacommerce\Mailcamp;

use PHPUnit\Framework\TestCase;
use Seacommerce\Mailcamp;
use Symfony\Component\Dotenv\Dotenv;


final class MailcampTest extends TestCase
{

    private static $settings;
    private static $newletterClient;
    private static $subscriber1;
    private static $subscriber2;
    private static $createdListId;
    private static $createdList;
    private static $newsletterid;
    private static $shopCode;

    public static function setUpBeforeClass()
    {

        (new Dotenv())->load(__DIR__.'/../.env');

        $mailcampSettings = new Mailcamp\Settings();

        $mailcampSettings->setEndpoint( $_SERVER['MAILCAMP_ENDPOINT']);
        $mailcampSettings->setUsername( $_SERVER['MAILCAMP_USER']);
        $mailcampSettings->setUserToken( $_SERVER['MAILCAMP_TOKEN']);

        $mailcampSettings->setProcessbounce(0);
        $mailcampSettings->setBounceemail("");
        $mailcampSettings->setBouncepassword("");
        $mailcampSettings->setBounceusername("");
        $mailcampSettings->setBounceserver("");

        $mailcampSettings->setCompanyaddress("");
        $mailcampSettings->setCompanyname("");
        $mailcampSettings->setCompanyphone("");

        $mailcampSettings->setReplytoemail("info@seacommerce.nl");
        $mailcampSettings->setOwneremail("info@seacommerce.nl");
        $mailcampSettings->setOwnername("Seacommerce");
        self::$settings = $mailcampSettings;

        $subscriber1 = new Mailcamp\Dto\Subscriber();
        $subscriber1->setEmailaddress("niels@seacommerce.nl");
        $subscriber1->setFirstname("Niels");
        $subscriber1->setLastname("Steenbakker");
        self::$subscriber1 = $subscriber1;

        $subscriber2 = new Mailcamp\Dto\Subscriber();
        $subscriber2->setEmailaddress("sil@seacommerce.nl");
        $subscriber2->setFirstname("Sil");
        $subscriber2->setLastname("Moelker");

        self::$subscriber2 = $subscriber2;

        self::$shopCode = "test-nl";
    }


    public function testMailcampInit()
    {
        self::$newletterClient = new Mailcamp\Mailcamp(self::$settings);
        $this->assertInstanceOf(Mailcamp\Mailcamp::class, self::$newletterClient);
    }


    /**
     * @depends testMailcampInit
     */
    public function testFindListByListNamePrefixFirst()
    {
        $lists = self::$newletterClient->findListByListNamePrefix(self::$shopCode);
        $this->assertEmpty($lists);
    }

    /**
     * @depends testMailcampInit
     */
    public function testCreateMailingList()
    {
        $result = self::$newletterClient->createMailingList(self::$shopCode);
        $this->assertGreaterThan(0, $result);
        self::$createdListId = $result;
    }

    /**
     * @depends testCreateMailingList
     */
    public function testFindListByListId()
    {
        $list = self::$newletterClient->findListById(self::$createdListId);
        $this->assertNotEmpty($list);
        self::$createdList = $list;
    }

    /**
     * @depends testCreateMailingList
     */
    public function testAddSubscriberToList()
    {
        self::$newletterClient->AddSubscriberToList(self::$subscriber1, self::$createdListId);
        self::$newletterClient->AddSubscriberToList(self::$subscriber2, self::$createdListId);
        $list = self::$newletterClient->findListById(self::$createdListId);
        $this->assertEquals(2, $list->subscribecount);
    }

    /**
     * @depends testAddSubscriberToList
     */
    public function testDeleteSubscriberFromList()
    {
        self::$newletterClient->DeleteSubscriberFromList(self::$subscriber2->getEmailaddress(), self::$createdListId);
        $list = self::$newletterClient->findListById(self::$createdListId);
        $this->assertEquals(1, $list->subscribecount);
    }

    /**
     * @depends testAddSubscriberToList
     */
    public function testCreateMailing()
    {
        $name = "Test mailing";
        $subject = "Test mailing #1";
        $textbody = "test mailing body";
        $htmlbody = "<html><head></head><body>" . $textbody . "</body></html>";
        self::$newsletterid = self::$newletterClient->createMailing($name, $subject, $textbody, $htmlbody);
        $this->assertGreaterThan(1, self::$newsletterid);
    }

    /**
     * @depends testCreateMailing
     */
    public function testSendMailing()
    {
        $result = self::$newletterClient->sendMailing( self::$createdListId,  self::$newsletterid);
        $this->assertGreaterThan(1, self::$newsletterid);
    }

    /**
     * @depends testCreateMailingList
     */
    public function testDeleteMailingList()
    {
        $result = self::$newletterClient->DeleteMailingList( self::$createdListId);
        $lists = self::$newletterClient->findListByListNamePrefix(self::$shopCode);
        $this->assertEmpty($lists);
    }
}