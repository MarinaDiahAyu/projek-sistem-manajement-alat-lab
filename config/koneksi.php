<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "lab_management";

$connect = mysqli_connect($hostname,$username,$password,$database, 8111 );

if(!$connect){
    die("Koneksi gagal :".mysqli_connect_error());
}
?>