<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
class ApiNewsService
{
    private HttpClientInterface $client;
    private string $apiKey;
    public function __construct(string $apiKey, HttpClientInterface $client){
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    /**
     * @return array
     */
    public function getNews(){
        $response = $this->client->request('GET', 'https://newsapi.org/v2/everything/',[
            'query' => [
                'q' => "santé",
                'language' => "fr",
                'searchin' => "title",
                'apiKey' => $this->apiKey,
                'pageSize' => 6
            ],
        ]);

        $info = $response->toArray();

        return $info;
    }

}
