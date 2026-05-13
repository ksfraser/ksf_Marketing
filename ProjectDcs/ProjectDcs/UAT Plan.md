# ksf_Marketing - UAT Plan

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | UAT-MARKETING-001 |
| **Module** | ksf_Marketing |
| **Project** | Marketing Automation & AI |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. Introduction

### 1.1 Purpose
This UAT Plan defines acceptance testing for the ksf_Marketing module covering AI content generation, social media publishing, lead scoring, and campaign automation.

### 1.2 Scope
Testing covers end-to-end user workflows for marketing automation features.

### 1.3 Out of Scope
- Unit/integration testing (completed separately)
- Performance/load testing
- Security penetration testing

---

## 2. UAT Objectives

| ID | Objective | Success Metric |
|----|-----------|----------------|
| OBJ-001 | Validate AI article generation | Content generated successfully |
| OBJ-002 | Validate social publishing | Posts published to platforms |
| OBJ-003 | Validate lead scoring | Scores calculated correctly |
| OBJ-004 | Validate drip campaigns | Emails queued and sent |
| OBJ-005 | Validate A/B testing | Winners determined correctly |

---

## 3. UAT Scenarios

### 3.1 Scenario MU-01: Generate Marketing Article

**Scenario ID**: MU-01  
**Priority**: Critical  
**Business Value**: Core AI feature

#### Scenario
Marketing Manager generates a marketing article using AI.

#### Pre-Conditions
- OpenAI API key configured
- User logged in with marketing access

#### Test Steps
1. Navigate to AI Article Generator
2. Enter topic: "Cloud Computing Benefits"
3. Select tone: "Professional"
4. Click "Generate Article"
5. Wait for generation (up to 10 seconds)
6. Verify article displays with: Title, Content, Excerpt
7. Verify title is "Cloud Computing Benefits"
8. Verify content has structure (intro, points, conclusion)

#### Expected Results
- Article generated within 10 seconds
- Title matches topic
- Content is non-empty and structured
- Excerpt is ~200 characters

#### Pass Criteria
- [ ] Generation completes successfully
- [ ] All fields populated
- [ ] Content is coherent
- [ ] Fallback shown if API unavailable

---

### 3.2 Scenario MU-02: Publish to Twitter

**Scenario ID**: MU-02  
**Priority**: High  
**Business Value**: Social media automation

#### Scenario
Social Media Manager publishes a tweet.

#### Pre-Conditions
- Twitter API credentials configured
- User logged in with social access

#### Test Steps
1. Navigate to Social Publishing
2. Enter tweet content: "Excited to announce our new product launch!"
3. Select platform: Twitter
4. Verify character count: 50/280
5. Click "Publish Now"
6. Verify success message
7. Verify post ID returned

#### Expected Results
- Tweet published successfully
- Success confirmation displayed
- Post ID returned for tracking

#### Pass Criteria
- [ ] Tweet published
- [ ] Success message shown
- [ ] Post ID returned

---

### 3.3 Scenario MU-03: Lead Scoring Flow

**Scenario ID**: MU-03  
**Priority**: Critical  
**Business Value**: Lead qualification

#### Scenario
Sales team views lead qualification status.

#### Pre-Conditions
- Lead with existing score exists
- User logged in as Sales Rep

#### Test Steps
1. Navigate to Lead Management
2. View list of contacts
3. Select a contact
4. View lead score details
5. Verify score value displayed
6. Verify stage label displayed (Lead/MQL/SQL/Opportunity/Customer)
7. View recommended actions

#### Expected Results
- Score displayed (e.g., 35 points)
- Stage labeled correctly (e.g., "Marketing Qualified Lead")
- Recommendations shown (e.g., "Send product info", "Schedule demo")

#### Pass Criteria
- [ ] Score value accurate
- [ ] Stage matches score threshold
- [ ] Recommendations appropriate for stage

---

### 3.4 Scenario MU-04: Submit Marketing Form

**Scenario ID**: MU-04  
**Priority**: High  
**Business Value**: Lead capture

#### Scenario
Website visitor submits contact form.

#### Pre-Conditions
- Marketing form embedded on website
- Form fields: name, email, phone, company

#### Test Steps
1. Visit website with embedded form
2. Fill form fields:
   - Name: "John Smith"
   - Email: "john@example.com"
   - Phone: "555-1234"
   - Company: "Acme Corp"
3. Click "Submit"
4. Verify thank you message displayed
5. Log into admin
6. Find contact "John Smith"
7. Verify score is 10 (form submission points)

#### Expected Results
- Form submission successful
- Thank you message shown
- Contact created in database
- Lead score initialized at 10 points

#### Pass Criteria
- [ ] Form submission accepted
- [ ] Contact created
- [ ] Lead score initialized

---

### 3.5 Scenario MU-05: Create Drip Campaign

**Scenario ID**: MU-05  
**Priority**: High  
**Business Value**: Email automation

#### Scenario
Marketing Manager creates a welcome drip campaign.

#### Pre-Conditions
- User logged in as Marketing Manager
- Drip campaign feature accessible

#### Test Steps
1. Navigate to Drip Campaign Builder
2. Enter campaign name: "Welcome Series"
3. Select trigger: "Signup"
4. Add Step 1:
   - Name: "Welcome Email"
   - Delay: 0 days
   - Subject: "Welcome to Our Platform!"
   - Body: "Thank you for joining us..."
5. Add Step 2:
   - Name: "Feature Introduction"
   - Delay: 3 days
   - Subject: "Getting Started"
   - Body: "Here are our key features..."
6. Click "Save Campaign"
7. Verify campaign created with 2 steps
8. Activate campaign

#### Expected Results
- Campaign saved with 2 steps
- Trigger configured
- Campaign can be activated

#### Pass Criteria
- [ ] Campaign created
- [ ] Steps configured correctly
- [ ] Delay days calculated
- [ ] Campaign activated

---

### 3.6 Scenario MU-06: A/B Test Winner

**Scenario ID**: MU-06  
**Priority**: Medium  
**Business Value**: Optimization

#### Scenario
Marketing Manager views A/B test results.

#### Pre-Conditions
- A/B test with collected data exists
- Test has been running

#### Test Data
```
Variant A: 100 views, 5 conversions (5%)
Variant B: 100 views, 8 conversions (8%)
```

#### Test Steps
1. Navigate to A/B Test Results
2. View test: "Subject Line Test"
3. View results for each variant
4. Verify conversion rates calculated correctly
5. View winner determination
6. Verify Variant B identified as winner

#### Expected Results
- Variant A: 5% conversion rate
- Variant B: 8% conversion rate
- Winner: Variant B

#### Pass Criteria
- [ ] Rates calculated correctly
- [ ] Winner determined by highest rate
- [ ] Results displayed clearly

---

## 4. Test Data

### 4.1 Test Users

| User | Role | Access |
|------|------|--------|
| marketing_admin | Marketing Manager | Full access |
| content_creator | Content Creator | AI + Forms |
| social_manager | Social Media Manager | Social only |
| sales_rep | Sales Rep | Lead view |

### 4.2 Test Scenarios Matrix

| Scenario | User | Pre-Conditions | Expected | Pass |
|----------|------|----------------|----------|------|
| MU-01 | Marketing Manager | API key configured | Article generated | [ ] |
| MU-02 | Social Manager | Twitter credentials | Tweet published | [ ] |
| MU-03 | Sales Rep | Lead with score | Score displayed | [ ] |
| MU-04 | Visitor | Form embedded | Contact created | [ ] |
| MU-05 | Marketing Manager | Drip builder access | Campaign created | [ ] |
| MU-06 | Marketing Manager | Test with data | Winner determined | [ ] |

---

## 5. Success Criteria

| Criterion | Target | Weight |
|-----------|--------|--------|
| All critical scenarios pass | 100% | 40% |
| All high priority scenarios pass | 100% | 30% |
| No critical defects open | 0 | 30% |

**Overall Pass Threshold**: 95%

---

## 6. Sign-Off

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Business Owner | | | |
| Project Manager | | | |
| UAT Lead | | | |

---

**Document Owner**: KS Fraser Development Team  
**Status**: Ready for UAT