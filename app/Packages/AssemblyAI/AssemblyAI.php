<?php

namespace App\Packages\AssemblyAI;

use App\Packages\AssemblyAI\Exceptions\GetTranscriptionRequestException;
use App\Packages\AssemblyAI\Exceptions\GetTranscriptionSubtitlesRequestException;
use App\Packages\AssemblyAI\Exceptions\TranscribeRequestException;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssemblyAI
{
    protected $client;
    protected $defaultBody;

    public function __construct()
    {
        $this->client = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
            ->withToken(config('assemblyai.token'), 'Bearer')
            ->baseUrl('https://api.assemblyai.com/v2')
            ->timeout(90);
    }

    public function transcribe($fileUrl, $meta = [])
    {
        $urlParams = count($meta) > 0 ? '?' . http_build_query($meta) : '';
        $params = [
            'audio_url' => $fileUrl,
            'webhook_url' => config('assemblyai.webhook_url') . $urlParams,
            'webhook_auth_header_name' => 'Authorization',
            'webhook_auth_header_value' => 'Bearer ' . config('assemblyai.token'),
            'speaker_labels' => true,
            'filter_profanity' => false,
            'content_safety' => false,
            'language_code' => $meta['language'] ?? 'en'
        ];

        if ($meta['speakers_expected'] ?? false) {
            $params['speakers_expected'] = $meta['speakers_expected'];
        }

        try {
            $response = $this->client
                ->post('/transcript' . $urlParams, $params);

            if ($response->failed()) {
                return $response->throw();
            }

            if ($response->successful()) {
                return json_decode($response->body(), true);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new TranscribeRequestException($e->getMessage());
        }
    }

    public function getTranscription($transcriptionId)
    {
        try {
            $response = $this->client->get('/transcript/' . $transcriptionId);

            if ($response->failed()) {
                return $response->throw();
            }

            if ($response->successful()) {
                return json_decode($response->body(), true);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new GetTranscriptionRequestException($e->getMessage());
        }
    }

    public function getTranscriptionSubtitles($transcriptionId)
    {
        try {
            $baseUrl = '/transcript/' . $transcriptionId;
            $vttResponse = $this->client->get($baseUrl . '/vtt');
            $srtResponse = $this->client->get($baseUrl . '/srt');

            $results = [];

            if ($vttResponse->successful()) {
                $results['vtt'] = $vttResponse->body();
            }

            if ($srtResponse->successful()) {
                $results['srt'] = $srtResponse->body();
            }

            return $results;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new GetTranscriptionSubtitlesRequestException($e->getMessage());
        }
    }
}
