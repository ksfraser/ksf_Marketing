<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\Marketing;

use Ksfraser\Marketing\ABTest;
use Ksfraser\Marketing\LeadScoring;
use Ksfraser\Marketing\MarketingForm;
use PHPUnit\Framework\TestCase;

class LeadScoringTest extends TestCase
{
    public function testLeadScoringConstruction(): void
    {
        $scoring = new LeadScoring(1);

        $this->assertEquals(1, $scoring->contact_id);
        $this->assertIsInt($scoring->score);
        $this->assertIsString($scoring->stage);
        $this->assertIsArray($scoring->points_breakdown);
    }

    public function testLeadScoringStageConstants(): void
    {
        $this->assertEquals('lead', LeadScoring::STAGE_LEAD);
        $this->assertEquals('marketing_qualified', LeadScoring::STAGE_MQL);
        $this->assertEquals('sales_qualified', LeadScoring::STAGE_SQL);
        $this->assertEquals('opportunity', LeadScoring::STAGE_OPPORTUNITY);
        $this->assertEquals('customer', LeadScoring::STAGE_CUSTOMER);
    }

    public function testLeadScoringAddPoints(): void
    {
        $scoring = new LeadScoring(2);
        $scoring->score = 0;
        $scoring->stage = LeadScoring::STAGE_LEAD;
        $scoring->points_breakdown = [];

        $scoring->addPoints('form_submission', 10);

        $this->assertEquals(10, $scoring->score);
        $this->assertArrayHasKey('form_submission', $scoring->points_breakdown);
        $this->assertEquals(10, $scoring->points_breakdown['form_submission']);
    }

    public function testLeadScoringSubtractPoints(): void
    {
        $scoring = new LeadScoring(3);
        $scoring->score = 20;
        $scoring->stage = LeadScoring::STAGE_MQL;
        $scoring->points_breakdown = ['form_submission' => 10];

        $scoring->subtractPoints('form_submission', 5);

        $this->assertEquals(15, $scoring->score);
        $this->assertEquals(5, $scoring->points_breakdown['form_submission']);
    }

    public function testLeadScoringAddPointsUpdatesStage(): void
    {
        $scoring = new LeadScoring(4);
        $scoring->score = 0;
        $scoring->stage = LeadScoring::STAGE_LEAD;
        $scoring->points_breakdown = [];

        $scoring->addPoints('engagement', 30);

        $this->assertEquals(30, $scoring->score);
    }

    public function testLeadScoringGetRecommendationsReturnsArray(): void
    {
        $scoring = new LeadScoring(5);
        $scoring->score = 15;
        $scoring->stage = LeadScoring::STAGE_MQL;
        $scoring->points_breakdown = [];

        $recommendations = $scoring->getRecommendations();

        $this->assertIsArray($recommendations);
        $this->assertArrayHasKey('score', $recommendations);
        $this->assertArrayHasKey('stage', $recommendations);
        $this->assertArrayHasKey('actions', $recommendations);
        $this->assertIsArray($recommendations['actions']);
    }

    public function testLeadScoringGetRecommendationsActionsVaryByStage(): void
    {
        $stages = [
            LeadScoring::STAGE_LEAD => 'Send welcome email',
            LeadScoring::STAGE_MQL => 'Send product info',
            LeadScoring::STAGE_SQL => 'Send quote',
            LeadScoring::STAGE_OPPORTUNITY => 'Send proposal',
            LeadScoring::STAGE_CUSTOMER => 'Cross-sell',
        ];

        foreach ($stages as $stage => $expectedAction) {
            $scoring = new LeadScoring(100);
            $scoring->score = 0;
            $scoring->stage = $stage;
            $scoring->points_breakdown = [];

            $recommendations = $scoring->getRecommendations();

            $this->assertContains($expectedAction, $recommendations['actions']);
        }
    }

    public function testMarketingFormConstructionWithData(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Contact Form',
            'html_content' => '<form>...</form>',
            'css_content' => '.form { }',
            'fields' => '[{"name":"email","type":"email"}]',
            'submit_action' => 'api',
            'submit_url' => '/api/contact',
            'options' => '{"submit_text":"Send"}',
        ];

        $form = new MarketingForm($data);

