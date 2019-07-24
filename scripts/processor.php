<?php
/**
 * This script handles the form processing
 *
 * PHP version 7.2
 *
 * @category Registration
 * @package  Registration
 * @author   Benson Imoh,ST <benson@stbensonimoh.com>
 * @license  GPL https://opensource.org/licenses/gpl-license
 * @link     https://stbensonimoh.com
 */
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Pull in the required files
require '../config.php';
require './DB.php';
require './Notify.php';
require './Newsletter.php';


//Capture the post data coming from the form
$firstName =$_POST['firstName'];
$middleName =$_POST['middleName'] ;
$lastName =$_POST['lastName'];
$email = $_POST['email'];
$phone =$_POST['phone'];
$city =$_POST['city'];
$gender = htmlspecialchars($_POST['gender'], ENT_QUOTES);
$dob = htmlspecialchars($_POST['dob'], ENT_QUOTES);
$occupation =htmlspecialchars ($_POST['occupation'], ENT_QUOTES);
$mentor = htmlspecialchars($_POST['mentor'], ENT_QUOTES);
$ownABusiness = htmlspecialchars($_POST['ownABusiness'], ENT_QUOTES);
$organisation = htmlspecialchars($_POST['organisation'], ENT_QUOTES);
$motivation = htmlspecialchars($_POST['motivation'], ENT_QUOTES);
$reasonFormentor = htmlspecialchars($_POST['reasonFormentor'], ENT_QUOTES);
$websiteLink = htmlspecialchars($_POST['websiteLink'], ENT_QUOTES);
$linkedin = htmlspecialchars($_POST['linkedin'], ENT_QUOTES);
$twitter = htmlspecialchars($_POST['twitter'], ENT_QUOTES);
$instagram = htmlspecialchars($_POST['instagram'], ENT_QUOTES);
$facebook = htmlspecialchars($_POST['facebook'], ENT_QUOTES);

$details = array(
    "firstName" => $firstName,
    "middleName" => $middleName,
    "lastName" => $lastName,
    "email" => $email,
    "phone" => $phone,
    "city" => $city,
    "gender" => $gender,
    "dob" => $dob,
    "occupation"=> $occupation,
    "mentor" => $mentor,
    "ownABusiness" => $ownABusiness,
    "organisation" => $organisation,
    "motivation" => $motivation,
    "reasonFormentor" => $reasonFormentor,
    "websiteLink" => $websiteLink,
    "linkedin" => $linkedin,
    "twitter" => $twitter,
    "instagram" => $instagram,
    "facebook" => $facebook
);

$db = new DB($host, $db, $username, $password);

$notify = new Notify($smstoken, $emailHost, $emailUsername, $emailPassword, $SMTPDebug, $SMTPAuth, $SMTPSecure, $Port);

$newsletter = new Newsletter($apiUserId, $apiSecret);

// First check to see if the user is in the Database
if ($db->userExists($email, "mentor")) {
    echo json_encode("user_exists");
} else {
    // Insert the user into the database
    $db->getConnection()->beginTransaction();
    $db->insertUser("mentor", $details);
        // // Send SMS
        $notify->viaSMS("YouthSummit", "Dear {$firstName} {$lastName}, thank you for registering to be a part of AWLO Youth Summit in commemoration of the International Youth Day. We look forward to receiving you. Kindly check your mail for more details. Thank you.", $phone);

        /**
         * Add User to the SendPulse Mail List
         */
        $emails = array(
            array(
                'email'             => $email,
                'variables'         => array(
                    'name'          => $firstName,
                    'middleName'    => $middleName,
                    'lastName'      => $lastName,
                    'phone'         => $phone,
                    'gender'        => $gender,
                    'occupation'    => $occupation,
                    'organisation'  => $organisation,
                    'city'          => $city
                )
            )
        );

        $newsletter->insertIntoList("244188", $emails);

        $name = $firstName . ' ' . $lastName;
        // Send Email
        require './emails.php';
        // Send Email
        $notify->viaEmail("youthsummit@awlo.org", "AWLO Youth Summit", $email, $name, $emailBody, "AWLO International Youth Summit");

        $db->getConnection()->commit();

        echo json_encode("success");
}