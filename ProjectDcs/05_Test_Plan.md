# Marketing Module - Test Plan

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Test Plan Specification |
| Module | ksf_Marketing |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Test Strategy

### 1.1 Test Types

| Type | Coverage Target | Tools |
|------|-----------------|-------|
| Unit Tests | 80% code coverage | PHPUnit |
| Integration Tests | Key workflows | PHPUnit + Mock DB |
| API Tests | All endpoints | PHPUnit HTTP |

### 1.2 Test Environment

- PHP 7.3+ (8.0+ recommended)
- PHPUnit 9.x+
- Mock database for integration tests
- FA/WP integration mocked

---

## 2. Test Cases

### 2.1 Lead Scoring Tests

| TC ID | Test Case | Expected Result |
|-------|-----------|-----------------|
| TC-MKT-001 | Score new lead with no engagement | Initial score (0) |
| TC-MKT-002 | Score after email opened | Score +5 |
| TC-MKT-003 | Score after form submitted | Score +20 |
| TC-MKT-004 | Score after multiple engagements | Cumulative score |
| TC-MKT-005 | Score crosses MQL threshold | Status = "qualified" |
| TC-MKT-006 | Score drops below MQL | Status unchanged (one-way) |

### 2.2 Drip Campaign Tests

| TC ID | Test Case | Expected Result |
|-------|-----------|-----------------|
| TC-MKT-010 | Create campaign with 3 steps | Campaign created, 3 steps |
| TC-MKT-011 | Enroll lead in active campaign | Enrollment record created |
| TC-MKT-012 | Process queue with pending emails | Emails scheduled |
| TC-MKT-013 | Process queue with no pending | Return 0 |
| TC-MKT-014 | Lead opens email, next step scheduled | Next step in queue |
| TC-MKT-015 | Unsubscribe removes from campaign | Lead not re-enrolled |

### 2.3 AI Marketing Tests

| TC ID | Test Case | Expected Result |
|-------|-----------|-----------------|
| TC-MKT-020 | Generate subject lines | Array of 3 suggestions |
| TC-MKT-021 | Generate content suggestions | Array of suggestions |
| TC-MKT-022 | Predict send time | Returns DateTime |
| TC-MKT-023 | Segment audience | Grouped lead IDs |

---

## 3. Test Coverage

### 3.1 Classes to Test

| Class | Coverage Target |
|-------|-----------------|
| LeadScoring | 90% |
| DripCampaigns | 85% |
| AIMarketing | 70% (mocked AI) |

### 3.2 Critical Paths

1. Lead capture → Scoring → MQL notification
2. Campaign creation → Enrollment → Email sent
3. Email engagement → Score update → Next step

---

## 4. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |