# ksf_Marketing - Test Plan

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | TP-MARKETING-001 |
| **Module** | ksf_Marketing |
| **Project** | Marketing Automation & AI |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. Introduction

### 1.1 Purpose
This Test Plan defines testing strategy for the ksf_Marketing module including AI content generation, social media publishing, lead scoring, and campaign automation.

### 1.2 Scope
Testing covers all marketing automation features including unit tests for individual components and integration tests for external API interactions.

---

## 2. Test Strategy

### 2.1 Testing Levels

| Level | Focus | Tools | Coverage |
|-------|-------|-------|----------|
| Unit | Individual methods | PHPUnit | 90%+ |
| Integration | API clients | PHPUnit | 80%+ |
| Mock Testing | External APIs | PHPUnit mocks | 100% API calls |
| System | Complete workflows | Manual + PHPUnit | 100% |

### 2.2 Test Environment

| Component | Specification |
|-----------|---------------|
| PHP Version | 7.3+ |
| Mock HTTP | Symfony HTTP Client |
| Test Database | SQLite in-memory |
| API Mocks | Mock objects |

---

## 3. Test Scenarios

### 3.1 TC-001: AI Article Generation

**Test Case ID**: TC-001  
**Class**: `AIContentGenerator`  
**Method**: `generateArticle()`

#### Test Data
```php
$topic = 'Cloud Computing Benefits';
$tone = 'professional';
```

#### Test Steps
1. Instantiate `AIContentGenerator` with valid API key
2. Call `generateArticle($topic, $tone)`
3. Assert return structure has: title, content, excerpt, generated_at
4. Assert title matches topic (capitalized)
5. Assert content is non-empty string
6. Assert excerpt is <= 203 chars

#### Expected Results
```php
[
    'title' => 'Cloud Computing Benefits',
    'content' => '...', // Non-empty
    'excerpt' => '...', // <= 203 chars
    'generated_at' => '2024-01-15 10:30:00'
]
```

#### Alternative Flow: API Unavailable
1. Instantiate with empty API key
2. Call `generateArticle()`
3. Assert fallback content returned

#### Pass Criteria
- [ ] Return structure correct
- [ ] All required fields present
- [ ] Fallback content when API unavailable

---

### 3.2 TC-002: Ad Copy Generation

**Test Case ID**: TC-002  
**Class**: `AIContentGenerator`  
**Method**: `generateAd()`

#### Test Data
```php
$product = 'Cloud ERP System';
$platforms = ['facebook', 'instagram', 'linkedin', 'google'];
```

#### Test Steps
1. For each platform, call `generateAd($product, $platform)`
2. Assert return is string
3. Assert Facebook ad <= 200 chars
4. Assert Instagram ad <= 125 chars
5. Assert LinkedIn ad <= 3000 chars

#### Pass Criteria
- [ ] All platforms generate output
- [ ] Character limits respected
- [ ] Platform-specific prompts used

---

### 3.3 TC-003: Social Post Transformation

**Test Case ID**: TC-003  
**Class**: `AIContentGenerator`  
**Method**: `generateSocialPost()`

#### Test Data
```php
$content = 'Check out our amazing new product that will transform your business!';
```

#### Test Steps
1. Call `generateSocialPost($content, 'twitter')`
2. Assert result length <= 280
3. Call `generateSocialPost($content, 'linkedin')`
4. Assert result contains hashtags
5. Call `generateSocialPost($content, 'facebook')`
6. Assert result contains "Learn more"

#### Pass Criteria
- [ ] Twitter: 280 char limit enforced
- [ ] LinkedIn: Hashtags added
- [ ] Facebook: CTA added

---

### 3.4 TC-004: Twitter Publishing

**Test Case ID**: TC-004  
**Class**: `SocialMediaPublisher`  
**Method**: `publishToTwitter()`

#### Test Data
```php
$credentials = ['twitter_api_key' => 'valid_key'];
$content = 'Test tweet content';
```

#### Test Steps
1. Instantiate with valid credentials
2. Call `publishToTwitter($content)`
3. Assert boolean return
4. Test with invalid credentials
5. Assert false return

