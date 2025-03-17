<?php

namespace App\Filament\Widgets;

use App\Models\Cita;
use App\Models\Frecuencia;
use Flowframe\Trend\Trend;

// use Carbon\Carbon;
use App\Models\VentaProducto;
use App\Models\VentaServicio;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AvancesDashChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Avances';

    protected static ?string $maxHeight = '180px';

    protected static ?int $sort = 4;

    // protected int | string | array $columnSpan = '1';

    protected function getData(): array
    {

        $start = $this->filters['startDate'] == null ? now()->startOfDay()->format('Y-m-d') : date('Y-m-d', strtotime($this->filters['startDate']));
        $end = $this->filters['endDate'] == null ? now()->endOfDay() : $this->filters['endDate'] . ' 23:59:59';


        // $citas_agendadas_bot = Cita::where('responsable', 'PiedyBot')
        //     ->where('fecha_formateada', $start)
        //     ->count();

        // $citas_agendadas_sistema = Cita::where('responsable', '!=', 'PiedyBot')
        //     ->where('fecha_formateada', $start)
        //     ->count();

        // $citas_canceladas = Cita::where('responsable', '!=', 'PiedyBot')
        //     ->where('fecha_formateada', $start)
        //     ->where('status', 3)
        //     ->count();


        // $array = [
        //     $citas_agendadas_bot,
        //     $citas_agendadas_sistema,
        //     $citas_canceladas
        // ];

        $labels = [
            '1',
            '2',
        ];

        // dd($array, $labels);
        // dd($data);

        $labels = $labels;

        // $shortenedLabels = $labels->map(function($label) {
        //     return substr($label, 0, 10) . (strlen($label) > 10 ? '...' : '');
        // });

        return [
            'datasets' => [
                [
                    'label' => '',
                    'data' => [2, 5, 10],
                    'backgroundColor' => [
                        '#a16d69',
                        '#99bcbf',
                        '#bf99a9',
                        '#bfaf99',
                        '#99a9bf',
                        '#99bfaf',
                        '#9c99bf',
                        '#99bf9c',
                        '#bf9c99',
                        '#bf99bc',
                        '#c7a8a5',
                        '#ab7e7a',
                        '#7ba69d',
                        '#7b9aa6',
                        '#a6877b',
                        '#7b85a6',
                        '#a69d7b',
                        '#a67b85',
                        '#9aa67b',
                        '#7ba687',
                        '#a67b9a',
                        '#56737f'
                    ],
                    'borderColor' => '#ffff',
                    // 'fill' => true,
                ],

            ],
            'labels' => $labels,

        ];
    }

    public function getDescription(): ?string
    {
        return 'aaaa';
    }

    protected static ?array $options = [
        'scales' => [
            'x' => [
                'display' => true,

                'ticks' => [
                    'stepSize' => 1
                ],
            ],
            'y' => [
                'display' => true,
            ],
        ],
        'plugins' => [
            'legend' => [
                'display' => false,
            ]
        ]
    ];

    protected function getType(): string
    {
        return 'bar';
    }
}
