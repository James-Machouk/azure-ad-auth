<?php

namespace JamesMachouk\azureAdAuth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AzureAdAuthController extends Controller
{
    public function signin()
      {
        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
          'clientId'                => env('OAUTH_APP_ID'),
          'clientSecret'            => env('OAUTH_APP_PASSWORD'),
          'redirectUri'             => env('OAUTH_REDIRECT_URI'),
          'urlAuthorize'            => env('OAUTH_AUTHORITY').env('AZURE_AD_TENANT_ID').env('OAUTH_AUTHORIZE_ENDPOINT'),
          'urlAccessToken'          => env('OAUTH_AUTHORITY').env('AZURE_AD_TENANT_ID').env('OAUTH_TOKEN_ENDPOINT'),
          'urlResourceOwnerDetails' => '',
          'scopes'                  => env('OAUTH_SCOPES')
        ]);

        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        session(['oauthState' => $oauthClient->getState()]);

        // Redirect to AAD signin page
        return redirect()->away($authUrl);
      }


      public function azureCallback(Request $request)
        {
          // Validate state
          $expectedState = session('oauthState');
          $request->session()->forget('oauthState');
          $providedState = $request->query('state');

          if (!isset($expectedState)) {
            // If there is no expected state in the session,
            // do nothing and redirect to the home page.
            return redirect()->route(config('azureAdAuth.redirect_success'));
          }

          if (!isset($providedState) || $expectedState != $providedState) {
            return redirect()->route(config('azureAdAuth.redirect_fail'))
              ->with('error', 'Invalid auth state')
              ->with('errorDetail', 'The provided auth state did not match the expected value');
          }

          // Authorization code should be in the "code" query param
          $authCode = $request->query('code');
          if (isset($authCode)) {
            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
              'clientId'                => env('OAUTH_APP_ID'),
              'clientSecret'            => env('OAUTH_APP_PASSWORD'),
              'redirectUri'             => env('OAUTH_REDIRECT_URI'),
              'urlAuthorize'            => env('OAUTH_AUTHORITY').env('AZURE_AD_TENANT_ID').env('OAUTH_AUTHORIZE_ENDPOINT'),
              'urlAccessToken'          => env('OAUTH_AUTHORITY').env('AZURE_AD_TENANT_ID').env('OAUTH_TOKEN_ENDPOINT'),
              'urlResourceOwnerDetails' => '',
              'scopes'                  => env('OAUTH_SCOPES')
            ]);

            try {
              // Make the token request
              $accessToken = $oauthClient->getAccessToken('authorization_code', [
                'code' => $authCode
              ]);

              // hna we speak m3a 7bibna Graph
              $ch = curl_init();
              // set url
              curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me");
              curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Content-Type : application/json',
              'authorization : Bearer '.$accessToken->getToken()));
              //return the transfer as a string
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

              // $output contains the output string
              $output = curl_exec($ch);

              // close curl resource to free up system resources
              curl_close($ch);

              if($this->checkUser(json_decode($output))){
                  //get this from config "redirect_success"
                  $thepage= config('azureAdAuth.redirect_success');
                  return  redirect()->route($thepage);
              }
              // --- TODO : Make a good code for ERROR Handling logic ...
                return redirect()->route(config('azureAdAuth.redirect_fail'))
                    ->with('error', 'Access token received')
                    ->with('errorDetail', $accessToken->getToken());
            }
            catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return redirect()->route(config('azureAdAuth.redirect_fail'))
                    ->with('error', 'Error requesting access token')
                    ->with('errorDetail', $e->getMessage());
            }
          }
            return redirect()->route(config('azureAdAuth.redirect_fail'))
                ->with('error', $request->query('error'))
                ->with('errorDetail', $request->query('error_description'));
        }

        // Hna call the Real login function of laravel
        private function checkUser($_AdUser)
        {
            //get the user path from config
            $u = config('azureAdAuth.user_model');
            $theUser = $u::where('email', $_AdUser->userPrincipalName)->first();
            if($theUser == null){
                // create the user
                $usr = new $u();
                $usr->email = $_AdUser->userPrincipalName;
                $usr->name = $_AdUser->displayName;
                $usr->password = Hash::make($_AdUser->id);
                $usr->save();

                $theUser = $usr;
            }
            Auth::login($theUser);
            return Auth::check();
        }

        public function adLogout()
        {
            $currentUser = Auth::user();
            if ($currentUser == null) {
                //No Laravel user is found try to logout from Azure
                $azureLogoutPath = env('OAUTH_AUTHORITY') . env('AZURE_AD_TENANT_ID') . '/oauth2/v2.0/logout?post_logout_redirect_uri=' . urlencode(env('OAUTH_REDIRECT_AFTER_LOGOUT_URI'));
                return redirect()->away($azureLogoutPath);
            } else {
                // the laravel session is still alive you need to logout from laravel before logging out from Azure
                throw new \Exception('you need to logout from laravel before calling the AD logout URL.');
            }
        }
}
