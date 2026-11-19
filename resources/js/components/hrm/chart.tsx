import {
    ArcElement,
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Filler,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
    type ChartData,
    type ChartOptions,
} from 'chart.js';
import { Bar, Doughnut, Line } from 'react-chartjs-2';

ChartJS.register(
    ArcElement,
    BarElement,
    CategoryScale,
    Filler,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

const PALETTE = [
    'rgba(59, 130, 246, 0.85)',
    'rgba(245, 158, 11, 0.85)',
    'rgba(16, 185, 129, 0.85)',
    'rgba(239, 68, 68, 0.85)',
    'rgba(139, 92, 246, 0.85)',
];

export type ChartProps = {
    type: 'line' | 'bar' | 'doughnut';
    labels: string[];
    datasets: Array<{
        label: string;
        data: number[];
        backgroundColor?: string | string[];
        borderColor?: string;
        fill?: boolean;
    }>;
    height?: number;
};

type NormalizedDataset = ChartProps['datasets'][number] & {
    borderWidth?: number;
    tension?: number;
    pointRadius?: number;
};

export default function Chart({
    type,
    labels,
    datasets,
    height = 260,
}: ChartProps) {
    const normalized: NormalizedDataset[] = datasets.map((dataset, index) => ({
        backgroundColor:
            dataset.backgroundColor ??
            (type === 'doughnut'
                ? PALETTE
                : `${PALETTE[index % PALETTE.length]}40`),
        borderColor:
            dataset.borderColor ?? PALETTE[index % PALETTE.length],
        borderWidth: type === 'doughnut' ? 0 : 2,
        fill: dataset.fill ?? type === 'line',
        tension: 0.35,
        pointRadius: 0,
        ...dataset,
    }));

    const options: ChartOptions<'line' | 'bar' | 'doughnut'> = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: {
            legend: {
                display: datasets.length > 1 || type === 'doughnut',
                labels: { color: '#a1a1aa', usePointStyle: true },
            },
        },
        scales:
            type === 'doughnut'
                ? undefined
                : {
                      x: {
                          grid: { display: false },
                          ticks: {
                              color: '#a1a1aa',
                              maxRotation: 0,
                              autoSkip: true,
                              maxTicksLimit: 10,
                          },
                      },
                      y: {
                          beginAtZero: true,
                          grid: { color: 'rgba(148, 163, 184, 0.15)' },
                          ticks: { color: '#a1a1aa' },
                      },
                  },
    };

    const chartData = { labels, datasets: normalized };

    return (
        <div style={{ height }} className="w-full">
            {type === 'line' && (
                <Line
                    data={chartData as ChartData<'line'>}
                    options={options as ChartOptions<'line'>}
                />
            )}
            {type === 'bar' && (
                <Bar
                    data={chartData as ChartData<'bar'>}
                    options={options as ChartOptions<'bar'>}
                />
            )}
            {type === 'doughnut' && (
                <Doughnut
                    data={chartData as ChartData<'doughnut'>}
                    options={options as ChartOptions<'doughnut'>}
                />
            )}
        </div>
    );
}
