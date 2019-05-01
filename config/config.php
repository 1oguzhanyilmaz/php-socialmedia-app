<?php
ob_start(); // Cannot modify header information - headers already sent by hatasi icin kullanildi.
session_start();

$timezone = date_default_timezone_set("Europe/Istanbul");

$con = mysqli_connect("localhost", "root", "", "socialMedia1"); // connection

if (mysqli_connect_errno()) {
	echo "VT Baglantisi Basarisiz : " . mysqli_connect_errno();
}

?>