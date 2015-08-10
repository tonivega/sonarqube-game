<?php

include "lib/SonarQube.php";

if(
    !isset($_COOKIE['user']) || (isset($_COOKIE['user']) && is_null($_COOKIE['user']))
    &&
    !isset($_COOKIE['pwd']) || (isset($_COOKIE['pwd']) && is_null($_COOKIE['pwd']))


){
    if(isset($_GET['user']) && $_GET['pwd']){
        setcookie('user',$_GET['user']);
        setcookie('pwd',$_GET['pwd']);
    }else{
        die("fail");
    }
}



$sonar = new SonarQube\SonarQube();

//$sonar->getAuth();

$sonar->getOpenIssues();

