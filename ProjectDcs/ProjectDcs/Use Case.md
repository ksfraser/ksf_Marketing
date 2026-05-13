# ksf_Marketing - Use Case Specification

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | UCD-MARKETING-001 |
| **Module** | ksf_Marketing |
| **Project** | Marketing Automation & AI |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. Use Case Overview

### 1.1 Actor Definitions

| Actor | Description | Access |
|-------|-------------|--------|
| **Marketing Manager** | Creates campaigns, configures AI | Full access |
| **Content Creator** | Generates content, manages forms | AI + Forms |
| **Social Media Manager** | Publishes to social platforms | Social only |
| **Sales Rep** | Views leads, manages scoring | Lead view |
| **System** | Automated processes | Background jobs |

### 1.2 Use Case Summary

| UC ID | Use Case Name | Actor | Priority |
|-------|---------------|-------|----------|
| UC-001 | Generate AI Article | Content Creator | High |
| UC-002 | Generate Ad Copy | Marketing Manager | High |
| UC-003 | Publish Social Post | Social Media Manager | High |
| UC-004 | Schedule Social Post | Social Media Manager | Medium |
| UC-005 | Submit Marketing Form | System/User | High |
| UC-006 | View Lead Score | Sales Rep | High |
| UC-007 | Create Drip Campaign | Marketing Manager | High |
| UC-008 | Process Drip Queue | System | High |
| UC-009 | Create A/B Test | Marketing Manager | Medium |
| UC-010 | View Campaign Analytics | Marketing Manager | Medium |

---

## 2. Use Case Details

### 2.1 UC-001: Generate AI Article

**Primary Actor**: Content Creator  
**Priority**: High  
**Pre-condition**: OpenAI API key configured

#### Description
Content creator generates a marketing article using AI assistance.

#### Pre-Conditions
1. OpenAI API key is configured
2. User has access to AI generation feature
3. Topic for article is known

#### Basic Flow
```
1. Content Creator accesses article generator
2. User enters article topic: "Cloud Computing Benefits"
3. User selects tone: "professional"
4. User clicks "Generate" button
5. System constructs AI prompt
6. System calls OpenAI API
7. System receives generated content
8. System formats response (title, content, excerpt)
9. System displays generated article
```

#### Alternative Flows

**AF-001: API Unavailable**
```
6a.1. OpenAI API returns error or timeout
6a.2. System logs error
6a.3. System returns fallback placeholder content
6a.4. Display message: "AI unavailable - using placeholder"
```

**AF-002: Invalid Topic**
```
3a.1. User submits empty topic
3a.2. System displays validation error
3a.3. User enters valid topic
```

#### Post-Conditions
- Article content generated and displayed
- Content ready for editing/publishing

---

### 2.2 UC-002: Generate Ad Copy

**Primary Actor**: Marketing Manager  
**Priority**: High  
**Pre-condition**: OpenAI API key configured

#### Description
Generate platform-specific advertisement copy.

#### Pre-Conditions
1. OpenAI API key configured
2. Product/service identified

#### Basic Flow
```
1. Marketing Manager accesses ad generator
2. User enters product name
3. User selects target platform
4. User clicks "Generate Ad"
5. System calls AI with platform-specific prompt
6. System receives ad copy
7. System displays copy with character count
```

#### Platform-Specific Prompts
- Facebook: "Write a catchy Facebook ad for {product}. Max 200 chars. Include CTA."
- Instagram: "Write an Instagram caption for {product}. Max 125 chars. Add hashtags."
- LinkedIn: "Write a professional LinkedIn post about {product}. Max 3000 chars."
- Google: "Write a Google Ads headline and description for {product}."

#### Post-Conditions
- Ad copy generated with correct character limits
- Copy ready for campaign use

---

### 2.3 UC-003: Publish Social Post

**Primary Actor**: Social Media Manager  
**Priority**: High  
**Pre-condition**: Platform credentials configured

#### Description
Publish content directly to social media platforms.

