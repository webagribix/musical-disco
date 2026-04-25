<?php
declare(strict_types=1);

class BreedSeeder
{
    public function __construct(private App\Core\DB $db) {}

    public function run(): void
    {
        $breeds = [
            [
                'name'                  => 'Kienyeji (KALRO)',
                'target_weight_by_week' => json_encode(['1'=>0.07,'2'=>0.12,'3'=>0.18,'4'=>0.25,'5'=>0.33,'6'=>0.42,'8'=>0.60,'12'=>1.00,'20'=>1.50]),
                'expected_egg_rate'     => 65.00,
                'feed_intake_curve'     => json_encode(['1'=>0.01,'2'=>0.02,'3'=>0.03,'4'=>0.04,'5'=>0.05,'6'=>0.06,'8'=>0.07,'12'=>0.09,'20'=>0.10]),
                'notes'                 => 'Indigenous Kenyan breed. Hardy, dual-purpose, adapted to free-range. Improved by KALRO.',
            ],
            [
                'name'                  => 'Kuroiler',
                'target_weight_by_week' => json_encode(['1'=>0.10,'2'=>0.20,'3'=>0.35,'4'=>0.50,'5'=>0.68,'6'=>0.88,'8'=>1.30,'12'=>1.80,'20'=>2.20]),
                'expected_egg_rate'     => 72.00,
                'feed_intake_curve'     => json_encode(['1'=>0.015,'2'=>0.025,'3'=>0.04,'4'=>0.06,'5'=>0.075,'6'=>0.09,'8'=>0.11,'12'=>0.13,'20'=>0.13]),
                'notes'                 => 'Improved Kenyan/Indian dual-purpose breed. Good egg and meat production on low-cost diets.',
            ],
            [
                'name'                  => 'Broiler (Ross 308)',
                'target_weight_by_week' => json_encode(['1'=>0.17,'2'=>0.43,'3'=>0.85,'4'=>1.38,'5'=>1.90,'6'=>2.40,'7'=>2.85,'8'=>3.20]),
                'expected_egg_rate'     => 0.00,
                'feed_intake_curve'     => json_encode(['1'=>0.015,'2'=>0.04,'3'=>0.08,'4'=>0.13,'5'=>0.17,'6'=>0.21,'7'=>0.24,'8'=>0.27]),
                'notes'                 => 'Fast-growing commercial meat breed. Target slaughter at week 6-8.',
            ],
        ];

        foreach ($breeds as $breed) {
            $existing = $this->db->selectOne('breeds', ['name' => $breed['name']]);
            if (!$existing) {
                $breed['created_at'] = date('Y-m-d H:i:s');
                $breed['updated_at'] = date('Y-m-d H:i:s');
                $this->db->insert('breeds', $breed);
                echo "  Seeded breed: {$breed['name']}\n";
            } else {
                echo "  Skipped (exists): {$breed['name']}\n";
            }
        }
    }
}
