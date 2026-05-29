<?php

namespace Ksfraser\Marketing;

class AIContentGenerator
{
    private string $api_key;
    private string $model;

    public function __construct(string $api_key = '', string $model = 'gpt-4')
    {
        $this->api_key = $api_key ?: get_option('ksf_ai_api_key', '');
        $this->model = $model;
    }

    public function generateArticle(string $topic, string $tone = 'professional'): array
    {
        $prompt = "Write a marketing article about {$topic} in a {$tone} tone. 
            Include: Introduction, 3-5 key points, and conclusion.
            Make it engaging for business readers.";

        $content = $this->callAI($prompt);

        return [
            'title' => ucwords($topic),
            'content' => $content,
            'excerpt' => substr($content, 0, 200) . '...',
            'generated_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function generateAd(string $product, string $platform): string
    {
        $prompts = [
            'facebook' => "Write a catchy Facebook ad for {$product}. Max 200 chars. Include CTA.",
            'instagram' => "Write an Instagram caption for {$product}. Max 125 chars. Add hashtags.",
            'linkedin' => "Write a professional LinkedIn post about {$product}. Max 3000 chars.",
            'google' => "Write a Google Ads headline and description for {$product}.",
        ];

        $prompt = $prompts[$platform] ?? "Write marketing copy for {$product}";
        
        return $this->callAI($prompt);
    }

    public function generateSocialPost(string $content, string $platform): string
    {
        $transforms = [
            'twitter' => fn($c) => substr($c, 0, 280),
            'linkedin' => fn($c) => $c . "\n\n#business #marketing",
            'facebook' => fn($c) => $c . "\n\nLearn more at our website!",
        ];

        $transform = $transforms[$platform] ?? fn($c) => $c;

        return $transform($content);
    }

    public function improveContent(string $content, string $goal): string
    {
        $prompt = "Improve this content to {$goal}:\n\n{$content}";
        
        return $this->callAI($prompt);
    }

    public function generateEmailCampaign(string $subject, string $template): array
    {
        $prompts = [
            'welcome' => "Write a welcome email with subject: {$subject}",
            'promotional' => "Write a promotional email with subject: {$subject}",
            'newsletter' => "Write a newsletter email with subject: {$subject}",
            'followup' => "Write a follow-up email with subject: {$subject}",
        ];

        $prompt = $prompts[$template] ?? "Write an email with subject: {$subject}";
        
        return [
            'subject' => $subject,
            'body' => $this->callAI($prompt),
            'template' => $template,
        ];
    }

    private function callAI(string $prompt): string
    {
        if (empty($this->api_key)) {
            return $this->fallbackGenerate($prompt);
        }

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => 2000,
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        
        return $data['choices'][0]['message']['content'] ?? $this->fallbackGenerate($prompt);
    }

    private function fallbackGenerate(string $prompt): string
    {
        return "AI-generated content for: " . substr($prompt, 0, 50) . "...\n\n" .
               "This is a placeholder. Configure your AI API key in settings for real content generation.";
    }
}

class SocialMediaPublisher
{
    private array $credentials;

    public function __construct(array $credentials = [])
    {
        $this->credentials = $credentials;
    }

    public function publishToTwitter(string $content, string $media_path = ''): bool
    {
        if (empty($this->credentials['twitter_api_key'])) {
            return false;
        }

        $ch = curl_init('https://api.twitter.com/2/tweets');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->credentials['twitter_api_key'],
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $content]));

        $response = curl_exec($ch);
        curl_close($ch);

        return !empty(json_decode($response, true)['data']['id']);
    }

    public function publishToLinkedIn(string $content, string $title, string $media_path = ''): bool
    {
        if (empty($this->credentials['linkedin_access_token'])) {
            return false;
        }

        $post = [
            'author' => 'urn:li:organization:' . $this->credentials['linkedin_org_id'],
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.UgcPostCreate' => [
                    'content' => [
                        'com.linkedin.UgcPostCreate$article' => [
                            'title' => $title,
                            'articleUrl' => $content,
                        ],
                    ],
                ],
            ],
            ];

        $ch = curl_init('https://api.linkedin.com/v2/ugcPosts');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->credentials['linkedin_access_token'],
            'Content-Type: => application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

        $response = curl_exec($ch);
        curl_close($ch);

        return !empty(json_decode($response, true)['id']);
    }

    public function schedulePost(string $content, string $schedule_time, string $platform): array
    {
        if (!function_exists('db_query')) {
            return [
                'id' => 0,
                'scheduled_for' => $schedule_time,
                'status' => 'unavailable',
                'message' => 'Database not available'
            ];
        }

        $id = db_insert(TB_PREF . 'marketing_scheduled_posts');
        
        $sql = "INSERT INTO " . TB_PREF . "marketing_scheduled_posts
            (id, content, schedule_time, platform, status, created_at)
            VALUES (
                " . db_escape($id) . ",
                " . db_escape($content) . ",
                " . db_escape($schedule_time) . ",
                " . db_escape($platform) . ",
                'scheduled',
                NOW()
            )";

        @db_query($sql);

        return ['id' => $id, 'scheduled_for' => $schedule_time];
    }
}

class MarketingCampaign
{
    private static ?bool $faAvailable = null;

    private static function checkFaAvailable(): bool
    {
        if (self::$faAvailable !== null) {
            return self::$faAvailable;
        }
        self::$faAvailable = function_exists('db_query') && defined('TB_PREF') && is_readable(__DIR__ . '/../../../includes/db.inc');
        return self::$faAvailable;
    }

    public int $id;
    public string $name;
    public string $type;
    public string $status;
    public array $channels;
    public int $leads_generated;
    public float $conversion_rate;
    public string $start_date;
    public ?string $end_date;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->name = $data['name'] ?? '';
            $this->type = $data['type'] ?? '';
            $this->status = $data['status'] ?? 'draft';
            $channelsData = $data['channels'] ?? null;
            $this->channels = is_array($channelsData) 
                ? $channelsData 
                : (is_string($channelsData) ? json_decode($channelsData, true) ?? [] : []);
            $this->leads_generated = $data['leads_generated'] ?? 0;
            $this->conversion_rate = $data['conversion_rate'] ?? 0;
            $this->start_date = $data['start_date'] ?? '';
            $this->end_date = $data['end_date'] ?? null;
        }
    }

    public function trackConversion(): bool
    {
        $this->leads_generated++;
        return true;
    }

    public static function getActive(): array
    {
        if (!self::checkFaAvailable()) {
            return [];
        }

        $sql = "SELECT * FROM " . TB_PREF . "marketing_campaigns 
            WHERE status = 'active'
            AND (end_date IS NULL OR end_date >= CURDATE())";

        $result = @db_query($sql);
        if (!$result) {
            return [];
        }

        $campaigns = [];
        while ($row = @db_fetch_assoc($result)) {
            $campaigns[] = new self($row);
        }

        return $campaigns;
    }
}