#### Mock Testing
- Mock HTTP client to return success response
- Assert correct endpoint called
- Assert correct payload sent

#### Pass Criteria
- [ ] Success returns true
- [ ] Failure returns false
- [ ] API endpoint correct
- [ ] Payload structure correct

---

### 3.5 TC-005: Lead Scoring Initialization

**Test Case ID**: TC-005  
**Class**: `LeadScoring`  
**Method**: Constructor

#### Test Data
```php
$contact_id = 123;
```

#### Test Steps
1. Create new `LeadScoring($contact_id)`
2. Assert score is 0
3. Assert stage is 'lead'
4. Assert points_breakdown is empty array

#### Pass Criteria
- [ ] New leads start with 0 score
- [ ] Stage is 'lead'
- [ ] No initial points breakdown

---

### 3.6 TC-006: Lead Scoring Points

**Test Case ID**: TC-006  
**Class**: `LeadScoring`  
**Method**: `addPoints()`

#### Test Data
```php
$contact_id = 123;
$points = 10;
$reason = 'form_submission';
```

#### Test Steps
1. Create `LeadScoring($contact_id)`
2. Call `addPoints($reason, $points)`
3. Assert score = 10
4. Call `addPoints('email_opened', 5)`
5. Assert score = 15
6. Assert points_breakdown has both entries

#### Stage Transitions
```php
// Test stage transitions
$scoring = new LeadScoring($contact_id);
$scoring->addPoints('form_submission', 10);      // stage: lead (10)
$scoring->addPoints('demo_requested', 20);    // stage: marketing_qualified (30)
$scoring->addPoints('quote_sent', 25);          // stage: sales_qualified (55)
$scoring->addPoints('negotiation', 30);          // stage: opportunity (85)
$scoring->addPoints('deal_won', 20);            // stage: customer (105)

assert($scoring->stage === 'customer');
assert($scoring->score === 105);
```

#### Pass Criteria
- [ ] Points added correctly
- [ ] Score accumulates
- [ ] Stage transitions at thresholds
- [ ] Points breakdown tracks reasons

---

### 3.7 TC-007: Drip Campaign Creation

**Test Case ID**: TC-007  
**Class**: `DripCampaign`  
**Method**: `addStep()`

#### Test Data
```php
$campaignData = [
    'name' => 'Welcome Series',
    'trigger' => 'signup'
];
$step = [
    'name' => 'Welcome Email',
    'delay_days' => 0,
    'subject' => 'Welcome!',
    'body' => 'Thank you for joining us.'
];
```

#### Test Steps
1. Create `DripCampaign($campaignData)`
2. Call `addStep($step)`
3. Assert steps array has 1 element
4. Assert step has order assigned

#### Pass Criteria
- [ ] Steps array populated
- [ ] Order assigned to step
- [ ] Step data preserved

---

### 3.8 TC-008: Drip Email Execution

**Test Case ID**: TC-008  
**Class**: `DripCampaign`  
**Method**: `executeForContact()`

#### Test Data
```php
$contact_id = 456;
$steps = [
    ['name' => 'Step 1', 'delay_days' => 0, 'subject' => 'Welcome'],
    ['name' => 'Step 2', 'delay_days' => 3, 'subject' => 'Features']
];
```

#### Test Steps
1. Create campaign with steps
2. Call `executeForContact($contact_id)`
3. Assert queue entries created
4. Assert scheduling calculated correctly

#### Pass Criteria
- [ ] Queue entries created
- [ ] Emails scheduled at correct times
- [ ] Contact ID associated

---

### 3.9 TC-009: Marketing Form Rendering

**Test Case ID**: TC-009  
**Class**: `MarketingForm`  
**Method**: `render()`

#### Test Data
```php
$formData = [
    'name' => 'Contact Us',
    'fields' => [
        ['name' => 'name', 'type' => 'text', 'label' => 'Name'],
        ['name' => 'email', 'type' => 'email', 'required' => true],
        ['name' => 'message', 'type' => 'textarea']
    ]
];
```

