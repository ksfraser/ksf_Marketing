<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\Marketing;

use Ksfraser\Marketing\AIContentGenerator;
use Ksfraser\Marketing\MarketingCampaign;
use Ksfraser\Marketing\SocialMediaPublisher;
use PHPUnit\Framework\TestCase;

class AIMarketingTest extends TestCase
{
    public function testAIContentGeneratorConstruction(): void
    {
        $generator = new AIContentGenerator('test-api-key', 'gpt-3.5');

        $this->assertInstanceOf(AIContentGenerator::class, $generator);
    }

    public function testAIContentGeneratorWithEmptyApiKey(): void
    {
        $generator = new AIContentGenerator('', 'gpt-4');

        $this->assertInstanceOf(AIContentGenerator::class, $generator);
    }

    public function testGenerateArticleReturnsArray(): void
    {
        $generator = new AIContentGenerator('');
        $article = $generator->generateArticle('marketing automation');

        $this->assertIsArray($article);
        $this->assertArrayHasKey('title', $article);
        $this->assertArrayHasKey('content', $article);
        $this->assertArrayHasKey('excerpt', $article);
        $this->assertArrayHasKey('generated_at', $article);
    }

    public function testGenerateArticleTitleIsFormatted(): void
    {
        $generator = new AIContentGenerator('');
        $article = $generator->generateArticle('email marketing');

        $this->assertEquals('Email Marketing', $article['title']);
    }

    public function testGenerateArticleContainsContent(): void
    {
        $generator = new AIContentGenerator('');
        $article = $generator->generateArticle('test topic');

        $this->assertNotEmpty($article['content']);
        $this->assertIsString($article['content']);
    }

    public function testGenerateArticleWithCustomTone(): void
    {
        $generator = new AIContentGenerator('');
        $article = $generator->generateArticle('social media', 'friendly');

        $this->assertIsArray($article);
    }

    public function testGenerateAdReturnsString(): void
    {
        $generator = new AIContentGenerator('');
        $ad = $generator->generateAd('Product X', 'facebook');

        $this->assertIsString($ad);
        $this->assertNotEmpty($ad);
    }

    public function testGenerateAdForFacebook(): void
    {
        $generator = new AIContentGenerator('');
        $ad = $generator->generateAd('Product Y', 'facebook');

        $this->assertNotEmpty($ad);
    }

    public function testGenerateAdForInstagram(): void
    {
        $generator = new AIContentGenerator('');
        $ad = $generator->generateAd('Product Z', 'instagram');

        $this->assertNotEmpty($ad);
    }

    public function testGenerateAdForLinkedIn(): void
    {
        $generator = new AIContentGenerator('');
        $ad = $generator->generateAd('Enterprise Product', 'linkedin');

        $this->assertNotEmpty($ad);
    }

    public function testGenerateAdForGoogle(): void
    {
        $generator = new AIContentGenerator('');
        $ad = $generator->generateAd('Search Product', 'google');

        $this->assertNotEmpty($ad);
    }

    public function testGenerateAdForUnknownPlatform(): void
    {
        $generator = new AIContentGenerator('');
        $ad = $generator->generateAd('Generic Product', 'unknown');

        $this->assertNotEmpty($ad);
    }

    public function testGenerateSocialPostForTwitter(): void
    {
        $generator = new AIContentGenerator('');
        $post = $generator->generateSocialPost('This is a test message', 'twitter');

        $this->assertIsString($post);
        $this->assertLessThanOrEqual(280, strlen($post));
    }

    public function testGenerateSocialPostForLinkedIn(): void
    {
        $generator = new AIContentGenerator('');
        $post = $generator->generateSocialPost('Business update', 'linkedin');

        $this->assertStringContainsString('#business', $post);
        $this->assertStringContainsString('#marketing', $post);
    }

    public function testGenerateSocialPostForFacebook(): void
    {
        $generator = new AIContentGenerator('');
        $post = $generator->generateSocialPost('Check out our new product', 'facebook');

        $this->assertStringContainsString('Learn more at our website', $post);
    }

    public function testGenerateSocialPostForUnknownPlatform(): void
    {
        $generator = new AIContentGenerator('');
        $post = $generator->generateSocialPost('Original content', 'unknown');

        $this->assertEquals('Original content', $post);
    }

    public function testImproveContentReturnsString(): void
    {
        $generator = new AIContentGenerator('');
        $improved = $generator->improveContent('Original content', 'be more engaging');

        $this->assertIsString($improved);
        $this->assertNotEmpty($improved);
    }

    public function testGenerateEmailCampaignReturnsArray(): void
    {
        $generator = new AIContentGenerator('');
        $email = $generator->generateEmailCampaign('Welcome!', 'welcome');

        $this->assertIsArray($email);
        $this->assertArrayHasKey('subject', $email);
        $this->assertArrayHasKey('body', $email);
        $this->assertArrayHasKey('template', $email);
        $this->assertEquals('Welcome!', $email['subject']);
        $this->assertEquals('welcome', $email['template']);
    }

