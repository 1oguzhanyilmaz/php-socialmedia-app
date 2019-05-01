<?php

include '../../config/config.php';
include '../classes/User.php';
include '../classes/Notification.php';

$limit = 7; // yuklenecek mesaj sayisi

$notification = new Notification($con, $_REQUEST['userLoggedIn']);
echo $notification->getNotificationsDropdown($_REQUEST, $limit); // response
// echo $message->cagir();
?>