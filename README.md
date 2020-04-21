# Azure Active Directory Auth for Laravel

it's an easy and simple way to integrate azure active directory login with Laravel auth. 
this package replaces the default login/registration logic with OAuth2 and adds a user authenticity check with your Azure AD.

you can activate or deactivate this package without losing any Laravel Auth default functionality.

if deactivated, you can controller registration on your project by locking or unlocking it.


## Installation
* This package uses Laravel default auth, if you don't have it, please refer to this [documentation](https://laravel.com/docs/7.x/authentication) 
* you need to have "name, email, password" field on your "Users" database table


Use the package manager [composer](https://getcomposer.org/doc/00-intro.md/) to install azure-ad-auth.

```bash
composer require james-machouk/azure-ad-auth
```

## Usage


publish the package with artisan
```bash
composer require james-machouk/azure-ad-auth
```

add this lines to you .env file
```bash
AZURE_AD_TENANT_ID="your ad tenant id"
OAUTH_APP_ID="your app id"
OAUTH_APP_PASSWORD="your app password"
OAUTH_REDIRECT_URI=https://[YOUR DOMAIN]/callback
OAUTH_SCOPES='openid profile offline_access user.read calendars.read'
OAUTH_AUTHORITY=https://login.microsoftonline.com/
OAUTH_AUTHORIZE_ENDPOINT=/oauth2/v2.0/authorize
OAUTH_TOKEN_ENDPOINT=/oauth2/v2.0/token
OVERRIDE_DEFAULT_LOGIN=true
ALLOW_REGISTRATION=false
```
+ **AZURE_AD_TENANT_ID** / **OAUTH_APP_ID** / **OAUTH_APP_PASSWORD** : you'll find all this params in you Azure AD dashboard.
+ **OAUTH_REDIRECT_URI** : this is the callback uri set on your azure dashboard, if you are on dev envirenement with localhost, then your URI will be *http://localhost/callback*.
+ **OAUTH_SCOPES** : refer to this [documentation](https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-permissions-and-consent).
+ **OAUTH_AUTHORITY** / **OAUTH_AUTHORIZE_ENDPOINT** / **OAUTH_TOKEN_ENDPOINT** : this paths are given by microsoft, do not change them unless microsoft changes them.
+ **OVERRIDE_DEFAULT_LOGIN** : this params is to activate or deactivate the package
+ **ALLOW_REGISTRATION**  : this is to activate or deactivate the registration of the default Laravel Auth


after publishing, you'll find a new config file *azureAdAuth.php*
```php
//set you User model correct path
  "user_model" => App\User::class,
//this is where to redirect users if theirs login succeed ( user route name only )
  "redirect_success" => "home",
//this is where to redirect users if theirs login fails
  "redirect_fail" => "/",
```

go to your *routes/web.php* and remove 
```php
Auth::routes();
```
this will be handled by the package even if it's deactivated

## Issues
this package is created under Laravel 7,
if you encounter this issue *"Attribute [auth] does not exist."* read this [documentation](https://laravel.com/docs/7.x/upgrade#authentication-scaffolding)

## License
[MIT](https://choosealicense.com/licenses/mit/)
