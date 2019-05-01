<?php

include '../../config/config.php';
include '../classes/User.php';
include '../classes/Message.php';

$limit = 6; // yuklenecek mesaj sayisi

$message = new Message($con, $_REQUEST['userLoggedIn']);
echo $message->getSohbetDropdown($_REQUEST, $limit); // response
// echo $message->cagir();
?>