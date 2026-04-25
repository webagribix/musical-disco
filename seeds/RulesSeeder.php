<?php
declare(strict_types=1);

class RulesSeeder
{
    public function __construct(private App\Core\DB $db) {}

    public function run(): void
    {
        $rules = [
            [
                'name'             => 'High Mortality Rate (48h)',
                'metric'           => 'mortality',
                'condition_op'     => 'gt',
                'threshold_value'  => 2.0000,  // 2%
                'time_window_hours'=> 48,
                'severity'         => 'critical',
            ],
            [
                'name'             => 'Feed Intake Drop >30% vs 7-day avg',
                'metric'           => 'feed',
                'condition_op'     => 'drop_pct',
                'threshold_value'  => 30.0000,
                'time_window_hours'=> 24,
                'severity'         => 'high',
            ],
            [
                'name'             => 'Egg Production Drop >20% vs 7-day avg',
                'metric'           => 'eggs',
                'condition_op'     => 'drop_pct',
                'threshold_value'  => 20.0000,
                'time_window_hours'=> 24,
                'severity'         => 'medium',
            ],
            [
                'name'             => 'High Brooding Temperature (>35°C)',
                'metric'           => 'temperature',
                'condition_op'     => 'gt',
                'threshold_value'  => 35.0000,
                'time_window_hours'=> 1,
                'severity'         => 'high',
            ],
            [
                'name'             => 'Water Intake Drop >30% vs 7-day avg',
                'metric'           => 'water',
                'condition_op'     => 'drop_pct',
                'threshold_value'  => 30.0000,
                'time_window_hours'=> 24,
                'severity'         => 'high',
            ],
        ];

        foreach ($rules as $rule) {
            $existing = $this->db->selectOne('rules', ['name' => $rule['name']]);
            if (!$existing) {
                $rule['is_active']  = 1;
                $rule['created_at'] = date('Y-m-d H:i:s');
                $rule['updated_at'] = date('Y-m-d H:i:s');
                $this->db->insert('rules', $rule);
                echo "  Seeded rule: {$rule['name']}\n";
            } else {
                echo "  Skipped (exists): {$rule['name']}\n";
            }
        }
    }
}
