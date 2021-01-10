<?php
namespace IndieLogin;

class Client extends \IndieAuth\Client {

  public static $server;

  public static function discoverAuthorizationEndpoint($url) {
    // First try to find the user's authorization endpoint and continue directly if one is found
    $authorizationEndpoint = parent::discoverAuthorizationEndpoint($url);

    if($authorizationEndpoint)
      return $authorizationEndpoint;
    else
      return self::$server.'/auth';
  }

}
