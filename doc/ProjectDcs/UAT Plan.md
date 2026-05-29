# Marketing - UAT Plan

**Document ID:** UAT-MARKETING-001  
**Module:** ksf_Marketing  
**Version:** 1.0.0  

---

## 1. UAT Objectives

Verify that:
1. Lead scoring accurately tracks engagement
2. Stage transitions occur at correct thresholds
3. Forms capture contacts correctly
4. A/B tests determine winners correctly

## 2. Test Scenarios

| Scenario | Expected | Tester |
|----------|----------|--------|
| UAT-001: Submit marketing form | Contact created, score 10 | Marketing Manager |
| UAT-002: Add engagement points | Score increases | Sales Rep |
| UAT-003: Lead reaches MQL threshold | Stage changes | System |
| UAT-004: Create A/B test | Test created with variants | Marketing Analyst |
| UAT-005: Record test conversion | Conversion counted | System |
| UAT-006: End test, get winner | Winner determined | Marketing Analyst |

## 3. Test Data

| Contact | Scenario |
|---------|----------|
| Test Lead 1 | Form submission, no other engagement |
| Test Lead 2 | Multiple email opens, moderate score |
| Test Lead 3 | Demo request, high score |

## 4. Sign-Off

| Role | Name | Date |
|------|------|------|
| Marketing Manager | | |
| Sales Rep | | |
| QA Lead | | |