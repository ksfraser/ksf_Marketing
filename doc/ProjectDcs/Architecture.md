# Marketing - Architecture

**Document ID:** ARCH-MARKETING-001  
**Module:** ksf_Marketing  
**Version:** 1.0.0  

---

## 1. Module Overview

Marketing module implements business entities and services for lead management, campaign automation, and marketing optimization.

## 2. Class Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     LeadScoring                              │
├─────────────────────────────────────────────────────────────┤
│ - contact_id: int                                           │
│ - score: int                                                │
│ - stage: string                                              │
│ - points_breakdown: array                                    │
├─────────────────────────────────────────────────────────────┤
│ + addPoints(reason, points): void                           │
│ + subtractPoints(reason, points): void                      │
│ + getRecommendations(): array                               │
│ - updateStage(): void                                       │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    MarketingForm                             │
├─────────────────────────────────────────────────────────────┤
│ - id: int                                                    │
│ - name: string                                               │
│ - html_content: string                                       │
│ - css_content: string                                        │
│ - fields: array                                              │
│ - submit_url: string                                         │
├─────────────────────────────────────────────────────────────┤
│ + render(): string                                           │
│ + processSubmission(data): array                            │
│ + static find(id): ?MarketingForm                           │
│ + static all(): MarketingForm[]                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                       ABTest                                │
├─────────────────────────────────────────────────────────────┤
│ - id: int                                                    │
│ - name: string                                               │
│ - type: string                                               │
│ - variants: array                                            │
│ - results: array                                             │
│ - status: string                                             │
├─────────────────────────────────────────────────────────────┤
│ + recordConversion(variantId): void                         │
│ + recordView(variantId): void                               │
│ + getWinner(): ?string                                       │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                   AIMarketing                                │
├─────────────────────────────────────────────────────────────┤
│ + generateContent(type, context): string                     │
│ + suggestOptimization(campaignId): array                     │
│ + predictScore(contactId): int                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                   DripCampaigns                              │
├─────────────────────────────────────────────────────────────┤
│ + create(): void                                            │
│ + addTouch(sequence): void                                  │
│ + execute(contactId): void                                  │
└─────────────────────────────────────────────────────────────┘
```

## 3. Directory Structure

```
ksf_Marketing/
├── src/Ksfraser/Marketing/
│   ├── LeadScoring.php
│   ├── AIMarketing.php
│   ├── DripCampaigns.php
├── includes/
│   └── ksf_marketing.php
└── doc/ProjectDcs/
```

## 4. Database Tables

```sql
CREATE TABLE marketing_lead_scores (
    contact_id INT PRIMARY KEY,
    score INT DEFAULT 0,
    stage VARCHAR(50) DEFAULT 'lead',
    points_breakdown JSON,
    updated_at DATETIME
);

CREATE TABLE marketing_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    html_content TEXT,
    css_content TEXT,
    fields JSON,
    submit_action VARCHAR(50),
    submit_url VARCHAR(500),
    options JSON
);

CREATE TABLE marketing_ab_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    type VARCHAR(50),
    variants JSON,
    results JSON,
    status VARCHAR(50),
    start_date DATE,
    end_date DATE
);
```

## 5. Technology Stack

| Component | Technology |
|-----------|------------|
| Language | PHP |
| Database | MySQL/MariaDB |
| AI | External AI API integration |