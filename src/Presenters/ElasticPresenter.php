<?php

declare(strict_types=1);

namespace App\Presenters;

use Redis;

final class ElasticPresenter extends BasePresenter
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        // 1. vytvořit index - viz soubor s commandem

        // curl -X POST -u elastic:test http://localhost:9200/_bulk --data-binary @michelin.txt -H "Content-Type: application/json"


        // na tuto stránku se dostanu localhost:80/elastic


        // čas z elasticu: 0.014842987060546875
        // čas z redisu: 0.001107931137084961
        // redis je 13,4x rychlejší


        $redisKey = "elasticsearch:query:" . md5(json_encode([
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ])); // Generujeme klíč na základě dotazu
        $redisHost = 'redis'; // Docker název služby
        $redisPort = 6379;

        $redis = new Redis();
        $redis->connect($redisHost, $redisPort);

        // Elasticsearch dotaz
        $url = 'http://elasticsearch:9200/michelin/_search';
        $query = [
            'query' => [
                'match_all' => new \stdClass(),
            ],
            'size' => 100, // Volitelně nastavte velikost výsledků
        ];
        $jsonQuery = json_encode($query);

        // --- Načtení z Redis ---
        $redisStartTime = microtime(true); // Začátek měření času pro Redis
        $cachedData = $redis->get($redisKey); // Načtení dat z Redis
        $redisEndTime = microtime(true); // Konec měření času pro Redis
        $redisResponseTime = $redisEndTime - $redisStartTime;

        if ($cachedData) {
            // Data nalezena v Redis
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse([
                'source' => 'redis',
                'response_time' => $redisResponseTime,
                'data' => json_decode($cachedData, true),
            ]));
            return;
        }

        // --- Načtení z Elasticsearch ---
        $elasticStartTime = microtime(true); // Začátek měření času pro Elasticsearch

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonQuery);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonQuery),
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, 'elastic:test');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $elasticEndTime = microtime(true); // Konec měření času pro Elasticsearch
        $elasticResponseTime = $elasticEndTime - $elasticStartTime;

        curl_close($ch);

        if ($httpCode !== 200) {
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse([
                'status' => 'error',
                'message' => 'Failed to fetch data from Elasticsearch',
                'http_code' => $httpCode,
                'response' => json_decode($response, true),
            ]));
            return;
        }

        // Data získána z Elasticsearch, uložení do Redis
        $redis->set($redisKey, $response);

        // Vrácení odpovědi
        $this->sendResponse(new \Nette\Application\Responses\JsonResponse([
            'source' => 'elasticsearch',
            'response_time' => $elasticResponseTime,
            'data' => json_decode($response, true),
        ]));

    }

    public function actionGetAll(): void
    {
        $url = 'http://elasticsearch:9200/michelinnew/_search';
        $query = [
            'query' => [
                'match_all' => new \stdClass(),
            ],
//            'size' => 100,
        ];

        $jsonQuery = json_encode($query);



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonQuery);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonQuery),
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, 'elastic:test');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        if (curl_errno($ch)) {

            $this->sendResponse(new \Nette\Application\Responses\JsonResponse([
                'status' => 'error',
                'message' => curl_error($ch),
            ]));
            curl_close($ch);
        }

        $this->sendResponse(new \Nette\Application\Responses\JsonResponse([
            'status' => $httpCode,
            'response' => json_decode($response, true),
        ]));
    }
}