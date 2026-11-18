<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "lab_management";

$connect = mysqli_connect($hostname,$username,$password,$database, 3306);

if(!$connect){
    die("Koneksi gagal :".mysqli_connect_error());
}
?>