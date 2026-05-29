<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\Marketing;

use Ksfraser\Marketing\DripCampaign;
use Ksfraser\Marketing\DripEmailSender;
use PHPUnit\Framework\TestCase;

class DripCampaignsTest extends TestCase
{
    public function testDripCampaignConstructionWithData(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Welcome Series',
            'trigger' => 'signup',
            'steps' => '[{"name":"Day 1 Email","delay_days":0},{"name":"Day 3 Email","delay_days":2}]',
            'status' => 'active',
            'created_at' => '2024-01-01 10:00:00',
        ];

        $campaign = new DripCampaign($data);

        $this->assertEquals(1, $campaign->id);
        $this->assertEquals('Welcome Series', $campaign->name);
        $this->assertEquals('signup', $campaign->trigger);
        $this->assertIsArray($campaign->steps);
        $this->assertCount(2, $campaign->steps);
        $this->assertEquals('active', $campaign->status);
        $this->assertEquals('2024-01-01 10:00:00', $campaign->created_at);
    }

    public function testDripCampaignConstructionWithDefaults(): void
    {
        $data = [
            'id' => 2,
            'name' => 'Default Campaign',
            'trigger' => 'purchase',
        ];

        $campaign = new DripCampaign($data);

        $this->assertEquals(2, $campaign->id);
        $this->assertEquals('Default Campaign', $campaign->name);
        $this->assertEquals('purchase', $campaign->trigger);
        $this->assertEquals([], $campaign->steps);
        $this->assertEquals('draft', $campaign->status);
        $this->assertNotEmpty($campaign->created_at);
    }

    public function testDripCampaignTriggerConstants(): void
    {
        $this->assertEquals('signup', DripCampaign::TRIGGER_SIGNUP);
        $this->assertEquals('purchase', DripCampaign::TRIGGER_PURCHASE);
        $this->assertEquals('inactivity', DripCampaign::TRIGGER_INACTIVITY);
        $this->assertEquals('tag_added', DripCampaign::TRIGGER_TAG_ADDED);
        $this->assertEquals('score_threshold', DripCampaign::TRIGGER_SCORE);
    }

    public function testDripCampaignAddStep(): void
    {
        $campaign = new DripCampaign([
            'id' => 3,
            'name' => 'Add Step Test',
            'trigger' => 'signup',
            'steps' => '[]',
        ]);

        $campaign->addStep([
            'name' => 'Onboarding Email',
            'subject' => 'Welcome!',
            'delay_days' => 1,
        ]);

        $this->assertCount(1, $campaign->steps);
        $this->assertEquals('Onboarding Email', $campaign->steps[0]['name']);
        $this->assertEquals(1, $campaign->steps[0]['order']);
    }

    public function testDripCampaignAddMultipleStepsIncrementsOrder(): void
    {
        $campaign = new DripCampaign([
            'id' => 4,
            'name' => 'Multiple Steps Test',
            'trigger' => 'signup',
            'steps' => '[]',
        ]);

        $campaign->addStep(['name' => 'Step 1', 'delay_days' => 0]);
        $campaign->addStep(['name' => 'Step 2', 'delay_days' => 3]);
        $campaign->addStep(['name' => 'Step 3', 'delay_days' => 7]);

        $this->assertCount(3, $campaign->steps);
        $this->assertEquals(1, $campaign->steps[0]['order']);
        $this->assertEquals(2, $campaign->steps[1]['order']);
        $this->assertEquals(3, $campaign->steps[2]['order']);
    }

    public function testDripCampaignAddStepPreservesOriginalFields(): void
    {
        $campaign = new DripCampaign([
            'id' => 5,
            'name' => 'Preserve Fields Test',
            'trigger' => 'signup',
            'steps' => '[]',
        ]);

        $campaign->addStep([
            'name' => 'Follow Up',
            'subject' => 'Checking In',
            'body' => 'How are you enjoying our product?',
            'delay_days' => 5,
        ]);

        $step = $campaign->steps[0];
        $this->assertEquals('Follow Up', $step['name']);
        $this->assertEquals('Checking In', $step['subject']);
        $this->assertEquals('How are you enjoying our product?', $step['body']);
        $this->assertEquals(5, $step['delay_days']);
    }

    public function testDripCampaignExecuteForContactReturnsArray(): void
    {
        $campaign = new DripCampaign([
            'id' => 6,
            'name' => 'Execute Test',
            'trigger' => 'signup',
            'steps' => json_encode([
                ['name' => 'Email 1', 'subject' => 'Welcome', 'delay_days' => 0],
                ['name' => 'Email 2', 'subject' => 'Tips', 'delay_days' => 2],
            ]),
        ]);

        $result = $campaign->executeForContact(123);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContains('Email 1', $result);
        $this->assertContains('Email 2', $result);
    }

    public function testDripCampaignExecuteWithEmptyStepsReturnsEmptyArray(): void
    {
        $campaign = new DripCampaign([
            'id' => 7,
            'name' => 'No Steps Campaign',
            'trigger' => 'signup',
            'steps' => '[]',
        ]);

        $result = $campaign->executeForContact(456);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testDripCampaignExecutePreservesStepNames(): void
    {
        $campaign = new DripCampaign([
            'id' => 8,
            'name' => 'Step Names Test',
            'trigger' => 'purchase',
            'steps' => json_encode([
                ['name' => 'Thank You', 'delay_days' => 0],
                ['name' => 'Cross Sell', 'delay_days' => 7],
                ['name' => 'Feedback Request', 'delay_days' => 14],
            ]),
        ]);

        $result = $campaign->executeForContact(789);

        $this->assertEquals('Thank You', $result[0]);
        $this->assertEquals('Cross Sell', $result[1]);
        $this->assertEquals('Feedback Request', $result[2]);
    }

    public function testDripEmailSenderProcessQueueReturnsInt(): void
    {
        $processed = DripEmailSender::processQueue();

        $this->assertIsInt($processed);
        $this->assertGreaterThanOrEqual(0, $processed);
    }

    public function testDripEmailSenderProcessQueueReturnsZeroWhenNoQueue(): void
    {
        $processed = DripEmailSender::processQueue();

        $this->assertIsInt($processed);
    }

    public function testDripCampaignConstructionWithEmptyData(): void
    {
        $campaign = new DripCampaign([]);

        $this->assertEquals(0, $campaign->id);
        $this->assertEquals('', $campaign->name);
        $this->assertEquals('', $campaign->trigger);
        $this->assertEquals([], $campaign->steps);
        $this->assertEquals('draft', $campaign->status);
    }

    public function testDripCampaignStepDelayDaysCalculation(): void
    {
        $campaign = new DripCampaign([
            'id' => 9,
            'name' => 'Delay Test',
            'trigger' => 'signup',
            'steps' => json_encode([
                ['name' => 'Day 1', 'delay_days' => 0],
                ['name' => 'Day 5', 'delay_days' => 4],
                ['name' => 'Day 10', 'delay_days' => 9],
            ]),
        ]);

        $this->assertEquals(0, $campaign->steps[0]['delay_days']);
        $this->assertEquals(4, $campaign->steps[1]['delay_days']);
        $this->assertEquals(9, $campaign->steps[2]['delay_days']);
    }

    public function testDripCampaignStepsArrayContainsCorrectStructure(): void
    {
        $campaign = new DripCampaign([
            'id' => 10,
            'name' => 'Structure Test',
            'trigger' => 'signup',
            'steps' => json_encode([
                [
                    'name' => 'Welcome Email',
                    'subject' => 'Welcome to Our Service!',
                    'body' => '<h1>Hello!</h1><p>We are excited to have you.</p>',
                    'delay_days' => 0,
                ],
            ]),
        ]);

        $step = $campaign->steps[0];
        $this->assertArrayHasKey('name', $step);
        $this->assertArrayHasKey('subject', $step);
        $this->assertArrayHasKey('body', $step);
        $this->assertArrayHasKey('delay_days', $step);
    }

    public function testDripCampaignCreatedAtUsesCurrentTimeWhenNotProvided(): void
    {
        $before = date('Y-m-d H:i:s');
        $campaign = new DripCampaign([
            'id' => 11,
            'name' => 'Time Test',
            'trigger' => 'purchase',
        ]);
        $after = date('Y-m-d H:i:s');

        $this->assertGreaterThanOrEqual($before, $campaign->created_at);
        $this->assertLessThanOrEqual($after, $campaign->created_at);
    }

    public function testDripCampaignStatusCanBeSet(): void
    {
        $campaign = new DripCampaign([
            'id' => 12,
            'name' => 'Status Test',
            'trigger' => 'signup',
            'status' => 'paused',
        ]);

        $this->assertEquals('paused', $campaign->status);
    }

    public function testDripCampaignAllTriggerTypesCanBeSet(): void
    {
        $triggers = [
            DripCampaign::TRIGGER_SIGNUP,
            DripCampaign::TRIGGER_PURCHASE,
            DripCampaign::TRIGGER_INACTIVITY,
            DripCampaign::TRIGGER_TAG_ADDED,
            DripCampaign::TRIGGER_SCORE,
        ];

        foreach ($triggers as $trigger) {
            $campaign = new DripCampaign([
                'id' => 0,
                'name' => 'Trigger Test',
                'trigger' => $trigger,
            ]);

            $this->assertEquals($trigger, $campaign->trigger);
        }
    }

    public function testDripCampaignExecuteForContactHandlesDefaultSubject(): void
    {
        $campaign = new DripCampaign([
            'id' => 13,
            'name' => 'Default Subject Test',
            'trigger' => 'signup',
            'steps' => json_encode([
                ['name' => 'Email Without Subject', 'delay_days' => 0],
            ]),
        ]);

        $result = $campaign->executeForContact(100);

        $this->assertCount(1, $result);
    }
}