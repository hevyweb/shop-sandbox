<?php

namespace App\Service;

class Status {
    public static function getStatuses()
    {
        return [
            1 => self::getNew(),
            2 => self::getAcknowledged(),
            3 => self::getSentToDelivery(),
            4 => self::getDeliveredStatus(),
            5 => self::getGotToCustomer(),
            6 => self::getRejectedStatus(),
        ];
    }

    public static function getActiveStatuses()
    {
        return [
            1 => self::getNew(),
            2 => self::getAcknowledged(),
            3 => self::getSentToDelivery(),
            4 => self::getDeliveredStatus(),
            5 => self::getGotToCustomer(),
        ];
    }

    public static function getNew()
    {
        return 'New';
    }

    public static function getAcknowledged()
    {
        return 'Acknowledged';
    }

    public static function getSentToDelivery()
    {
        return 'Sent to delivery';
    }

    public static function getDeliveredStatus()
    {
        return 'Delivered to destination';
    }

    public static function getGotToCustomer()
    {
        return 'Got to the customer';
    }

    public static function getRejectedStatus()
    {
        return 'Rejected';
    }
}