<?php
$host="localhost";
$user="root";
$pass="";
$dbname="product_sunglasses";

$conn= new mysqli($host,$user,$pass,$dbname);

if($conn ->connect_error){
    die("Connection failed:". $conn ->connect_error);
}
//echo "database connected succesfully!";