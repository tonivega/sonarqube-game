<?php
/**
 * User: gonzalo.cosin
 * Date: 10/08/2015
 */

namespace SonarQube;

include "constUrl.php";


class SonarQube
{

    private $defaultOpt = ['componentRoots' => 'org.prv.sonar:Privalia_front_common'];

    public function getAuth()
    {
        $url = URL_SONAR_QUBE . PATH_AUTH;

        $this->doCall($url);
    }


    public function getOpenIssues()
    {
        $url = URL_SONAR_QUBE . PATH_ISSUES;

        $opt = ['statuses' =>'OPEN'];

        $this->doCall($url, $opt);
    }

    private function doCall($url, $curlOpt =  null)
    {

        $url .= '?' . http_build_query($this->defaultOpt);


        if(!is_null($curlOpt)){
            $url .= '&' . http_build_query($curlOpt);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       // curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                "Authorization: Basic " . base64_encode($_GET['user'] . ":" . $_GET['pwd'])
            ));


        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errorCurl = curl_error($ch);
        $curl_errno= curl_errno($ch);


//var_dump(array($errorCurl,$curl_errno),$output);
        var_dump(json_decode($output));
        die();
    }
}