    public function testGenerateEmailCampaignForPromotional(): void
    {
        $generator = new AIContentGenerator('');
        $email = $generator->generateEmailCampaign('50% Off!', 'promotional');

        $this->assertEquals('promotional', $email['template']);
    }

    public function testGenerateEmailCampaignForNewsletter(): void
    {
        $generator = new AIContentGenerator('');
        $email = $generator->generateEmailCampaign('Monthly Update', 'newsletter');

        $this->assertEquals('newsletter', $email['template']);
    }

    public function testGenerateEmailCampaignForFollowup(): void
    {
        $generator = new AIContentGenerator('');
        $email = $generator->generateEmailCampaign('Checking In', 'followup');

        $this->assertEquals('followup', $email['template']);
    }

    public function testGenerateEmailCampaignForUnknownTemplate(): void
    {
        $generator = new AIContentGenerator('');
        $email = $generator->generateEmailCampaign('Generic Subject', 'unknown');

        $this->assertArrayHasKey('subject', $email);
        $this->assertArrayHasKey('body', $email);
    }

    public function testSocialMediaPublisherConstruction(): void
    {
        $publisher = new SocialMediaPublisher([]);

        $this->assertInstanceOf(SocialMediaPublisher::class, $publisher);
    }

    public function testSocialMediaPublisherWithCredentials(): void
    {
        $credentials = [
            'twitter_api_key' => 'test-key',
            'linkedin_access_token' => 'test-token',
        ];

        $publisher = new SocialMediaPublisher($credentials);

        $this->assertInstanceOf(SocialMediaPublisher::class, $publisher);
    }

    public function testPublishToTwitterWithoutCredentialsReturnsFalse(): void
    {
        $publisher = new SocialMediaPublisher([]);
        $result = $publisher->publishToTwitter('Test tweet');

        $this->assertFalse($result);
    }

    public function testPublishToLinkedInWithoutCredentialsReturnsFalse(): void
    {
        $publisher = new SocialMediaPublisher([]);
        $result = $publisher->publishToLinkedIn('Content', 'Title');

        $this->assertFalse($result);
    }

    public function testSchedulePostReturnsArray(): void
    {
        $publisher = new SocialMediaPublisher([]);
        $result = $publisher->schedulePost('Test content', '2024-12-01 10:00:00', 'twitter');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('scheduled_for', $result);
    }

    public function testMarketingCampaignConstructionWithData(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Summer Sale',
            'type' => 'promotional',
            'status' => 'active',
            'channels' => '["email","social"]',
            'leads_generated' => 100,
            'conversion_rate' => 5.5,
            'start_date' => '2024-06-01',
            'end_date' => '2024-08-31',
        ];

        $campaign = new MarketingCampaign($data);

        $this->assertEquals(1, $campaign->id);
        $this->assertEquals('Summer Sale', $campaign->name);
        $this->assertEquals('promotional', $campaign->type);
        $this->assertEquals('active', $campaign->status);
        $this->assertEquals(['email', 'social'], $campaign->channels);
        $this->assertEquals(100, $campaign->leads_generated);
        $this->assertEquals(5.5, $campaign->conversion_rate);
        $this->assertEquals('2024-06-01', $campaign->start_date);
        $this->assertEquals('2024-08-31', $campaign->end_date);
    }

    public function testMarketingCampaignConstructionWithDefaults(): void
    {
        $data = [
            'name' => 'Test Campaign',
            'type' => 'email',
            'start_date' => '2024-01-01',
        ];

        $campaign = new MarketingCampaign($data);

        $this->assertEquals(0, $campaign->id);
        $this->assertEquals('Test Campaign', $campaign->name);
        $this->assertEquals('email', $campaign->type);
        $this->assertEquals('draft', $campaign->status);
        $this->assertEquals([], $campaign->channels);
        $this->assertEquals(0, $campaign->leads_generated);
        $this->assertEquals(0, $campaign->conversion_rate);
        $this->assertNull($campaign->end_date);
    }

    public function testMarketingCampaignTrackConversionIncrementsLeads(): void
    {
        $campaign = new MarketingCampaign([
            'name' => 'Test',
            'type' => 'email',
            'start_date' => '2024-01-01',
            'leads_generated' => 10,
        ]);

        $campaign->trackConversion();

        $this->assertEquals(11, $campaign->leads_generated);
    }

    public function testMarketingCampaignTrackConversionReturnsTrue(): void
    {
        $campaign = new MarketingCampaign([
            'name' => 'Test',
            'type' => 'email',
            'start_date' => '2024-01-01',
        ]);

        $result = $campaign->trackConversion();

        $this->assertTrue($result);
    }
}