<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use App\Http\Traits\ApiResponseTrait;
use Carbon\Carbon;

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
        if (empty(env('GITHUB_PERSONAL_ACCESS_TOKEN')))
            return $this->errorResponse('403', 'Token Required');

        $headers = [
            'Authorization' => "Bearer " . env('GITHUB_PERSONAL_ACCESS_TOKEN')
        ];

        $response = Http::withToken(env('GITHUB_PERSONAL_ACCESS_TOKEN'))->acceptJson()->get('https://api.github.com/search/repositories?q=stars:>0&per_page=100&order=Desc');

        if (!$response->ok()) return $this->errorResponse($response->status(), $response->collect()->has('message') ? $response->json()['message'] : 'Not Found');      // return a success response
        
        $responseToSend = [];

        $items = $response->collect()->sortByDesc('stargazers_count')['items'];
        $responses = Http::pool(function (Pool $pool) use($items, $responseToSend) {
            $contributionRes = [];
            foreach ($items as $item) {
                $pool->as($item['id'])->withToken(env('GITHUB_PERSONAL_ACCESS_TOKEN'))->acceptJson()->get($item['contributors_url'] . '?per_page=1');
            }
        });

        foreach ($items as $item) {
            $contributionRes = [];
            if ($responses[$item['id']]->ok()) {
                $contributionRes = [
                    'login' => $responses[$item['id']]->json()[0]['login'],
                    'id' => $responses[$item['id']]->json()[0]['id'],
                    'contributions' => $responses[$item['id']]->json()[0]['contributions'],
                ];
            }

            $tempArr = [];
            $tempArr = collect([
                'id'=> $item['id'],
                'name'=> $item['name'],
                'owner'=> $item['owner'],
                'html_url'=> $item['html_url'],
                'contributor'=> $contributionRes,
                'created_at'=> Carbon::parse($item['created_at'])->isoFormat('MMMM Do YYYY, h:mm:ss a')        //June 15th 2018, 5:34:15 pm
            ]);

            array_push($responseToSend, $tempArr);
        }

        unset($response, $res, $responses);             // unset the variables to free space
        return $this->successResponse(200, $responseToSend);        // return a success response
    }
}
