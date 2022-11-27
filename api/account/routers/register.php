<?php

/*function route($method, $urlList, $requestData)
{
    global $Link;
    switch ($method) {
        case 'GET':
            $token = substr(getallheaders()['Authorization'], 7);
            $userFromToken = $Link->query("SELECT user FROM token WHERE value='$token'")->fetch_assoc();
            if(!is_null($userFromToken)) {
                $userID = $userFromToken['user'];
                $user = $Link->query("SELECT * FROM user WHERE id='$userID'")->fetch_assoc();
                echo json_encode($user);
            }
            else {
                setHTTPStatus("400", "There is no such user with supplied token");
            }
            break;
        case 'POST':
            $email = $requestData->body->email;
            $user = $Link->query("SELECT id FROM user WHERE email='$email'")->fetch_assoc();

            if (is_null($user)) {

                $password = hash("sha1", $requestData->body->password);
                $fullName = $requestData->body->fullName;
                $address = $requestData->body->address;
                $birthDate = $requestData->body->birthDate;
                $gender = $requestData->body->gender;
                $phoneNumber = $requestData->body->phoneNumber;
                $id = userId();

                $userInsertResult = $Link->query("INSERT INTO user(id, fullName, birthDate, gender, address, email, phoneNumber, password) VALUES ('$id', '$fullName', '$birthDate', '$gender', '$address', '$email', '$phoneNumber', '$password')");

                if(!$userInsertResult) {
                    setHTTPStatus("400", "DB error: $Link->error");
                }
                else {
                    $user = $Link->query("SELECT id, fullName, birthDate, gender, address, email, phoneNumber FROM user WHERE id = '$id'");
                    echo json_encode(['token' => generateUserToken($user)]);
                }

            } else {
                setHTTPStatus("409", "User with email '$email' already exists");
            }

            break;
    }
}*/

function register($method, $requestData) {

    if($method == "POST") {
        global $Link;
        $email = $requestData->body->email;
        $user = $Link->query("SELECT id FROM user WHERE email='$email'")->fetch_assoc();
        if (is_null($user)) {

            $password = hash("sha1", $requestData->body->password);
            $fullName = $requestData->body->fullName;
            $address = $requestData->body->address;
            $birthDate = $requestData->body->birthDate;
            $gender = $requestData->body->gender;
            $phoneNumber = $requestData->body->phoneNumber;
            $id = userId();

            $registerResult = registerValidation($email, $phoneNumber, $requestData->body->password);

            if($registerResult == "true") {
                $userInsertResult = $Link->query("INSERT INTO user(id, fullName, birthDate, gender, address, email, phoneNumber, password) VALUES ('$id', '$fullName', '$birthDate', '$gender', '$address', '$email', '$phoneNumber', '$password')");

                if(!$userInsertResult) {
                    setHTTPStatus("400", "DB error: $Link->error");
                }
                else {
                    $user = $Link->query("SELECT id, fullName, birthDate, gender, address, email, phoneNumber FROM user WHERE id = '$id'")->fetch_assoc();
                    include_once "api/account/helpers/token_generator.php";
                    $token = generateUserToken($user);
                    echo json_encode(['token' => $token]);
                }
            }
            else {
                setHTTPStatus("400", $registerResult);
            }

        } else {
            setHTTPStatus("409", "User with email '$email' already exists");
        }
    }

    else {
        setHTTPStatus("400", "You can only send POST requests to register");
    }

}