#### Pre-Conditions
1. Platform API credentials configured
2. Content prepared
3. User has publishing permissions

#### Basic Flow
```
1. User navigates to social publishing
2. User enters post content
3. User selects platform (Twitter/LinkedIn)
4. User clicks "Publish Now"
5. System authenticates with platform API
6. System posts content
7. System receives confirmation
8. System displays success message
```

#### Alternative Flows

**AF-003: Character Limit Exceeded**
```
3a.1. Content exceeds platform limit
3a.2. System warns user
3a.3. User shortens content or uses transformation
```

**AF-004: Authentication Failed**
```
5a.1. API returns 401 Unauthorized
5a.2. System displays error: "Please check API credentials"
5a.3. Content NOT posted
```

#### Post-Conditions
- Post published to selected platform
- Post ID returned for tracking

---

### 2.4 UC-004: Schedule Social Post

**Primary Actor**: Social Media Manager  
**Priority**: Medium  
**Pre-condition**: Content prepared

#### Description
Schedule a social post for future publishing.

#### Basic Flow
```
1. User enters post content
2. User selects platform
3. User selects schedule date/time
4. User clicks "Schedule"
5. System validates content and time
6. System stores post in database
7. System confirms scheduling
```

#### Post-Conditions
- Post stored with 'scheduled' status
- Post will be published at scheduled time (via cron)

---

### 2.5 UC-005: Submit Marketing Form

**Primary Actor**: Website Visitor / System  
**Priority**: High  
**Pre-condition**: Marketing form embedded on site

#### Description
Process a submission from a marketing capture form.

#### Pre-Conditions
1. Marketing form is embedded
2. Form fields configured
3. Submission endpoint accessible

#### Basic Flow
```
1. Visitor fills out form (name, email, company)
2. Visitor clicks "Submit"
3. System validates required fields
4. System creates contact record
5. System initializes lead score (10 points)
6. System triggers drip campaign if configured
7. System displays thank you message
```

#### Post-Conditions
- Contact created in database
- Lead scoring initialized
- Drip campaign triggered (if signup trigger configured)

---

### 2.6 UC-006: View Lead Score

**Primary Actor**: Sales Rep  
**Priority**: High  
**Pre-condition**: Contact exists with score

#### Description
View lead score and recommendations for a contact.

#### Basic Flow
```
1. Sales Rep accesses lead management
2. User selects contact
3. System loads lead score
4. System displays:
   - Current score
   - Stage (Lead, MQL, SQL, Opportunity, Customer)
   - Points breakdown
   - Recommended next actions
```

#### Stage Determination Logic
```php
$this->stage = match(true) {
    $this->score >= 100 => 'customer',
    $this->score >= 75 => 'opportunity',
    $this->score >= 50 => 'sales_qualified',
    $this->score >= 25 => 'marketing_qualified',
    default => 'lead',
};
```

#### Post-Conditions
- Lead qualification status visible
- Sales rep can take recommended action

---

### 2.7 UC-007: Create Drip Campaign

**Primary Actor**: Marketing Manager  
**Priority**: High  
**Pre-condition**: Drip campaign feature enabled

#### Description
Create a multi-step email drip campaign.

#### Basic Flow
```
1. Marketing Manager accesses drip campaign builder
2. User enters campaign name
3. User selects trigger type
4. User adds campaign steps
5. For each step:
   a. User enters step name
   b. User sets delay (days)
   c. User enters email subject
   d. User enters email body
6. User clicks "Save Campaign"
7. System validates campaign structure
8. System saves campaign
9. System confirms creation
```

#### Step Configuration
```php
[
    'name' => 'Step 1: Welcome',
    'delay_days' => 0,
    'subject' => 'Welcome!',
    'body' => 'Thank you for joining...'
]
```

#### Post-Conditions
- Campaign created with status 'draft'
- Campaign can be activated
- Steps queued when triggered

---

### 2.8 UC-008: Process Drip Queue

