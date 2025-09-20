<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI;

class OpenaiService
{
    private $apiKey;
    private $openAiClient;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_KEY');
        $this->openAiClient = OpenAI::client($this->apiKey);
    }

    public function getFlashcardsContentFromOpenai(string $extractedText): array
    {

        $generatorPrompt = <<<PROMPT
                You are a flashcard generator. 
                From the following text, extract the most important terms and definitions.
                Text:
                $extractedText
                PROMPT;

        $response = $this->openAiClient->responses()->create([
            'model' => 'gpt-4o',
            'input' =>  $generatorPrompt,
            // 'input' =>  "Give me sample biology terms and their definitions",
            'temperature' => 0.7,
            'max_output_tokens' => 500,
            'text' => [
                'format' => [
                    'name' => 'flashcard',
                    'strict' => true,
                    'type' => 'json_schema',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'flashcards' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'term' => ['type' => 'string'],
                                        'definition' => ['type' => 'string'],
                                    ],
                                    'required' => ['term', 'definition'],
                                    'additionalProperties' => false,
                                ],
                            ],
                        ],
                        'required' => ['flashcards'],
                        'additionalProperties' => false,
                    ],
                ]
            ],
        ]);

        $extractedText = json_decode($response->outputText, true)['flashcards'];

        return $extractedText;
    }
}
