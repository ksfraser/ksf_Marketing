# ksf_Marketing - Functional Requirements

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | FRD-MARKETING-001 |
| **Module** | ksf_Marketing |
| **Project** | Marketing Automation & AI |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. Introduction

### 1.1 Purpose
This document defines detailed functional requirements for the ksf_Marketing module covering AI-powered content generation, social media publishing, lead scoring, drip campaigns, and A/B testing.

### 1.2 Scope
Functional requirements cover all marketing automation features including external API integrations, database operations, and user-facing functionality.

---

## 2. Functional Requirements

### 2.1 AI Content Generation

#### FR-001: Generate Marketing Article
| Requirement ID | FR-001 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | Medium |

**Description**: Generate structured marketing articles using AI.

**Functional Specification**:
- Accept topic string and tone parameter
- Construct prompt for OpenAI API
- Return structured array with title, content, excerpt
- Include generation timestamp

**Parameters**:
| Parameter | Type | Required | Default |
|-----------|------|----------|---------|
| topic | string | Yes | - |
| tone | string | No | 'professional' |

**Tone Options**: professional, casual, technical, friendly

**Business Rules**:
- Topic must not be empty
- Tone must be valid option
- API key must be configured or return fallback

**Return Structure**:
```php
[
    'title' => string,      // "Cloud Computing Benefits"
    'content' => string,   // Full article HTML/text
    'excerpt' => string,   // First 200 chars + "..."
    'generated_at' => string // "2024-01-15 10:30:00"
]
```

---

#### FR-002: Generate Advertisement Copy
| Requirement ID | FR-002 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | Low |

**Description**: Generate platform-specific advertisement copy.

**Functional Specification**:
- Accept product name and target platform
- Generate copy with character limits per platform
- Include CTA where appropriate

**Platforms**:
| Platform | Max Characters | Features |
|----------|--------------|----------|
| facebook | 200 | CTA included |
| instagram | 125 | Hashtags added |
| linkedin | 3000 | Professional tone |
| google | N/A | Headlines + description |

---

#### FR-003: Generate Social Post
| Requirement ID | FR-003 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | Low |

**Description**: Transform content for social media platforms.

**Functional Specification**:
- Accept base content and target platform
- Apply platform-specific transformations:
  - Twitter: Truncate to 280 chars
  - LinkedIn: Add business hashtags
  - Facebook: Add engagement CTA

**Transformations**:
```php
[
    'twitter' => fn($c) => substr($c, 0, 280),
    'linkedin' => fn($c) => $c . "\n\n#business #marketing",
    'facebook' => fn($c) => $c . "\n\nLearn more at our website!",
]
```

---

#### FR-004: Generate Email Campaign
| Requirement ID | FR-004 |
|----------------|--------|
| **Priority** | Medium |
| **Complexity** | Medium |

**Description**: Generate email campaign content.

**Template Types**:
| Template | Purpose | AI Prompt Focus |
|----------|---------|----------------|
| welcome | New subscriber greeting | Warm, informative |
| promotional | Sales offers | Compelling, urgent |
| newsletter | Regular updates | Informative, engaging |
| followup | Nurture sequence | Personalized, helpful |

**Return Structure**:
```php
[
    'subject' => string,
    'body' => string,  // HTML content
    'template' => string
]
```

---

### 2.2 Social Media Publishing

#### FR-005: Publish to Twitter
| Requirement ID | FR-005 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | Medium |

**Description**: Publish content directly to Twitter.

**Functional Specification**:
- Authenticate using Twitter API v2
- Post tweet with optional media
- Return success/failure status

**Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| content | string | Yes |
| media_path | string | No |

**Business Rules**:
- Content max 280 characters
- Media must be image file
- API key must be configured

**API Call**:
```http
POST https://api.twitter.com/2/tweets
Authorization: Bearer {twitter_api_key}
Content-Type: application/json

{"text": "Tweet content"}
```

---

#### FR-006: Publish to LinkedIn
| Requirement ID | FR-006 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | Medium |

**Description**: Publish content to LinkedIn organization.

**Functional Specification**:
- Authenticate using LinkedIn API
- Create UGC post for organization
- Support article and link posts

