<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
// use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use App\Http\Traits\ApiResponseTrait;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;

class ApiController extends Controller
{
    use ApiResponseTrait;
    /**
    * get Top Repos
    *
    * @param  mixed $request
    * @return void
    */
    public function mostStarredRepos(Request $request)
    {
        $responseToSend = [];
        $client = new Client();

        $headers = [
            'Authorization' => "Bearer " . env('GITHUB_PERSONAL_ACCESS_TOKEN')
        ];

        try {
            $res = $client->get('https://api.github.com/search/repositories?q=stars:>0&per_page=100&order=Desc', [ 'headers' => $headers ]);
            // $res = $client->request('GET', 'https://api.github.com/repos/freeCodeCamp/freeCodeCamp/contributors?per_page=1', [ 'headers' => $headers ]);
            $response = json_decode($res->getBody(), true);
            $response = collect($response['items'])->sortByDesc('stargazers_count');
            foreach ($response as $key => $value) {
                try {
                    $res = $client->get($value['contributors_url'] . '?per_page=1', [ 'headers' => $headers ]);
                    $contributionRes = json_decode($res->getBody());
                    $contributionRes = [
                        'login' => $contributionRes[0]->login,
                        'id' => $contributionRes[0]->id,
                        'contributions' => $contributionRes[0]->contributions,
                    ];

                    $tempArr = $this->pushToResponseToSend($responseToSend, $value, $contributionRes);      // common function to load the temp variable
                    array_push($responseToSend, $tempArr);

                } catch (\Throwable $th) {
                    // If we get any exception then load contributor array as blank
                    $tempArr = $this->pushToResponseToSend($responseToSend, $value, []);
                    array_push($responseToSend, $tempArr);
                }
            }
    
            unset($response, $res);             // unset the variables to free space
            return $this->successResponse(200, $responseToSend);        // return a success response
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getResponse()->getStatusCode(), $th->getResponse()->getReasonPhrase());        // return a success response
        }
    }

    public function pushToResponseToSend($responseToSend, $value, $contributionRes)
    {
        $tempArr = [];
        $tempArr = collect([
            'id'=> $value['id'],
            'name'=> $value['name'],
            'owner'=> $value['owner'],
            'html_url'=> $value['html_url'],
            'contributor'=> $contributionRes,
            'created_at'=> Carbon::parse($value['created_at'])->isoFormat('MMMM Do YYYY, h:mm:ss a')        //June 15th 2018, 5:34:15 pm
        ]);
        return $tempArr;
    }
}
