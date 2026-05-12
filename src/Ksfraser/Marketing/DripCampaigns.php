<?php

namespace Ksfraser\Marketing;

class DripCampaign
{
    public int $id;
    public string $name;
    public string $trigger;
    public array $steps;
    public string $status;
    public string $created_at;

    public const TRIGGER_SIGNUP = 'signup';
    public const TRIGGER_PURCHASE = 'purchase';
    public const TRIGGER_INACTIVITY = 'inactivity';
    public const TRIGGER_TAG_ADDED = 'tag_added';
    public const TRIGGER_SCORE = 'score_threshold';

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? 0;
            $this->name = $data['name'];
            $this->trigger = $data['trigger'];
            $this->steps = json_decode($data['steps'] ?? '[]', true);
            $this->status = $data['status'] ?? 'draft';
            $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        }
    }

    public function addStep(array $step): void
    {
        $this->steps[] = array_merge($step, ['order' => count($this->steps) + 1]);
    }

    public function executeForContact(int $contact_id): array
    {
        $sent = [];
        
        foreach ($this->steps as $step) {
            $delay = $step['delay_days'] ?? 0;
            
            $scheduled = date('Y-m-d H:i:s', strtotime("+{$delay} days"));
            
            $this->scheduleEmail($contact_id, $step, $scheduled);
            $sent[] = $step['name'];
        }
        
        return $sent;
    }

    private function scheduleEmail(int $contact_id, array $step, string $scheduled): void
    {
        global $db;
        include_once '../../../includes/db.inc';

        $sql = "INSERT INTO " . TB_PREF . "marketing_drip_queue
            (campaign_id, contact_id, subject, body, scheduled_at, status, created_at)
            VALUES (
                " . db_escape($this->id) . ",
                " . db_escape($contact_id) . ",
                " . db_escape($step['subject'] ?? 'Follow up') . ",
                " . db_escape($step['body'] ?? '') . ",
                " . db_escape($scheduled) . ",
                'pending',
                NOW()
            )";

        db_query($sql);
    }

    public static function find(int $id): ?self
    {
        global $db;
        include_once '../../../includes/db.inc';

        $sql = "SELECT * FROM " . TB_PREF . "marketing_drip_campaigns WHERE id = " . db_escape($id);
        $result = db_query($sql);
        $row = db_fetch_assoc($result);

        return $row ? new self($row) : null;
    }

    public static function findByTrigger(string $trigger): array
    {
        global $db;
        include_once '../../../includes/db.inc';

        $sql = "SELECT * FROM " . TB_PREF . "marketing_drip_campaigns 
            WHERE trigger = " . db_escape($trigger) . " AND status = 'active'";

        $result = db_query($sql);
        $campaigns = [];

        while ($row = db_fetch_assoc($result)) {
            $campaigns[] = new self($row);
        }

        return $campaigns;
    }
}

class DripEmailSender
{
    public static function processQueue(): int
    {
        global $db;
        include_once '../../../includes/db.inc';

        $sql = "SELECT * FROM " . TB_PREF . "marketing_drip_queue 
            WHERE status = 'pending' 
            AND scheduled_at <= NOW() 
            LIMIT 50";

        $result = db_query($sql);
        $sent = 0;

        while ($row = db_fetch_assoc($result)) {
            if (self::sendEmail($row['contact_id'], $row['subject'], $row['body'])) {
                $sql = "UPDATE " . TB_PREF . "marketing_drip_queue 
                    SET status = 'sent', sent_at = NOW() 
                    WHERE id = " . db_escape($row['id']);
                db_query($sql);
                $sent++;
            } else {
                $sql = "UPDATE " . TB_PREF . "marketing_drip_queue 
                    SET status = 'failed', error = 'Send failed' 
                    WHERE id = " . db_escape($row['id']);
                db_query($sql);
            }
        }

        return $sent;
    }

    private static function sendEmail(int $contact_id, string $subject, string $body): bool
    {
        global $db;
        include_once '../../../includes/db.inc';

        $sql = "SELECT email FROM " . TB_PREF . "marketing_contacts 
            WHERE id = " . db_escape($contact_id);

        $result = db_query($sql);
        $row = db_fetch_assoc($result);

        if (!$row || empty($row['email'])) {
            return false;
        }

        $to = $row['email'];
        
        $headers = "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'example.com') . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return mail($to, $subject, $body, $headers);
    }
}