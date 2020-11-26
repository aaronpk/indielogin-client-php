IndieLogin Client
=================

This is a helper library to use with services like IndieLogin.com. That service provides an API that works very similar to the [IndieAuth](https://indieauth.net) protocol, but can authenticate users with a number of different methods, including IndieAuth, email, PGP keys, GitHub and Twitter.

When users log in with this library, it will first check their website for an authorization endpoint and do an IndieAuth flow directly if found. Otherwise, it will redirect them to the configured server to authenticate there.


Usage
-----

### Create a Login Form

You'll first need to create a login form to prompt the user to enter their website address. This might look something like the HTML below.

```html
<form action="/login.php" method="post">
  <input type="url" name="url">
  <input type="submit" value="Log In">
</form>
```

### Begin the Login Flow

In the `login.php` file, you'll need to initialize the session, and tell this library to discover the user's endpoints. If everything succeeds, the library will return a URL that you can use to redirect the user to begin the flow.

The example below will have some really basic error handling, which you'll probably want to replace with something nicer looking.

Example `login.php` file:

```php
<?php

if(!isset($_POST['url'])) {
  die('Missing URL');
}

// Start a session for the library to be able to save state between requests.
session_start();

// You'll need to set up two pieces of information before you can use the client,
// the client ID and and the redirect URL.

// Configure the server that exposes this IndieAuth-like API, without a trailing slash
IndieLogin\Client::$server = 'https://indielogin.com';

// The client ID should be the home page of your app.
IndieLogin\Client::$clientID = 'https://example.com/';

// The redirect URL is where the user will be returned to after they approve the request.
IndieLogin\Client::$redirectURL = 'https://example.com/redirect.php';

// Pass the user's URL to the client.
list($authorizationURL, $error) = IndieLogin\Client::begin($_POST['url']);

// Redirect the user to the authorization endpoint, either their own or your configured server
header('Location: '.$authorizationURL);
```

The following scopes have special meaning to the authorization server and will request the user's full profile info instead of just verifying their profile URL:

* `profile`
* `email`


### Handling the Redirect

In your redirect file, you just need to pass all the query string parameters to the library and it will take care of things! It will use the authorization or token endpoint it found in the initial step, and will use the authorization code to verify the profile information.

The result will be the response from the authorization endpoint, which will contain the user's final `me` URL as well as profile info if you requested one or more scopes.

If there were any problems, the error information will be returned to you as well.

The library takes care of verifying the final returned profile URL has the same authorization endpoint as the entered URL.

Example `redirect.php` file:

```php
<?php
session_start();
IndieLogin\Client::$server = 'https://indielogin.com';
IndieLogin\Client::$clientID = 'https://example.com/';
IndieLogin\Client::$redirectURL = 'https://example.com/redirect.php';

list($response, $error) = IndieLogin\Client::complete($_GET);

if($error) {
  echo "<p>Error: ".$error['error']."</p>";
  echo "<p>".$error['error_description']."</p>";
} else {
  // Login succeeded!
  // The library will return the user's profile URL in the property "me"
  // It will also return the full response from the authorization or token endpoint, as well as debug info
  echo "URL: ".$response['me']."<br>";

  // The full parsed response from the endpoint will be available as:
  // $response['response']

  // The raw response:
  // $response['raw_response']

  // The HTTP response code:
  // $response['response_code']

  // You'll probably want to save the user's URL in the session
  $_SESSION['user'] = $user['me'];
}
```

License
-------

Copyright 2013-2020 by Aaron Parecki and contributors

Available under the MIT and Apache 2.0 licenses. See LICENSE.txt

