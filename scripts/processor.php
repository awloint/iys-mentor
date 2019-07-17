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
// echo json_encode($_POST);

// Pull in the required files
require '../config.php';
require './DB.php';
require './Notify.php';
require './Newsletter.php';

// Capture the post data coming from the form
$id = $_POST['id']
$firstName = $_POST['firstName'];
$middleName = $_POST['middleName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$city = $_POST['city'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];
$occupation = $_POST['occupation'];
$mentor = $_POST['mentor'];
$ownABusiness = $_POST['ownABusiness'];
$organisation = $_POST['organisation'];
$motivation = $_POST['motivation'];
$reasonFormentor = $_POST['reasonFormentor'];
$websiteLink = $_POST['websiteLink'];
$linkedin = $_POST['linkedin'];
$twitter = $_POST['twitter'];
$instagram = $_POST['instagram'];
$facebook = $_POST['facebook'];
$created_at = $_POST['created_at'];

$details = array(
    $id = $_POST['id']
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $occupation = $_POST['occupation'];
    $mentor = $_POST['mentor'];
    $ownABusiness = $_POST['ownABusiness'];
    $organisation = $_POST['organisation'];
    $motivation = $_POST['motivation'];
    $reasonFormentor = $_POST['reasonFormentor'];
    $websiteLink = $_POST['websiteLink'];
    $linkedin = $_POST['linkedin'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $facebook = $_POST['facebook'];
    $created_at = $_POST['created_at'];
    
);

$db = new DB($host, $db, $username, $password);

$notify = new Notify($smstoken, $emailHost, $emailUsername, $emailPassword, $SMTPDebug, $SMTPAuth, $SMTPSecure, $Port);

$newsletter = new Newsletter($apiUserId, $apiSecret);

// First check to see if the user is in the Database
if ($db->userExists($email, "iys_participation")) {
    echo json_encode("user_exists");
} else {
    // Insert the user into the database
    $db->getConnection()->beginTransaction();
    $db->insertUser("iys_participation", $details);
        // Send SMS
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
                    'city'          => $city
                )
            )
        );

        $newsletter->insertIntoList("2414419", $emails);

        $name = $firstName . ' ' . $lastName;
        // Send Email
        require './emails.php';
        // Send Email
        $notify->viaEmail("youthsummit@awlo.org", "AWLO Youth Summit", $email, $name, $emailBody, "AWLO International Youth Summit");

        $db->getConnection()->commit();

        echo json_encode("success");
}