#### Test Steps
1. Create `MarketingForm($formData)`
2. Call `render()`
3. Assert HTML contains form tag
4. Assert HTML contains all field inputs
5. Assert required fields have required attribute
6. Assert select fields render correctly

#### Pass Criteria
- [ ] Valid HTML form generated
- [ ] All fields rendered
- [ ] Required attributes present
- [ ] Multiple field types handled

---

### 3.10 TC-010: Form Submission Processing

**Test Case ID**: TC-010  
**Class**: `MarketingForm`  
**Method**: `processSubmission()`

#### Test Data
```php
$submission = [
    'name' => 'John Smith',
    'email' => 'john@example.com',
    'phone' => '555-1234',
    'company' => 'Acme Corp'
];
```

#### Test Steps
1. Create form with name, email, phone fields
2. Call `processSubmission($submission)`
3. Assert success is true
4. Assert contact_id returned
5. Assert lead scoring initialized (10 points)

#### Pass Criteria
- [ ] Contact created
- [ ] All fields saved
- [ ] Source tracked
- [ ] Lead score initialized

---

### 3.11 TC-011: A/B Test Winner Determination

**Test Case ID**: TC-011  
**Class**: `ABTest`  
**Method**: `getWinner()`

#### Test Data
```php
$testData = [
    'name' => 'Subject Line Test',
    'type' => 'email_subject',
    'results' => [
        'variant_a' => ['views' => 100, 'conversions' => 5],
        'variant_b' => ['views' => 100, 'conversions' => 8]
    ]
];
```

#### Test Steps
1. Create ABTest with data
2. Call `getWinner()`
3. Assert 'variant_b' returned (8% vs 5%)
4. Test with tie (equal rates)
5. Assert null or first returned

#### Pass Criteria
- [ ] Highest conversion rate selected
- [ ] Tie handling defined
- [ ] Results based on rate not raw numbers

---

## 4. Database Test Fixtures

### 4.1 Test Tables

```sql
CREATE TABLE test_marketing_contacts (
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT UNIQUE,
    phone TEXT,
    company TEXT,
    source TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE test_marketing_lead_scores (
    id INTEGER PRIMARY KEY,
    contact_id INTEGER,
    score INTEGER DEFAULT 0,
    stage TEXT DEFAULT 'lead',
    points_breakdown TEXT,
    updated_at DATETIME,
    FOREIGN KEY (contact_id) REFERENCES test_marketing_contacts(id)
);

CREATE TABLE test_marketing_drip_queue (
    id INTEGER PRIMARY KEY,
    campaign_id INTEGER,
    contact_id INTEGER,
    subject TEXT,
    body TEXT,
    scheduled_at DATETIME,
    status TEXT DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    sent_at DATETIME,
    error TEXT
);
```

---

## 5. Test Execution Matrix

| Test Case | Class | Method | Priority | Status |
|-----------|-------|--------|----------|--------|
| TC-001 | AIContentGenerator | generateArticle | High | Pending |
| TC-002 | AIContentGenerator | generateAd | High | Pending |
| TC-003 | AIContentGenerator | generateSocialPost | High | Pending |
| TC-004 | SocialMediaPublisher | publishToTwitter | High | Pending |
| TC-005 | LeadScoring | __construct | High | Pending |
| TC-006 | LeadScoring | addPoints | High | Pending |
| TC-007 | DripCampaign | addStep | High | Pending |
| TC-008 | DripCampaign | executeForContact | High | Pending |
| TC-009 | MarketingForm | render | Medium | Pending |
| TC-010 | MarketingForm | processSubmission | Medium | Pending |
| TC-011 | ABTest | getWinner | Medium | Pending |

---

## 6. Sign-Off Criteria

| Criterion | Target | Weight |
|-----------|--------|--------|
| Test Coverage | 90%+ | 30% |
| Test Pass Rate | 100% | 40% |
| Critical Defects | 0 | 30% |

---

**Document Owner**: KS Fraser Development Team  
**Review Status**: Pending Approval