        $this->assertEquals(1, $form->id);
        $this->assertEquals('Contact Form', $form->name);
        $this->assertEquals('<form>...</form>', $form->html_content);
        $this->assertEquals('.form { }', $form->css_content);
        $this->assertIsArray($form->fields);
        $this->assertEquals('api', $form->submit_action);
        $this->assertEquals('/api/contact', $form->submit_url);
        $this->assertIsArray($form->options);
    }

    public function testMarketingFormConstructionWithDefaults(): void
    {
        $data = [
            'id' => 2,
            'name' => 'Simple Form',
        ];

        $form = new MarketingForm($data);

        $this->assertEquals(2, $form->id);
        $this->assertEquals('Simple Form', $form->name);
        $this->assertEquals('', $form->html_content);
        $this->assertEquals('', $form->css_content);
        $this->assertEquals([], $form->fields);
        $this->assertEquals('api', $form->submit_action);
        $this->assertEquals('', $form->submit_url);
        $this->assertEquals([], $form->options);
    }

    public function testMarketingFormRenderReturnsString(): void
    {
        $form = new MarketingForm([
            'id' => 3,
            'name' => 'Test Form',
            'fields' => '[{"name":"name","label":"Name","type":"text","required":true}]',
        ]);

        $html = $form->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('ksf-marketing-form', $html);
        $this->assertStringContainsString('data-form-id="3"', $html);
    }

    public function testMarketingFormRenderContainsFields(): void
    {
        $form = new MarketingForm([
            'id' => 4,
            'name' => 'Fields Form',
            'fields' => json_encode([
                ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text'],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ]),
        ]);

        $html = $form->render();

        $this->assertStringContainsString('first_name', $html);
        $this->assertStringContainsString('email', $html);
        $this->assertStringContainsString('First Name', $html);
        $this->assertStringContainsString('Email', $html);
    }

    public function testMarketingFormRenderWithSelectField(): void
    {
        $form = new MarketingForm([
            'id' => 5,
            'name' => 'Select Form',
            'fields' => json_encode([
                [
                    'name' => 'country',
                    'label' => 'Country',
                    'type' => 'select',
                    'options' => ['USA', 'Canada', 'UK'],
                ],
            ]),
        ]);

        $html = $form->render();

        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('USA', $html);
        $this->assertStringContainsString('Canada', $html);
    }

    public function testMarketingFormRenderWithTextareaField(): void
    {
        $form = new MarketingForm([
            'id' => 6,
            'name' => 'Textarea Form',
            'fields' => json_encode([
                ['name' => 'message', 'label' => 'Message', 'type' => 'textarea'],
            ]),
        ]);

        $html = $form->render();

        $this->assertStringContainsString('<textarea', $html);
        $this->assertStringContainsString('message', $html);
    }

    public function testMarketingFormRenderWithCustomSubmitText(): void
    {
        $form = new MarketingForm([
            'id' => 7,
            'name' => 'Custom Submit Form',
            'options' => json_encode(['submit_text' => 'Get Started']),
        ]);

        $html = $form->render();

        $this->assertStringContainsString('Get Started', $html);
    }

    public function testMarketingFormRenderIncludesCssContent(): void
    {
        $form = new MarketingForm([
            'id' => 8,
            'name' => 'Styled Form',
            'css_content' => '.custom-style { color: red; }',
        ]);

        $html = $form->render();

        $this->assertStringContainsString('<style>', $html);
        $this->assertStringContainsString('.custom-style', $html);
    }

    public function testABTestConstructionWithData(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Subject Line Test',
            'type' => 'email_subject',
            'variants' => '["A","B","C"]',
            'results' => '{"A":{"views":100,"conversions":5}}',
            'status' => 'active',
            'start_date' => '2024-01-01',
            'end_date' => '2024-02-01',
        ];

        $test = new ABTest($data);

        $this->assertEquals(1, $test->id);
        $this->assertEquals('Subject Line Test', $test->name);
        $this->assertEquals('email_subject', $test->type);
        $this->assertEquals(['A', 'B', 'C'], $test->variants);
        $this->assertIsArray($test->results);
        $this->assertEquals('active', $test->status);
        $this->assertEquals('2024-01-01', $test->start_date);
        $this->assertEquals('2024-02-01', $test->end_date);
    }

    public function testABTestConstructionWithDefaults(): void
    {
        $data = [
            'id' => 2,
            'name' => 'Default Test',
            'type' => 'email_content',
            'start_date' => '2024-01-01',
        ];

        $test = new ABTest($data);

        $this->assertEquals(2, $test->id);
        $this->assertEquals([], $test->variants);
        $this->assertEquals([], $test->results);
        $this->assertEquals('draft', $test->status);
        $this->assertNull($test->end_date);
    }

    public function testABTestTypeConstants(): void
    {
        $this->assertEquals('email_subject', ABTest::TYPE_EMAIL_SUBJECT);
        $this->assertEquals('email_content', ABTest::TYPE_EMAIL_CONTENT);
        $this->assertEquals('landing_page', ABTest::TYPE_LANDING_PAGE);
        $this->assertEquals('ad', ABTest::TYPE_AD);
    }

    public function testABTestRecordConversion(): void
    {
        $test = new ABTest([
            'id' => 3,
            'name' => 'Conversion Test',
            'type' => 'email_subject',
            'start_date' => '2024-01-01',
            'results' => '{"A":{"views":100,"conversions":5}}',
        ]);

        $test->recordConversion('A');

        $this->assertEquals(6, $test->results['A']['conversions']);
    }

    public function testABTestRecordConversionForNewVariant(): void
    {
        $test = new ABTest([
            'id' => 4,
            'name' => 'New Variant Test',
            'type' => 'email_subject',
            'start_date' => '2024-01-01',
            'results' => '{}',
        ]);

        $test->recordConversion('B');

        $this->assertArrayHasKey('B', $test->results);
        $this->assertEquals(1, $test->results['B']['conversions']);
        $this->assertEquals(0, $test->results['B']['views']);
    }

    public function testABTestRecordView(): void
    {
        $test = new ABTest([
            'id' => 5,
            'name' => 'View Test',
            'type' => 'email_subject',
            'start_date' => '2024-01-01',
            'results' => '{"A":{"views":100,"conversions":5}}',
        ]);

        $test->recordView('A');

        $this->assertEquals(101, $test->results['A']['views']);
    }

    public function testABTestRecordViewForNewVariant(): void
    {
        $test = new ABTest([
            'id' => 6,
            'name' => 'New View Variant Test',
            'type' => 'email_subject',
            'start_date' => '2024-01-01',
            'results' => '{}',
        ]);

        $test->recordView('C');

        $this->assertArrayHasKey('C', $test->results);
        $this->assertEquals(1, $test->results['C']['views']);
        $this->assertEquals(0, $test->results['C']['conversions']);
    }

    public function testABTestGetWinnerReturnsVariantId(): void
    {
        $test = new ABTest([
            'id' => 7,
            'name' => 'Winner Test',
            'type' => 'email_subject',
            'start_date' => '2024-01-01',
            'results' => [
                'A' => ['views' => 100, 'conversions' => 10],
                'B' => ['views' => 100, 'conversions' => 15],
                'C' => ['views' => 100, 'conversions' => 5],
            ],
        ]);

        $winner = $test->getWinner();

        $this->assertEquals('B', $winner);
    }

    public function testABTestGetWinnerReturnsNullWithNoViews(): void
    {
        $test = new ABTest([
            'id' => 8,
            'name' => 'No Views Test',
            'type' => 'email_subject',
            'start_date' => '2024-01-01',
            'results' => [
                'A' => ['views' => 0, 'conversions' => 0],
                'B' => ['views' => 0, 'conversions' => 0],
            ],
        ]);

        $winner = $test->getWinner();

        $this->assertNull($winner);
    }

    public function testABTestGetWinnerReturnsNullWithEmptyResults(): void
    {
        $test = new ABTest([
            'id' => 9,
            'name' => 'Empty Results Test',
            'type' => 'email_subject',
            'start_date' => '2024-01-01',
            'results' => [],
        ]);

        $winner = $test->getWinner();

        $this->assertNull($winner);
    }

    public function testMarketingFormProcessSubmissionReturnsArray(): void
    {
        $form = new MarketingForm([
            'id' => 10,
            'name' => 'Process Form',
        ]);

        $result = $form->processSubmission([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }
}