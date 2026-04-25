<?php
declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $this->dompdf = new Dompdf($options);
    }

    public function generateFromHtml(string $html, string $paperSize = 'A4', string $orientation = 'portrait'): string
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($paperSize, $orientation);
        $this->dompdf->render();
        return $this->dompdf->output();
    }

    public function generatePLReport(array $plData): string
    {
        $currency = APP_CURRENCY;
        $html     = "<!DOCTYPE html><html><head><meta charset='utf-8'>
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
            h1   { color: #2e7d32; }
            table{ width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
            th     { background: #e8f5e9; }
            .total { font-weight: bold; background: #f1f8e9; }
            .profit{ color: #2e7d32; } .loss { color: #c62828; }
        </style></head><body>
        <h1>P&amp;L Report — {$plData['batch_name']}</h1>
        <p>Generated: " . date('Y-m-d H:i') . "</p>
        <h2>Revenue</h2>
        <table><tr><th>Type</th><th>Amount ({$currency})</th></tr>";

        foreach ($plData['revenue_by_type'] as $row) {
            $html .= "<tr><td>{$row['sale_type']}</td><td>" . number_format($row['revenue'], 2) . "</td></tr>";
        }
        $html .= "<tr class='total'><td>Total Revenue</td><td>" . number_format($plData['total_revenue'], 2) . "</td></tr></table>";

        $html .= "<h2>Expenses</h2><table><tr><th>Category</th><th>Amount ({$currency})</th></tr>";
        foreach ($plData['expenses_by_category'] as $row) {
            $html .= "<tr><td>{$row['category']}</td><td>" . number_format($row['total'], 2) . "</td></tr>";
        }
        $html .= "<tr class='total'><td>Total Expenses</td><td>" . number_format($plData['total_expenses'], 2) . "</td></tr></table>";

        $profitClass = $plData['net_profit'] >= 0 ? 'profit' : 'loss';
        $html .= "<h2>Summary</h2><table>
        <tr><td>Net Profit/Loss</td><td class='{$profitClass}'>{$currency} " . number_format($plData['net_profit'], 2) . "</td></tr>
        <tr><td>Live Bird Count</td><td>{$plData['live_count']}</td></tr>
        <tr><td>Cost Per Bird</td><td>{$currency} " . number_format($plData['cost_per_bird'], 2) . "</td></tr>
        <tr><td>Cost Per Egg</td><td>{$currency} " . number_format($plData['cost_per_egg'], 2) . "</td></tr>
        <tr><td>FCR</td><td>{$plData['fcr']}</td></tr>
        </table></body></html>";

        return $this->generateFromHtml($html);
    }
}
