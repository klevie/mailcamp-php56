<?php
/**
 * Created by PhpStorm.
 * User: niels
 * Date: 20-7-2018
 * Time: 10:37
 */

namespace Seacommerce\Mailcamp\Dto;


class Request
{
    /**
     * @var string
     */
    public $requesttype;
    /**
     * @var string
     */
    public $requestmethod;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $usertoken;
    /**
     * @var array
     */
    public $details;

}