**API Call**:
```http
POST https://api.linkedin.com/v2/ugcPosts
Authorization: Bearer {linkedin_access_token}
Content-Type: application/json

{
    "author": "urn:li:organization:{org_id}",
    "lifecycleState": "PUBLISHED",
    "specificContent": {
        "com.linkedin.UgcPostCreate": {
            "content": {
                "com.linkedin.UgcPostCreate$article": {
                    "title": "Post Title",
                    "articleUrl": "https://..."
                }
            }
        }
    }
}
```

---

#### FR-007: Schedule Social Post
| Requirement ID | FR-007 |
|----------------|--------|
| **Priority** | Medium |
| **Complexity** | Low |

**Description**: Schedule a post for future publishing.

**Functional Specification**:
- Store post in database with schedule time
- Set status to 'scheduled'
- Return scheduled post ID

**Database Record**:
```sql
INSERT INTO 0_marketing_scheduled_posts 
    (content, schedule_time, platform, status, created_at)
VALUES (?, ?, ?, 'scheduled', NOW())
```

---

### 2.3 Lead Management

#### FR-008: Lead Scoring Engine
| Requirement ID | FR-008 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | Medium |

**Description**: Score leads based on engagement and actions.

**Stage Progression**:
| Stage | Code | Score Range | Threshold |
|-------|------|------------|-----------|
| Lead | lead | 0-24 | Entry point |
| Marketing Qualified | mql | 25-49 | +25 points |
| Sales Qualified | sql | 50-74 | +50 points |
| Opportunity | opportunity | 75-99 | +75 points |
| Customer | customer | 100+ | +100 points |

**Points Breakdown**:
| Action | Points | Reason Code |
|--------|--------|-------------|
| Form submission | 10 | form_submission |
| Email open | 5 | email_opened |
| Link click | 10 | link_clicked |
| Demo request | 25 | demo_requested |
| Pricing page view | 15 | pricing_viewed |

**Recommendation Actions**:
```php
[
    'lead' => ['Send welcome email', 'Add to nurture campaign'],
    'marketing_qualified' => ['Send product info', 'Schedule demo'],
    'sales_qualified' => ['Send quote', 'Schedule meeting'],
    'opportunity' => ['Send proposal', 'Follow up call'],
    'customer' => ['Cross-sell', 'Request referral'],
]
```

---

#### FR-009: Marketing Form Processing
| Requirement ID | FR-009 |
|----------------|--------|
| **Priority** | Medium |
| **Complexity** | Medium |

**Description**: Process marketing form submissions.

**Form Configuration**:
| Field | Type | Description |
|-------|------|-------------|
| name | text | Contact name |
| email | email | Contact email (required) |
| phone | tel | Phone number |
| company | text | Company name |

**Processing Flow**:
1. Validate required fields (email)
2. Create contact record in database
3. Initialize lead scoring (10 points for form submission)
4. Return success with contact ID

---

### 2.4 Campaign Automation

#### FR-010: Drip Campaign Execution
| Requirement ID | FR-010 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | High |

**Description**: Execute multi-step email drip campaigns.

**Trigger Types**:
| Trigger | Code | Description |
|---------|------|-------------|
| Signup | signup | New contact created |
| Purchase | purchase | Order completed |
| Inactivity | inactivity | No engagement for X days |
| Tag Added | tag_added | Specific tag assigned |
| Score Threshold | score_threshold | Score reaches level |

**Campaign Steps**:
```php
[
    [
        'name' => 'Welcome Email',
        'delay_days' => 0,
        'subject' => 'Welcome to our platform',
        'body' => 'Thank you for signing up...'
    ],
    [
        'name' => 'Feature Introduction',
        'delay_days' => 3,
        'subject' => 'Getting Started',
        'body' => 'Here are our key features...'
    ],
    [
        'name' => 'Follow-up',
        'delay_days' => 7,
        'subject' => 'Have questions?',
        'body' => 'Let us know how we can help...'
    ]
]
```

**Queue Management**:
- Schedule emails based on delay_days
- Process queue in batches of 50
- Mark as sent/failed after processing

---

#### FR-011: Campaign Tracking
| Requirement ID | FR-011 |
|----------------|--------|
| **Priority** | Medium |
| **Complexity** | Low |

**Description**: Track campaign metrics and conversions.

**Metrics Tracked**:
| Metric | Type | Calculation |
|--------|------|-------------|
| leads_generated | INT | COUNT of new contacts |
| conversion_rate | DECIMAL | leads_generated / views |

**Active Campaign Filter**:
- Status = 'active'
- End date is NULL OR >= current date

---

#### FR-012: A/B Testing
| Requirement ID | FR-012 |
|----------------|--------|
| **Priority** | Medium |
| **Complexity** | Medium |

**Description**: Create and analyze A/B tests.

**Test Types**:
| Type | Code | Description |
|------|------|-------------|
| Email Subject | email_subject | Different subject lines |
| Email Content | email_content | Different email bodies |
| Landing Page | landing_page | Different page layouts |
| Advertisement | ad | Different ad copy |

**Variant Results Structure**:
```php
[
    'variant_a' => ['views' => 100, 'conversions' => 5],
    'variant_b' => ['views' => 100, 'conversions' => 8]
]
```

**Winner Determination**:
- Calculate conversion rate for each variant
- Select variant with highest rate
- Require minimum 100 views per variant

---

## 3. Data Requirements

### 3.1 Input Data

| Feature | Input Fields | Validation |
|---------|-------------|------------|
| AI Article | topic, tone | topic required, tone in [professional, casual, technical] |
| Social Post | content, platform | content required, platform in [twitter, linkedin, facebook] |
| Lead Form | name, email, phone, company | email required, valid format |
| Drip Campaign | name, trigger, steps | name required, valid trigger, steps array |
| A/B Test | name, type, variants | name required, valid type, variants array |

### 3.2 Output Data

| Feature | Output | Format |
|---------|--------|--------|
| AI Article | {title, content, excerpt, generated_at} | JSON array |
| Social Publish | {success: bool} | Boolean |
| Scheduled Post | {id, scheduled_for} | JSON array |
| Lead Score | {score, stage, actions} | JSON array |
| Drip Queue | {sent: int} | Integer count |

---

## 4. API Specifications

### 4.1 OpenAI API

**Endpoint**: `https://api.openai.com/v1/chat/completions`

**Request**:
```json
{
    "model": "gpt-4",
    "messages": [
        {
            "role": "user",
            "content": "Prompt text..."
        }
    ],
    "max_tokens": 2000
}
```

**Response**:
```json
{
    "choices": [
        {
            "message": {
                "content": "Generated text..."
            }
        }
    ]
}
```

### 4.2 Twitter API v2

**Endpoint**: `https://api.twitter.com/2/tweets`

**Request**:
```json
{
    "text": "Tweet content"
}
```

### 4.3 LinkedIn API v2

**Endpoint**: `https://api.linkedin.com/v2/ugcPosts`

---

## 5. Business Rules Summary

| Rule ID | Description |
|---------|-------------|
| BR-001 | AI content requires valid OpenAI API key |
| BR-002 | Twitter posts limited to 280 characters |
| BR-003 | LinkedIn posts limited to 3000 characters |
| BR-004 | Lead score threshold: 25 for MQL |
| BR-005 | Lead score threshold: 50 for SQL |
| BR-006 | Drip emails processed in batches of 50 |
| BR-007 | A/B test requires minimum 100 views per variant |
| BR-008 | Social credentials required for publishing |

---

## 6. Non-Functional Requirements

### 6.1 Performance
| Metric | Target |
|--------|--------|
| AI generation | < 10 seconds |
| Social publish | < 5 seconds |
| Lead scoring | < 100ms |
| Drip queue processing | < 30 seconds per batch |

### 6.2 Availability
- External APIs: Subject to third-party availability
- Local features: 99.9% uptime

### 6.3 Scalability
- Support 10,000+ contacts
- Process 1,000+ drip emails per day
- Handle 100+ concurrent AI requests

---

## 7. Requirements Traceability

| Requirement ID | Use Case | Test Case | Status |
|---------------|----------|-----------|--------|
| FR-001 | UC-001 | TC-001 | Pending |
| FR-002 | UC-002 | TC-002 | Pending |
| FR-003 | UC-003 | TC-003 | Pending |
| FR-004 | UC-004 | TC-004 | Pending |
| FR-005 | UC-005 | TC-005 | Pending |
| FR-006 | UC-006 | TC-006 | Pending |
| FR-007 | UC-007 | TC-007 | Pending |
| FR-008 | UC-008 | TC-008 | Pending |
| FR-009 | UC-009 | TC-009 | Pending |
| FR-010 | UC-010 | TC-010 | Pending |
| FR-011 | UC-011 | TC-011 | Pending |
| FR-012 | UC-012 | TC-012 | Pending |

---

**Document Owner**: KS Fraser Development Team  
**Review Status**: Pending Approval