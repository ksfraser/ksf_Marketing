# ksf_Marketing - Automation & AI

## Features
- AI-powered content generation (GPT integration)
- Social media publishing (Twitter, LinkedIn)
- Campaign tracking
- Lead scoring
- Drip email sequences

## Installation
```bash
composer require ksfraser/ksf_marketing
```

## Usage
```php
use Ksfraser\Marketing\AIContentGenerator;

$ai = new AIContentGenerator($api_key);

// Generate article
$article = $ai->generateArticle('Cloud Computing Benefits', 'professional');
echo $article['content'];

// Generate social posts
$twitter_post = $ai->generateSocialPost($content, 'twitter');
$linkedin_post = $ai->generateSocialPost($content, 'linkedin');

// Schedule social post
use Ksfraser\Marketing\SocialMediaPublisher;

$pub = new SocialMediaPublisher(['twitter_api_key' => '...']);
$pub->schedulePost($content, '2024-12-25 10:00', 'twitter');

// Track campaign
use Ksfraser\Marketing\MarketingCampaign;

$campaign->trackConversion();
```

## Shortcodes
- `[ksf_ai_article topic="Cloud Computing"]` - AI-generated article
- `[ksf_social_post platform="twitter" content="..."]` - Social preview
- `[ksf_campaign_list]` - Active campaigns

## Mautic Integration (Roadmap)
- [ ] Email tracking pixels
- [ ] Form embedding
- [ ] A/B testing engine
- [ ] Drip campaign builder
- [ ] SMS notifications
- [ ] Push notifications
- [ ] Point-based lead scoring
- [ ] Contact segmentation

## Requirements
- WordPress for WP integration
- OpenAI API key for AI features

## Roadmap
1. Add Mautic-style form builder
2. Implement A/B testing
3. Add SMS gateway integration
4. Add push notification support