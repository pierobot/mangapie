<?php

namespace App\Interfaces;

interface NotificationInterface
{
    public function getId();
    public function getType();
    public function getMessage();
    public function getData();
    public function getDateTime();
}
