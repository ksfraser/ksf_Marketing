# Marketing Module - Architecture

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Technical Architecture Specification |
| Module | ksf_Marketing |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Architecture Overview

### 1.1 Design Philosophy

The Marketing module follows the KSF Business Logic + Platform Adapters pattern:
- Business logic in `src/Ksfraser/Marketing/`
- Platform-specific adapters in respective FA/WP modules
- Event-driven integration with other modules

### 1.2 Module Structure

```
ksf_Marketing/
├── src/Ksfraser/Marketing/
│   ├── LeadScoring.php         # Lead scoring engine
│   ├── DripCampaigns.php      # Drip campaign management
│   ├── AIMarketing.php        # AI-powered features
│   └── Events/
├── tests/Unit/
└── ProjectDcs/
```

---

## 2. Core Classes

### 2.1 LeadScoring

```php
namespace Ksfraser\Marketing;

class LeadScoring {
    
    private array $rules = [];
    
    public function addRule(ScoringRule $rule): void;
    public function calculateScore(Lead $lead): int;
    public function getMQLThreshold(): int;
    public function isMarketingQualified(int $score): bool;
}
```

### 2.2 DripCampaigns

```php
namespace Ksfraser\Marketing;

class DripCampaigns {
    
    public function createCampaign(array $data): Campaign;
    public function addStep(int $campaignId, CampaignStep $step): void;
    public function enrollLead(int $campaignId, int $leadId): void;
    public function processQueue(): int; // Returns emails sent
}
```

### 2.3 AIMarketing

```php
namespace Ksfraser\Marketing;

class AIMarketing {
    
    public function generateSubjectLine(string $goal, int $count = 3): array;
    public function suggestContent(string $goal, array $context): array;
    public function predictSendTime(int $recipientId): \DateTime;
    public function segmentAudience(array $criteria): array;
}
```

---

## 3. Event System

### 3.1 Dispatched Events

| Event | Trigger | Data |
|-------|---------|------|
| `marketing.lead.created` | New lead captured | lead |
| `marketing.lead.score_changed` | Score recalculated | lead, old_score, new_score |
| `marketing.lead.mql` | Becomes MQL | lead |
| `marketing.campaign.started` | Campaign launched | campaign |
| `marketing.campaign.email_sent` | Email delivered | campaign, lead |
| `marketing.campaign.completed` | Campaign ends | campaign |

---

## 4. Database Schema

### 4.1 Tables

```sql
-- Leads
CREATE TABLE {PREFIX}marketing_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    company VARCHAR(255),
    source VARCHAR(50),
    score INT DEFAULT 0,
    status ENUM('new', 'contacted', 'qualified', 'converted', 'lost') DEFAULT 'new',
    created_at DATETIME,
    updated_at DATETIME
);

-- Campaigns
CREATE TABLE {PREFIX}marketing_campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('draft', 'active', 'paused', 'completed') DEFAULT 'draft',
    start_date DATE,
    end_date DATE,
    created_at DATETIME
);

-- Campaign Steps
CREATE TABLE {PREFIX}marketing_campaign_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    step_order INT NOT NULL,
    delay_days INT DEFAULT 0,
    email_template_id INT,
    created_at DATETIME
);

-- Enrollments
CREATE TABLE {PREFIX}marketing_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    campaign_id INT NOT NULL,
    current_step INT DEFAULT 0,
    enrolled_at DATETIME,
    last_activity_at DATETIME,
    completed_at DATETIME,
    FOREIGN KEY (lead_id) REFERENCES {PREFIX}marketing_leads(id),
    FOREIGN KEY (campaign_id) REFERENCES {PREFIX}marketing_campaigns(id)
);
```

---

## 5. Dependencies

| Package | Purpose |
|---------|---------|
| `ksfraser/traits` | Reusable traits |
| `ksfraser/exceptions` | Exception hierarchy |
| `ksf_EmailManager` | Email delivery integration |
| `ksf_CRM` | Lead conversion |

---

## 6. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |