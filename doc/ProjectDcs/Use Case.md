# Marketing - Use Cases

**Document ID:** UC-MARKETING-001  
**Module:** ksf_Marketing  
**Version:** 1.0.0  

---

## 1. Use Case Overview

### UC-001: Score Lead Engagement

**Description:** System updates lead score based on prospect engagement.

**Primary Flow:**
1. Contact performs engagement action (form submit, email open, etc.)
2. System calls addPoints() with reason and points
3. System updates total score
4. System recalculates stage
5. System persists to database

---

### UC-002: Process Marketing Form

**Description:** System processes visitor form submission.

**Primary Flow:**
1. Visitor submits marketing form
2. System validates required fields
3. System creates new contact record
4. System assigns form_submission points (10)
5. System returns success message
6. System provides initial lead score

---

### UC-003: Conduct A/B Test

**Description:** Marketing Analyst runs email subject line test.

**Primary Flow:**
1. Analyst creates A/B test with variants
2. Test activates at scheduled date
3. System shows random variant to contacts
4. System records view for shown variant
5. On conversion, system records conversion
6. When test ends, system calculates winner

---

### UC-004: View Lead Recommendations

**Description:** Sales rep views recommended next actions for lead.

**Primary Flow:**
1. Sales rep accesses lead profile
2. System retrieves LeadScoring data
3. System calls getRecommendations()
4. System returns score, stage, and next actions
5. Rep uses recommendations to engage lead

## 2. Actors

| Actor | Role |
|-------|------|
| Contact | Website visitor, lead |
| Marketing Manager | Create campaigns, A/B tests |
| Sales Rep | View and act on lead scores |
| System | Track engagement, update scores |