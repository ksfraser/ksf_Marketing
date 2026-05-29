# Marketing Module - Use Case Specification

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Use Case Specification |
| Module | ksf_Marketing |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Use Cases Overview

| UC ID | Use Case | Actor | Priority |
|-------|----------|-------|----------|
| UC-MKT-001 | Capture Lead from Web Form | Website Visitor | High |
| UC-MKT-002 | Score Lead | System | High |
| UC-MKT-003 | Create Drip Campaign | Marketing Manager | High |
| UC-MKT-004 | Enroll Lead in Campaign | System | High |
| UC-MKT-005 | Track Email Engagement | System | High |
| UC-MKT-006 | Convert Lead to Customer | Sales Rep | High |
| UC-MKT-007 | Generate AI Content | Marketing Manager | Medium |

---

## 2. Detailed Use Cases

### UC-MKT-001: Capture Lead from Web Form

**Description**: System captures a lead submission from a web form and creates a lead record.

**Actors**: Website Visitor, Marketing System

**Preconditions**:
- Web form configured and active
- Marketing module installed

**Flow**:
1. Visitor submits web form with contact information
2. System validates required fields (email required)
3. System checks for duplicate email
4. System creates lead record with source="web_form"
5. System triggers lead scoring
6. System dispatches `marketing.lead.created` event

**Postconditions**:
- Lead record created in system
- Lead enrolled in appropriate campaigns

**Alternative Flows**:
- Duplicate email: Update existing lead with new data
- Invalid email: Return error to visitor

---

### UC-MKT-002: Score Lead

**Description**: System automatically calculates and updates lead score based on rules.

**Actors**: System

**Preconditions**:
- Lead exists in system
- Scoring rules configured

**Flow**:
1. System identifies scoring triggers (email opened, form submitted, etc.)
2. System retrieves all applicable rules
3. System calculates total score by summing rule points
4. System updates lead record with new score
5. System checks if lead crossed MQL threshold
6. If MQL threshold crossed:
   a. System updates lead status to "qualified"
   b. System dispatches `marketing.lead.mql` event
   c. System notifies sales team
7. System dispatches `marketing.lead.score_changed` event

**Postconditions**:
- Lead score updated
- Status may change if threshold crossed

---

### UC-MKT-003: Create Drip Campaign

**Description**: Marketing Manager creates a multi-step email drip campaign.

**Actors**: Marketing Manager

**Preconditions**:
- Marketing Manager authenticated
- EmailManager integration configured

**Flow**:
1. Marketing Manager accesses Campaign Manager
2. Manager creates new campaign with name and description
3. Manager defines campaign goal (nurture, re-engage, onboarding)
4. Manager adds email steps:
   a. Select email template for step
   b. Set delay from previous step (days)
   c. Set conditional logic (if opened, if clicked, etc.)
5. Manager defines target segment
6. Manager activates campaign

**Postconditions**:
- Campaign created in draft or active status
- Leads matching segment can be enrolled

---

### UC-MKT-004: Enroll Lead in Campaign

**Description**: System automatically enrolls a lead in a drip campaign.

**Actors**: System (triggered by lead creation or manual action)

**Preconditions**:
- Campaign exists and is active
- Lead meets campaign segment criteria
- Lead not already enrolled

**Flow**:
1. System evaluates lead against campaign segment
2. If lead matches segment criteria:
   a. System creates enrollment record
   b. System schedules first email (immediate or per delay)
   c. System dispatches `marketing.campaign.enrolled` event
3. Email queue processes step 1

**Postconditions**:
- Lead enrolled in campaign
- First email scheduled

---

### UC-MKT-005: Track Email Engagement

**Description**: System tracks and processes email engagement events.

**Actors**: System, EmailManager

**Preconditions**:
- Email sent via drip campaign
- Email tracking enabled

**Flow**:
1. EmailManager sends email and tracks:
   - Open event (via tracking pixel)
   - Click event (via tracked links)
2. Engagement event received by Marketing module
3. System processes event:
   - Open: Update lead engagement, may trigger next step
   - Click: Higher score boost, may trigger next step
4. System checks for next step in campaign
5. If next step exists and conditions met:
   a. System schedules next email
6. System updates enrollment record

**Postconditions**:
- Lead engagement tracked
- Next campaign step scheduled if applicable

---

### UC-MKT-006: Convert Lead to Customer

**Description**: Sales Rep converts a qualified lead to a CRM customer.

**Actors**: Sales Rep, System

**Preconditions**:
- Lead is qualified (MQL or higher)
- CRM integration configured

**Flow**:
1. Sales Rep reviews lead in Marketing module or CRM
2. Sales Rep qualifies lead as sales-ready
3. Sales Rep initiates conversion:
   a. Select conversion action (create customer, opportunity, etc.)
   b. Map lead data to CRM fields
4. System creates customer record in CRM
5. System updates lead status to "converted"
6. System removes lead from active campaigns
7. System dispatches `marketing.lead.converted` event

**Postconditions**:
- Lead converted to customer
- Lead no longer in marketing campaigns

---

### UC-MKT-007: Generate AI Content

**Description**: Marketing Manager uses AI to generate email content suggestions.

**Actors**: Marketing Manager, AI System

**Preconditions**:
- AI Marketing features enabled
- OpenAI/API configured

**Flow**:
1. Marketing Manager accesses AI Marketing feature
2. Manager enters campaign goal (e.g., "nurture new leads")
3. Manager requests content generation:
   - Subject lines: Enter count (default 3)
   - Email body: Enter goal and context
4. AI Marketing calls AI API with prompts
5. AI returns suggestions
6. Manager reviews and selects/modifies suggestions
7. Manager applies suggestion to email template

**Postconditions**:
- Content suggestion available for use
- Optionally applied to email template

---

## 3. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |