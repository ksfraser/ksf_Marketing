<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\Marketing;

use PHPUnit\Framework\TestCase;

/**
 * The marketing tables were addressed as `TB_PREF . "marketing_contacts"` and
 * `TB_PREF . "marketing_lead_scores"`, i.e. `0_marketing_contacts` and
 * `0_marketing_lead_scores`. No schema in the estate creates either table, so
 * every read and write failed with MySQL error 1146.
 *
 * A marketing contact is a captured lead, not an FA person, so the tables
 * stay module-local - but they move into the 0_ksf_marketing_* family, which
 * is where a schema for them now lives (sql/).
 */
class MarketingTableNameTest extends TestCase
{
    private const CONTACTS_TABLE = 'ksf_marketing_contacts';

    private const SCORES_TABLE = 'ksf_marketing_lead_scores';

    private string $moduleDir;

    protected function setUp(): void
    {
        $this->moduleDir = dirname(__DIR__, 3);
    }

    /**
     * @dataProvider sourceFileProvider
     */
    public function testSourceDoesNotAddressTheNonExistentMarketingTables(string $file): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/' . $file);

        $this->assertStringNotContainsString(
            'TB_PREF . "marketing_contacts',
            $contents,
            $file . ' must not address 0_marketing_contacts: no schema creates it'
        );
        $this->assertStringNotContainsString(
            'TB_PREF . "marketing_lead_scores',
            $contents,
            $file . ' must not address 0_marketing_lead_scores: no schema creates it'
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function sourceFileProvider(): array
    {
        return [
            'LeadScoring'   => ['src/Ksfraser/Marketing/LeadScoring.php'],
            'DripCampaigns' => ['src/Ksfraser/Marketing/DripCampaigns.php'],
        ];
    }

    public function testLeadScoringUsesTheModuleOwnedTables(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/src/Ksfraser/Marketing/LeadScoring.php');

        $this->assertSame(2, substr_count($contents, 'TB_PREF . "' . self::SCORES_TABLE));
        $this->assertSame(1, substr_count($contents, 'TB_PREF . "' . self::CONTACTS_TABLE));
    }

    public function testDripCampaignsUsesTheModuleOwnedContactsTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/src/Ksfraser/Marketing/DripCampaigns.php');

        $this->assertSame(1, substr_count($contents, 'TB_PREF . "' . self::CONTACTS_TABLE));
    }

    public function testSchemaCreatesTheContactsTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/sql/ksf_marketing_contacts.sql');

        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS `0_' . self::CONTACTS_TABLE . '`',
            $contents
        );
    }

    public function testSchemaCreatesTheLeadScoresTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/sql/ksf_marketing_lead_scores.sql');

        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS `0_' . self::SCORES_TABLE . '`',
            $contents
        );
    }

    /**
     * The runtime is PHP 7.4.33; match() is PHP 8 and is a parse error there.
     */
    public function testSourceIsParseableOnPhp74(): void
    {
        foreach (['src/Ksfraser/Marketing/LeadScoring.php', 'src/Ksfraser/Marketing/DripCampaigns.php'] as $file) {
            $contents = (string) file_get_contents($this->moduleDir . '/' . $file);

            $this->assertDoesNotMatchRegularExpression(
                '/(=|return)\s*match\s*\(/',
                $contents,
                $file . ' must not use PHP 8 match()'
            );
        }
    }
}
