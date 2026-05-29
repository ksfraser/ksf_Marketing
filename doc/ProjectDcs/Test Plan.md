# Marketing - Test Plan

**Document ID:** TP-MARKETING-001  
**Module:** ksf_Marketing  
**Version:** 1.0.0  

---

## 1. Test Scope

- Lead score calculations
- Stage transitions
- Form rendering and processing
- A/B test tracking

## 2. Test Cases

### 2.1 LeadScoring Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-001 | testInitialScore | new contact | Score = 0, stage = 'lead' |
| TC-002 | testAddPoints | +10 points | Score = 10 |
| TC-003 | testSubtractPoints | -5 points | Score decreases |
| TC-004 | testStageTransition_Lead | score = 10 | Stage = 'lead' |
| TC-005 | testStageTransition_MQL | score = 30 | Stage = 'marketing_qualified' |
| TC-006 | testStageTransition_SQL | score = 60 | Stage = 'sales_qualified' |
| TC-007 | testStageTransition_Opp | score = 80 | Stage = 'opportunity' |
| TC-008 | testStageTransition_Customer | score = 100 | Stage = 'customer' |
| TC-009 | testPointsBreakdown | multiple adds | All reasons tracked |
| TC-010 | testRecommendations | stage='sql' | Returns SQL actions |

### 2.2 MarketingForm Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-020 | testRender_AllFieldTypes | text, select, textarea | Valid HTML rendered |
| TC-021 | testRender_Required | required field | Required attribute present |
| TC-022 | testProcessSubmission | valid data | Contact created |
| TC-023 | testProcessSubmission_InvalidEmail | missing email | Error returned |
| TC-024 | testProcessSubmission_InitialScore | form submit | Score = 10 assigned |

### 2.3 ABTest Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-030 | testRecordView | variant A | View count incremented |
| TC-031 | testRecordConversion | variant B | Conversion count incremented |
| TC-032 | testGetWinner | A: 10/100, B: 15/100 | B returned |
| TC-033 | testGetWinner_NoData | no views | null returned |