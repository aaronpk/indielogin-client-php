<?php
namespace IndieLogin;

class Client extends \IndieAuth\Client {

  public static $server;

  public static function begin($url, $scope=false, $authorizationEndpoint=false) {

    // First try to find the user's authorization endpoint and continue directly if one is found
    $authorizationEndpoint = self::discoverAuthorizationEndpoint(self::normalizeMeURL($url));

    if(!$authorizationEndpoint) {
      $authorizationEndpoint = self::$server.'/auth';
    }

    return parent::begin($url, $scope, $authorizationEndpoint);
  }

}
