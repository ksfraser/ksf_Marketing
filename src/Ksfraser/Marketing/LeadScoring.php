<?php

namespace Ksfraser\Marketing;

class LeadScoring
{
    private static ?bool $faAvailable = null;

    private static function checkFaAvailable(): bool
    {
        if (self::$faAvailable !== null) {
            return self::$faAvailable;
        }
        self::$faAvailable = function_exists('db_query') && defined('TB_PREF') && is_readable(__DIR__ . '/../../../includes/db.inc');
        return self::$faAvailable;
    }

    public int $contact_id;
    public int $score;
    public string $stage;
    public array $points_breakdown;

    public const STAGE_LEAD = 'lead';
    public const STAGE_MQL = 'marketing_qualified';
    public const STAGE_SQL = 'sales_qualified';
    public const STAGE_OPPORTUNITY = 'opportunity';
    public const STAGE_CUSTOMER = 'customer';

    public function __construct(int $contact_id)
    {
        $this->contact_id = $contact_id;
        $this->score = 0;
        $this->stage = self::STAGE_LEAD;
        $this->points_breakdown = [];
        $this->load();
    }

    private function load(): void
    {
        if (!self::checkFaAvailable()) {
            return;
        }

        $sql = "SELECT * FROM " . TB_PREF . "marketing_lead_scores 
            WHERE contact_id = " . db_escape($this->contact_id);

        $result = @db_query($sql);
        if (!$result) {
            return;
        }
        
        $row = @db_fetch_assoc($result);
        if ($row) {
            $this->score = (int)($row['score'] ?? 0);
            $this->stage = $row['stage'] ?? self::STAGE_LEAD;
            $this->points_breakdown = is_string($row['points_breakdown'] ?? null) 
                ? json_decode($row['points_breakdown'], true) ?? [] 
                : ($row['points_breakdown'] ?? []);
        }
    }

    public function addPoints(string $reason, int $points): void
    {
        $this->score += $points;
        $this->points_breakdown[$reason] = ($this->points_breakdown[$reason] ?? 0) + $points;
        $this->updateStage();
        $this->save();
    }

    public function subtractPoints(string $reason, int $points): void
    {
        $this->addPoints($reason, -$points);
    }

    private function updateStage(): void
    {
        if ($this->score >= 100) {
            $this->stage = self::STAGE_CUSTOMER;
        } elseif ($this->score >= 75) {
            $this->stage = self::STAGE_OPPORTUNITY;
        } elseif ($this->score >= 50) {
            $this->stage = self::STAGE_SQL;
        } elseif ($this->score >= 25) {
            $this->stage = self::STAGE_MQL;
        } else {
            $this->stage = self::STAGE_LEAD;
        }
    }

    private function save(): void
    {
        if (!self::checkFaAvailable()) {
            return;
        }

        $sql = "INSERT INTO " . TB_PREF . "marketing_lead_scores
            (contact_id, score, stage, points_breakdown, updated_at)
            VALUES (
                " . db_escape($this->contact_id) . ",
                " . db_escape($this->score) . ",
                " . db_escape($this->stage) . ",
                " . db_escape(json_encode($this->points_breakdown)) . ",
                NOW()
            )
            ON DUPLICATE KEY UPDATE 
                score = " . db_escape($this->score) . ",
                stage = " . db_escape($this->stage) . ",
                points_breakdown = " . db_escape(json_encode($this->points_breakdown)) . ",
                updated_at = NOW()";

        @db_query($sql);
    }

    public function getRecommendations(): array
    {
        return [
            'score' => $this->score,
            'stage' => $this->stage,
            'actions' => $this->getNextActions(),
        ];
    }

    private function getNextActions(): array
    {
        if ($this->stage === self::STAGE_LEAD) {
            return ['Send welcome email', 'Add to nurture campaign'];
        } elseif ($this->stage === self::STAGE_MQL) {
            return ['Send product info', 'Schedule demo'];
        } elseif ($this->stage === self::STAGE_SQL) {
            return ['Send quote', 'Schedule meeting'];
        } elseif ($this->stage === self::STAGE_OPPORTUNITY) {
            return ['Send proposal', 'Follow up call'];
        } else {
            return ['Cross-sell', 'Request referral'];
        }
    }
}

