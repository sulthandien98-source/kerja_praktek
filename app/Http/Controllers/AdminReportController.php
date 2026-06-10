<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;

class AdminReportController extends Controller
{
    public function index()
    {
        $today        = Carbon::today();
        $startWeek    = Carbon::now()->startOfWeek();
        $endWeek      = Carbon::now()->endOfWeek();
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        $dailySales = Order::whereDate('created_at', $today)
            ->where('status', Order::STATUS_SELESAI)
            ->sum('total_price');

        $dailyOrders = Order::whereDate('created_at', $today)->count();

        $weeklySales = Order::whereBetween('created_at', [$startWeek, $endWeek])
            ->where('status', Order::STATUS_SELESAI)
            ->sum('total_price');

        $weeklyOrders = Order::whereBetween('created_at', [$startWeek, $endWeek])->count();

        $monthlySales = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', Order::STATUS_SELESAI)
            ->sum('total_price');

        $monthlyOrders = Order::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $recentOrders = Order::with('user')->latest()->take(10)->get();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = Carbon::now()->subDays($i);
            $sales = Order::whereDate('created_at', $date)
                ->where('status', Order::STATUS_SELESAI)
                ->sum('total_price');

            $chartData[] = [
                'date'  => $date->format('d M'),
                'sales' => (int) $sales,
            ];
        }

        return view('admin.rekapitulasi.index', compact(
            'dailySales',   'dailyOrders',
            'weeklySales',  'weeklyOrders',
            'monthlySales', 'monthlyOrders',
            'recentOrders', 'chartData'
        ));
    }

    public function exportPdf()
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(500, 'Package DomPDF tidak terinstall. Jalankan: composer require barryvdh/laravel-dompdf');
        }

        $orders = Order::with('user')->latest()->take(500)->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.rekapitulasi.pdf',
            compact('orders')
        )->setPaper('a4', 'landscape');

        return $pdf->download('rekapitulasi-penjualan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        $orders   = Order::with('user')->latest()->get();
        $fileName = 'rekapitulasi-penjualan-' . now()->format('Y-m-d') . '.xls';

        // Gunakan format HTML-table yang dibaca Excel sebagai XLS
        // Tidak butuh library, tidak corrupt, kolom terpisah rapi
        $html  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $html .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                            xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
                            xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";
        $html .= '<Styles>
                    <Style ss:ID="header">
                        <Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/>
                        <Interior ss:Color="#EA580C" ss:Pattern="Solid"/>
                        <Alignment ss:Horizontal="Center"/>
                    </Style>
                    <Style ss:ID="title">
                        <Font ss:Bold="1" ss:Size="14"/>
                        <Alignment ss:Horizontal="Center"/>
                    </Style>
                    <Style ss:ID="currency">
                        <NumberFormat ss:Format="&quot;Rp &quot;#,##0"/>
                    </Style>
                    <Style ss:ID="center">
                        <Alignment ss:Horizontal="Center"/>
                    </Style>
                    <Style ss:ID="even">
                        <Interior ss:Color="#FFF7ED" ss:Pattern="Solid"/>
                    </Style>
                  </Styles>' . "\n";
        $html .= '<Worksheet ss:Name="Rekapitulasi">' . "\n";
        $html .= '<Table>' . "\n";

        // Row 1: Judul
        $html .= '<Row>
                    <Cell ss:MergeAcross="5" ss:StyleID="title">
                        <Data ss:Type="String">LAPORAN REKAPITULASI PENJUALAN - ' . now()->format('d/m/Y') . '</Data>
                    </Cell>
                  </Row>' . "\n";

        // Row 2: Subtitle
        $html .= '<Row>
                    <Cell ss:MergeAcross="5" ss:StyleID="center">
                        <Data ss:Type="String">Dimsum Mak\'Angga</Data>
                    </Cell>
                  </Row>' . "\n";

        // Row 3: Kosong
        $html .= '<Row><Cell><Data ss:Type="String"></Data></Cell></Row>' . "\n";

        // Row 4: Header kolom
        $headers = ['No', 'Customer', 'No HP', 'Total', 'Status', 'Tanggal'];
        $html   .= '<Row>' . "\n";
        foreach ($headers as $header) {
            $html .= '<Cell ss:StyleID="header"><Data ss:Type="String">'
                  . htmlspecialchars($header)
                  . '</Data></Cell>' . "\n";
        }
        $html .= '</Row>' . "\n";

        // Data rows
        $no = 1;
        foreach ($orders as $i => $order) {
            $style = ($i % 2 === 1) ? ' ss:StyleID="even"' : '';

            $html .= '<Row>' . "\n";
            $html .= '<Cell' . $style . '><Data ss:Type="Number">' . $no++ . '</Data></Cell>' . "\n";
            $html .= '<Cell' . $style . '><Data ss:Type="String">' . htmlspecialchars($order->customer_name) . '</Data></Cell>' . "\n";
            $html .= '<Cell' . $style . '><Data ss:Type="String">' . htmlspecialchars($order->phone ?? '-') . '</Data></Cell>' . "\n";
            $html .= '<Cell ss:StyleID="currency"><Data ss:Type="Number">' . (int) $order->total_price . '</Data></Cell>' . "\n";
            $html .= '<Cell' . $style . '><Data ss:Type="String">' . htmlspecialchars($order->status_label ?? ucfirst($order->status)) . '</Data></Cell>' . "\n";
            $html .= '<Cell' . $style . '><Data ss:Type="String">' . $order->created_at->format('d M Y H:i') . '</Data></Cell>' . "\n";
            $html .= '</Row>' . "\n";
        }

        $html .= '</Table>' . "\n";
        $html .= '</Worksheet>' . "\n";
        $html .= '</Workbook>' . "\n";

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'public',
        ]);
    }
}
