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


//try{
//
//
////$url = "http://sonarqube.privalia.pin/api/issues/search?componentRoots=org.prv.sonar:Privalia_front_common&statuses=RESOLVED";
////
//$url = "http://sonarqube.privalia.pin/api/authentication/validate";
//$username = "gcosin";
//$password = "gcosin";
//
//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
//
//curl_setopt($ch, CURLOPT_HTTPHEADER,
//            array(
//              "Authorization: Basic " . base64_encode($username . ":" . $password)
//));
//
//
//$output = curl_exec($ch);
//$info = curl_getinfo($ch);
//$errorCurl = curl_error($ch);
//    $curl_errno= curl_errno($ch);
//
//
////var_dump(array($errorCurl,$curl_errno),$output);
//var_dump(json_decode($output)->valid);
//
//    curl_close($ch);
//}catch (Exception $e){
//    echo "<pre>";
//    var_dump($e);
//    echo "</pre>";
//    die();
//}