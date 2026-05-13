# ksf_Marketing - Business Requirements

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | BRD-MARKETING-001 |
| **Module** | ksf_Marketing |
| **Project** | Marketing Automation & AI |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |
| **Status** | Draft |

---

## 1. Project Overview

### 1.1 Project Name
**ksf_Marketing** - Marketing Automation & AI Integration Module

### 1.2 Project Type
Marketing Automation Platform Module

### 1.3 Core Functionality Summary
The ksf_Marketing module provides comprehensive marketing automation capabilities including AI-powered content generation, social media publishing, drip email campaigns, lead scoring, and campaign tracking. It integrates with OpenAI for content generation and multiple social media platforms for distribution.

### 1.4 Target Users
- **Marketing Teams**: Content creators, social media managers
- **Sales Teams**: Lead qualification and nurturing
- **Business Owners**: Campaign oversight and ROI tracking
- **Developers**: Integration and extension

---

## 2. Problem Statement

### 2.1 Business Problem
Organizations struggle with:
- Manual content creation taking excessive time
- Inconsistent marketing messaging across channels
- Difficulty tracking lead quality and conversion
- Complex drip campaign management
- Social media scheduling and posting complexity

### 2.2 Current Solution Gaps

| Gap | Impact |
|-----|--------|
| Manual content writing | Slow time-to-market for campaigns |
| No lead scoring | Sales wastes time on unqualified leads |
| Scattered campaigns | No unified view of marketing activity |
| Manual social posting | Inconsistent publishing schedule |
| No A/B testing | Suboptimal campaign performance |

### 2.3 Opportunity
The ksf_Marketing module provides:
- AI-powered content generation for rapid campaign creation
- Automated lead scoring based on engagement
- Drip campaign automation for nurturing
- Multi-platform social media publishing
- Campaign tracking and conversion analytics

---

## 3. Project Scope

### 3.1 In-Scope Features

#### AI-Powered Content Generation
1. **Article Generation**
   - Generate marketing articles from topics
   - Adjustable tone (professional, casual, technical)
   - Structured output with intro, points, conclusion

2. **Advertisement Copy**
   - Platform-specific ad generation
   - Character limits enforced per platform
   - CTA inclusion

3. **Social Media Posts**
   - Platform-optimized transformations
   - Hashtag generation for reach
   - Character limits enforced

4. **Email Campaign Templates**
   - Welcome, promotional, newsletter, follow-up
   - Subject line generation
   - HTML body generation

#### Social Media Integration
5. **Twitter Publishing**
   - Direct posting to Twitter API
   - Media attachment support
   - Scheduled posting

6. **LinkedIn Publishing**
   - Organization posting
   - Article and link posts
   - Media support

7. **Facebook Integration** (Planned)
   - Page posting
   - Ad creation

#### Lead Management
8. **Lead Scoring**
   - Point-based scoring system
   - Engagement tracking
   - Stage progression (Lead → MQL → SQL → Opportunity → Customer)
   - Action recommendations per stage

9. **Contact Management**
   - Contact database
   - Source tracking
   - Form submission integration

#### Campaign Automation
10. **Drip Email Campaigns**
    - Multi-step sequences
    - Trigger-based activation
    - Delay scheduling
    - Queue management

11. **Campaign Tracking**
    - Conversion tracking
    - Lead generation metrics
    - Conversion rate calculation

#### Testing and Optimization
12. **A/B Testing**
    - Email subject testing
    - Content testing
    - Landing page testing
    - Statistical winner determination

13. **Marketing Forms**
    - Custom form builder
    - Field configuration
    - Submit action configuration

### 3.2 Out-of-Scope Features
- SMS notifications (roadmap)
- Push notifications (roadmap)
- Advanced analytics dashboard
- Multi-tenant support
- White-label functionality

### 3.3 Project Boundaries

```
┌─────────────────────────────────────────────────────────────┐
│                    ksf_Marketing Module                     │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────┐  ┌───────────────┐  ┌──────────────────┐ │
│  │AI Marketing   │  │Social Media  │  │Drip Campaigns    │ │
│  │- Content Gen  │  │- Twitter     │  │- Multi-step seq  │ │
│  │- Ad Copy     │  │- LinkedIn    │  │- Triggers        │ │
│  │- Email Draft │  │- Facebook    │  │- Queue Manager   │ │
│  └───────────────┘  └───────────────┘  └──────────────────┘ │
│                                                              │
│  ┌───────────────┐  ┌───────────────┐  ┌──────────────────┐ │
│  │Lead Scoring   │  │Campaign Track │  │A/B Testing        │ │
│  │- Point System│  │- Conversion   │  │- Variants        │ │
│  │- Stages      │  │- Metrics      │  │- Winner analysis │ │
│  │- Actions     │  │- Attribution  │  │- Results         │ │
│  └───────────────┘  └───────────────┘  └──────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              │
          ┌───────────────────┼───────────────────┐
          ▼                   ▼                   ▼
    ┌───────────┐       ┌───────────┐       ┌───────────┐
    │  OpenAI   │       │  Twitter  │       │ LinkedIn  │
    │    API    │       │    API    │       │    API    │
    └───────────┘       └───────────┘       └───────────┘
```

---

## 4. Module Features

### 4.1 AI Content Generation

#### F-001: Article Generation
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-001 |
| **Priority** | High |
| **Complexity** | Medium |

Generates structured marketing articles with:
- Topic-based content
- Configurable tone
- Introduction, key points, conclusion structure
- Title and excerpt generation

#### F-002: Ad Copy Generation
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-002 |
| **Priority** | High |
| **Complexity** | Low |

Platform-specific advertisement copy:
- Facebook ads (200 chars)
- Instagram (125 chars + hashtags)
- LinkedIn (3000 chars)
- Google Ads (headlines + descriptions)

#### F-003: Social Post Transformation
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-003 |
| **Priority** | High |
| **Complexity** | Low |

Transforms content for social platforms:
- Twitter: 280 char limit, truncation
- LinkedIn: Professional tone, hashtags
- Facebook: Engagement-focused

#### F-004: Email Campaign Generation
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-004 |
| **Priority** | Medium |
| **Complexity** | Medium |

Email template generation:
- Welcome emails
- Promotional emails
- Newsletter content
- Follow-up sequences

### 4.2 Social Media Publishing

#### F-005: Twitter Integration
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-005 |
| **Priority** | High |
| **Complexity** | Medium |

Direct Twitter API integration:
- Tweet posting
- Media attachment
- Scheduled posts storage

#### F-006: LinkedIn Integration
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-006 |
| **Priority** | High |
| **Complexity** | Medium |

LinkedIn API integration:
- Organization posts
- Article content
- UGC post creation

#### F-007: Scheduled Posts
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-007 |
| **Priority** | Medium |
| **Complexity** | Low |

Post scheduling system:
- Schedule future posts
- Platform selection
- Status tracking

### 4.3 Lead Management

#### F-008: Lead Scoring Engine
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-008 |
| **Priority** | High |
| **Complexity** | Medium |

Point-based scoring system:
- Contact engagement tracking
- Stage progression (5 stages)
- Points breakdown by action
- Next action recommendations

#### F-009: Marketing Forms
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-009 |
| **Priority** | Medium |
| **Complexity** | Medium |

Form management:
- Field configuration
- Custom styling
- API or webhook submission
- Contact capture

### 4.4 Campaign Automation

#### F-010: Drip Campaigns
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-010 |
| **Priority** | High |
| **Complexity** | High |

Multi-step email sequences:
- Trigger types: signup, purchase, inactivity, tag, score
- Configurable delays
- Step sequencing
- Queue management

#### F-011: Campaign Tracking
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-011 |
| **Priority** | Medium |
| **Complexity** | Low |

Campaign analytics:
- Conversion tracking
- Lead generation counts
- Conversion rate calculation
- Active campaign filtering

#### F-012: A/B Testing
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-012 |
| **Priority** | Medium |
| **Complexity** | Medium |

Split testing:
- Variant creation
- View/conversion tracking
- Statistical winner determination
- Multiple test types

### 4.5 Lead Stages

| Stage | Code | Score Threshold | Description |
|-------|------|----------------|-------------|
| Lead | lead | 0-24 | Initial contact |
| Marketing Qualified | mql | 25-49 | Engaged with marketing |
| Sales Qualified | sql | 50-74 | Ready for sales contact |
| Opportunity | opportunity | 75-99 | Active opportunity |
| Customer | customer | 100+ | Closed deal |

### 4.6 Triggers Supported

| Trigger | Code | Description |
|---------|------|-------------|
| Signup | signup | New contact created |
| Purchase | purchase | Order/complete |
| Inactivity | inactivity | No engagement for period |
| Tag Added | tag_added | Specific tag assigned |
| Score Threshold | score_threshold | Score reaches level |

---

## 5. Integration Dependencies

### 5.1 External Service Dependencies

| Service | Integration Type | Required | API Details |
|---------|-----------------|----------|-------------|
| OpenAI | AI Content | Yes | API key required |
| Twitter API | Social Posting | Optional | OAuth 2.0 |
| LinkedIn API | Social Posting | Optional | OAuth 2.0 |
| WordPress | Platform | Optional | For WP integration |

### 5.2 Database Dependencies

| Table | Purpose | Required |
|-------|---------|----------|
| marketing_scheduled_posts | Post scheduling | Yes |
| marketing_campaigns | Campaign tracking | Yes |
| marketing_drip_campaigns | Drip sequences | Yes |
| marketing_drip_queue | Email queue | Yes |
| marketing_contacts | Lead database | Yes |
| marketing_lead_scores | Scoring data | Yes |
| marketing_forms | Form definitions | Yes |
| marketing_ab_tests | A/B test data | Yes |

### 5.3 Platform Requirements

| Requirement | Specification |
|------------|---------------|
| PHP Version | 7.3+ |
| WordPress | 5.0+ (for WP integration) |
| Database | MySQL 5.7+ |
| OpenAI API | GPT-4 compatible |

---

## 6. User Roles and Permissions

### 6.1 Role Matrix

| Role | Content | Social | Lead Mgmt | Campaign | Admin |
|------|---------|--------|-----------|----------|-------|
| Marketing Manager | Full | Full | Full | Full | Yes |
| Content Creator | Full | Full | View | View | No |
| Social Media Manager | View | Full | View | View | No |
| Sales Rep | View | View | Full | View | No |

### 6.2 Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[ksf_ai_article topic="..."]` | AI-generated article |
| `[ksf_social_post platform="..." content="..."]` | Social preview |
| `[ksf_campaign_list]` | Active campaigns list |

---

## 7. Success Criteria

### 7.1 Functional Criteria
- [ ] AI content generation produces valid output
- [ ] Social posts publish to correct platforms
- [ ] Lead scoring calculates correctly
- [ ] Drip campaigns execute on triggers
- [ ] A/B tests track and determine winners

### 7.2 Technical Criteria
- [ ] OpenAI API integration functional
- [ ] Twitter API integration functional
- [ ] LinkedIn API integration functional
- [ ] Database operations performant
- [ ] Error handling robust

### 7.3 Performance Criteria
- [ ] AI generation < 10 seconds response
- [ ] Social publish < 5 seconds
- [ ] Lead scoring < 100ms
- [ ] Campaign queries < 500ms

---

## 8. Roadmap

### 8.1 Phase 1 (Current)
- AI content generation
- Basic social posting
- Lead scoring
- Drip campaigns

### 8.2 Phase 2 (Planned)
- Mautic-style form builder
- SMS gateway integration
- Advanced analytics

### 8.3 Phase 3 (Future)
- Push notifications
- Point-based rewards
- Contact segmentation

---

## 9. Glossary

| Term | Definition |
|------|------------|
| Lead Scoring | Point-based system for lead qualification |
| MQL | Marketing Qualified Lead |
| SQL | Sales Qualified Lead |
| Drip Campaign | Automated multi-step email sequence |
| UTM | Urchin Tracking Module (analytics) |
| Conversion | Desired user action completion |

---

## 10. References

### Internal References
- KSF Module Development Standards
- FrontAccounting Marketing Adapter Guidelines

### External References
- OpenAI API Documentation
- Twitter API v2 Documentation
- LinkedIn API Documentation

---

**Document Owner**: KS Fraser Development Team  
**Approval Status**: Pending Review