**Primary Actor**: System (Scheduled Job)  
**Priority**: High  
**Pre-condition**: Drip queue has pending items

#### Description
Process pending emails from drip campaign queue.

#### Basic Flow
```
1. System scheduler triggers drip processor
2. System queries pending emails (LIMIT 50)
3. For each pending email:
   a. System looks up contact email
   b. System sends email via SMTP
   c. If sent: Update status to 'sent'
   d. If failed: Update status to 'failed'
4. System logs processing results
```

#### Batch Processing
- Process up to 50 emails per run
- Run every 5 minutes via cron
- Continue processing if individual email fails

#### Post-Conditions
- Emails sent or marked as failed
- Queue updated with statuses
- Processing logged for audit

---

### 2.9 UC-009: Create A/B Test

**Primary Actor**: Marketing Manager  
**Priority**: Medium  
**Pre-condition**: Test feature enabled

#### Description
Create an A/B test for optimization.

#### Basic Flow
```
1. Marketing Manager accesses A/B testing
2. User enters test name
3. User selects test type
4. User creates variants:
   a. Variant A: Control version
   b. Variant B: Test version
5. User sets test duration
6. User clicks "Start Test"
7. System initializes tracking
8. System confirms test active
```

#### Test Types
| Type | Variants |
|------|---------|
| email_subject | Different subject lines |
| email_content | Different email bodies |
| landing_page | Different page layouts |
| ad | Different ad copy |

#### Post-Conditions
- Test created and tracking active
- Results collected over test period

---

### 2.10 UC-010: View Campaign Analytics

**Primary Actor**: Marketing Manager  
**Priority**: Medium  
**Pre-condition**: Campaigns exist with data

#### Description
View performance metrics for marketing campaigns.

#### Basic Flow
```
1. Marketing Manager accesses analytics
2. System retrieves active campaigns
3. System calculates metrics for each:
   - Leads generated
   - Conversion rate
   - Active status
4. System displays campaign list with metrics
```

#### Metrics Display
| Campaign | Leads | Conversion Rate | Status |
|----------|-------|----------------|--------|
| Summer Sale | 150 | 3.5% | Active |
| Newsletter | 500 | 1.2% | Paused |

#### Post-Conditions
- Campaign performance visible
- Data available for reporting

---

## 3. Business Process Flows

### 3.1 Lead Nurturing Flow

```
┌─────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│  Form   │───▶│  Create  │───▶│  Score   │───▶│  Drip    │
│ Submit  │    │ Contact  │    │  10 pts  │    │ Trigger  │
└─────────┘    └──────────┘    └──────────┘    └──────────┘
                                                     │
                                                     ▼
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ Customer │◀───│  Score   │◀───│  Score   │◀───│  Email  │
│ (100+)   │    │  75+     │    │  50+     │    │ Queue   │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
```

### 3.2 Content Generation Flow

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Content   │───▶│   AI Gen    │───▶│  Platform   │
│   Topic    │    │  (OpenAI)   │    │ Transform  │
└─────────────┘    └─────────────┘    └─────────────┘
                                           │
                    ┌─────────────┐         │
                    │  Publish   │◀────────┘
                    │  (Social)  │
                    └─────────────┘
```

---

## 4. Requirements Traceability

| Use Case | Functional Requirements | Test Cases |
|---------|------------------------|------------|
| UC-001 | FR-001 | TC-001 |
| UC-002 | FR-002 | TC-002 |
| UC-003 | FR-005, FR-006 | TC-005, TC-006 |
| UC-004 | FR-007 | TC-007 |
| UC-005 | FR-009 | TC-009 |
| UC-006 | FR-008 | TC-008 |
| UC-007 | FR-010 | TC-010 |
| UC-008 | FR-010 | TC-010 |
| UC-009 | FR-012 | TC-012 |
| UC-010 | FR-011 | TC-011 |

---

**Document Owner**: KS Fraser Development Team  
**Review Status**: Pending