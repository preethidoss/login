<?php
/**
 * Login model
 * Interact with DF API to seach for login authentication
 */

namespace App;

use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\HasApiTokens;
use Exception;
use RuntimeException;

class Login extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'email', 'password', 'city', 'company_name', 'last_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function AauthAcessToken()
    {
        return $this->hasMany(OauthAccessToken::class);
    }

    /**
     * @param string $email
     * @param string $password
     * @return mixed
     * @throws \Exception
     */
    public static function doLogin(string $email, string $password)
    {
        $url = env('DF_API_BASE') . '/login';
        $res = array();
        $jwt_token = null;
        $response_body = null;

        try {
            $client = new Client;
            $response = $client->post($url, [
                'form_params' =>
                    [
                        'email' => $email,
                        'password' => $password,
                    ]
            ]);
            $jwt_token = $response->getHeader('Authorization')[0];
            $response_body = $response->getBody()->getContents();

        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        $res['jwt_token'] = empty($jwt_token) ? null : $jwt_token;
        $res['body'] = empty($response_body) ? null : $response_body;
        return $res;
    }

    public static function doLogout()
    {
        $url = env('DF_API_BASE') . '/logout';

        try {
            $client = new Client;
            $response = $client->delete($url);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $response;
    }

    /**
     * Get User information after authorization
     *
     * @param string $jwt_token
     * @return string
     */
    public static function getUserData(string $jwt_token)
    {
        $userUrl = env('DF_API_BASE') . '/api/v1/user';
        $client = new Client;
        $options = [
            'headers' => [
                'Authorization' => $jwt_token
            ]
        ];
        $response = $client->get($userUrl, $options);
        $responseContent = $response->getBody()->getContents();

        return json_decode($responseContent, 1);
    }

    public static function getCustomerData(string $jwt_token)
    {
        try {
            $url = env('DF_API_BASE') . '/api/v2/customer_account';
            $client = new Client;
            $options = [
                'headers' => [
                    'Authorization' => $jwt_token
                ]
            ];
            $response = $client->get($url, $options);
            $responseContent = $response->getBody()->getContents();
            return json_decode($responseContent, 1);
        } catch(Exception $e){
            //return ['error' => 'CustomerAccount not found', 'message' => $e->getMessage()];
        return 1;
        }

    }
}
