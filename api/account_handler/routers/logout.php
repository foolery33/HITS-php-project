<?php

require "libs/vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function logout($method)
{
    global $Link;
    global $Key;
    $token = substr(getallheaders()['Authorization'], 7);
    if ($token) {
        try {
            $decoded = JWT::decode($token, new Key($Key, 'HS256'));
            $searchResult = $Link->query("SELECT token FROM token_blacklist WHERE token = '$token'")->fetch_assoc();
            if (!$searchResult) {
                $Link->query("INSERT INTO token_blacklist(token) VALUES ('$token')");
                setHTTPStatus("200", "Logged out");
            } else {
                setHTTPStatus("401", "Your token is expired");
            }
        } catch (Exception $e) {
            if ($e->getMessage() == "Expired token") {
                setHTTPStatus("401", "Your token is expired");
            } else {
                setHTTPStatus("401", "Your token is not valid");
            }
        }
    } else {
        setHTTPStatus("401", "Your token is not valid");
    }
}