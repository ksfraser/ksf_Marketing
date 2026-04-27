<?php

function ksf_marketing_register_shortcodes()
{
    add_shortcode('ksf_ai_article', function($atts) {
        $atts = shortcode_atts(['topic' => '', 'tone' => 'professional'], $atts);
        
        if (empty($atts['topic'])) {
            return '<p>Please specify topic attribute</p>';
        }
        
        $generator = new \Ksfraser\Marketing\AIContentGenerator();
        $article = $generator->generateArticle($atts['topic'], $atts['tone']);
        
        ob_start();
        
        ?>
        <article class="ksf-ai-article">
            <h2><?php echo esc_html($article['title']); ?></h2>
            <div class="article-content"><?php echo nl2br(esc_html($article['content'])); ?></div>
            <p class="article-meta">
                <small>AI Generated - <?php echo esc_html($article['generated_at']); ?></small>
            </p>
        </article>
        <?php
        
        return ob_get_clean();
    });
    
    add_shortcode('ksf_social_post', function($atts) {
        $atts = shortcode_atts(['platform' => 'twitter', 'content' => ''], $atts);
        
        if (empty($atts['content'])) {
            return '<p>Please specify content attribute</p>';
        }
        
        $publisher = new \Ksfraser\Marketing\SocialMediaPublisher([
            'twitter_api_key' => get_option('ksf_twitter_api_key'),
            'linkedin_access_token' => get_option('ksf_linkedin_token'),
        ]);
        
        $transformed = $publisher->generateSocialPost($atts['content'], $atts['platform']);
        
        return '<div class="ksf-social-preview"><pre>' . esc_html($transformed) . '</pre></div>';
    });
    
    add_shortcode('ksf_campaign_list', function() {
        $campaigns = \Ksfraser\Marketing\MarketingCampaign::getActive();
        
        if (empty($campaigns)) {
            return '<p>No active campaigns</p>';
        }
        
        ob_start();
        
        ?>
        <div class="ksf-campaigns">
            <?php foreach ($campaigns as $campaign): ?>
            <div class="campaign-card">
                <h3><?php echo esc_html($campaign->name); ?></h3>
                <p>Type: <?php echo esc_html($campaign->type); ?></p>
                <p>Leads: <?php echo esc_html($campaign->leads_generated); ?></p>
                <p>Conversion: <?php echo esc_html($campaign->conversion_rate); ?>%</p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        
        return ob_get_clean();
    });
}

add_action('init', 'ksf_marketing_register_shortcodes');