class MarketingForm
{
    public int $id;
    public string $name;
    public string $html_content;
    public string $css_content;
    public array $fields;
    public string $submit_action;
    public string $submit_url;
    public array $options;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->name = $data['name'] ?? '';
            $this->html_content = $data['html_content'] ?? '';
            $this->css_content = $data['css_content'] ?? '';
            $this->fields = is_string($data['fields'] ?? null) 
                ? json_decode($data['fields'], true) ?? [] 
                : ($data['fields'] ?? []);
            $this->submit_action = $data['submit_action'] ?? 'api';
            $this->submit_url = $data['submit_url'] ?? '';
            $this->options = is_string($data['options'] ?? null) 
                ? json_decode($data['options'], true) ?? [] 
                : ($data['options'] ?? []);
        }
    }

    public function render(): string
    {
        $html = '<form class="ksf-marketing-form" data-form-id="' . $this->id . '">';
        
        foreach ($this->fields as $field) {
            $required = $field['required'] ?? false ? 'required' : '';
            $label = $field['label'] ?? $field['name'] ?? '';
            $type = $field['type'] ?? 'text';
            
            $html .= '<div class="form-group">';
            $html .= '<label>' . htmlspecialchars($label) . '</label>';
            
            if ($type === 'select') {
                $html .= '<select name="' . ($field['name'] ?? '') . '" ' . $required . '>';
                $html .= '<option value="">Select...</option>';
                foreach ($field['options'] ?? [] as $opt) {
                    $html .= '<option value="' . htmlspecialchars($opt) . '">' . htmlspecialchars($opt) . '</option>';
                }
                $html .= '</select>';
            } elseif ($type === 'textarea') {
                $html .= '<textarea name="' . ($field['name'] ?? '') . '" ' . $required . '></textarea>';
            } else {
                $html .= '<input type="' . htmlspecialchars($type) . '" name="' . ($field['name'] ?? '') . '" ' . $required . '>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '<button type="submit">' . htmlspecialchars($this->options['submit_text'] ?? 'Submit') . '</button>';
        $html .= '</form>';
        
        if ($this->css_content) {
            $html .= '<style>' . $this->css_content . '</style>';
        }
        
        return $html;
    }

    public function processSubmission(array $data): array
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return [
                'success' => false,
                'contact_id' => 0,
                'message' => 'Database not available',
            ];
        }

        $contact_data = [
            'name' => $data['name'] ?? (($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'company' => $data['company'] ?? '',
            'source' => 'form_' . $this->id,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $cols = implode(', ', array_keys($contact_data));
        $vals = implode(', ', array_map(fn($v) => db_escape($v), array_values($contact_data)));
        
        $sql = "INSERT INTO " . TB_PREF . "marketing_contacts ($cols) VALUES ($vals)";
        @db_query($sql);
        
        $contact_id = function_exists('db_insert_id') ? db_insert_id() : 0;

        $lead = new LeadScoring($contact_id);
        $lead->addPoints('form_submission', 10);

        return [
            'success' => true,
            'contact_id' => $contact_id,
            'message' => 'Thank you for your interest!',
        ];
    }

    public static function find(int $id): ?self
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return null;
        }

        $sql = "SELECT * FROM " . TB_PREF . "marketing_forms WHERE id = " . db_escape($id);
        $result = @db_query($sql);
        if (!$result) {
            return null;
        }
        $row = @db_fetch_assoc($result);

        return $row ? new self($row) : null;
    }

    public static function all(): array
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return [];
        }

        $sql = "SELECT * FROM " . TB_PREF . "marketing_forms ORDER BY name";
        $result = @db_query($sql);
        if (!$result) {
            return [];
        }
        
        $forms = [];
        while ($row = @db_fetch_assoc($result)) {
            $forms[] = new self($row);
        }

        return $forms;
    }
}

class ABTest
{
    public int $id;
    public string $name;
    public string $type;
    public array $variants;
    public array $results;
    public string $status;
    public string $start_date;
    public ?string $end_date;

    public const TYPE_EMAIL_SUBJECT = 'email_subject';
    public const TYPE_EMAIL_CONTENT = 'email_content';
    public const TYPE_LANDING_PAGE = 'landing_page';
    public const TYPE_AD = 'ad';

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->variants = is_array($data['variants'] ?? null) 
            ? $data['variants'] 
            : json_decode($data['variants'] ?? '[]', true);
        $this->results = is_array($data['results'] ?? null) 
            ? $data['results'] 
            : json_decode($data['results'] ?? '{}', true);
        $this->status = $data['status'] ?? 'draft';
        $this->start_date = $data['start_date'] ?? '';
        $this->end_date = $data['end_date'] ?? null;
    }

    public function recordConversion(string $variant_id): void
    {
        if (!isset($this->results[$variant_id])) {
            $this->results[$variant_id] = ['views' => 0, 'conversions' => 0];
        }
        
        $this->results[$variant_id]['conversions']++;
        $this->saveResults();
    }

    public function recordView(string $variant_id): void
    {
        if (!isset($this->results[$variant_id])) {
            $this->results[$variant_id] = ['views' => 0, 'conversions' => 0];
        }
        
        $this->results[$variant_id]['views']++;
        $this->saveResults();
    }

    public function getWinner(): ?string
    {
        $best = null;
        $best_rate = 0;
        
        foreach ($this->results as $variant_id => $data) {
            if ($data['views'] > 0) {
                $rate = $data['conversions'] / $data['views'];
                if ($rate > $best_rate) {
                    $best_rate = $rate;
                    $best = $variant_id;
                }
            }
        }
        
        return $best;
    }

    private function saveResults(): void
    {
        if (!function_exists('db_query') || !defined('TB_PREF')) {
            return;
        }

        $sql = "UPDATE " . TB_PREF . "marketing_ab_tests
            SET results = " . db_escape(json_encode($this->results)) . "
            WHERE id = " . db_escape($this->id);

        @db_query($sql);
    }
}