<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MaterialPhysicalInventoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Agregación de movimientos de almacén agrupando estrictamente por Material y Estado
        // Excluye tensión y colapsa proyectos para obtener el inventario global físico.
        $mov = DB::table('mat_projects_materials as prm')
            ->join('mat_materials_summary as msu', 'msu.id_msu', '=', 'prm.materials_summary_id_prm')
            ->where('prm.deleted_prm', '!=', 1)
            ->where('msu.deleted_msu', '!=', 1)
            ->select(
                'prm.material_id_prm as material_id',
                // Saldo por estado: Nuevo (1), Viejo (2), Retirado en buen estado (3), Indefinido (4 / NULL)
                DB::raw("SUM(CASE 
                    WHEN IFNULL(prm.status_id_prm, 4) = 1 THEN
                        (CASE WHEN msu.summary_type_id_msu IN (3, 19, 9, 18) THEN prm.quantity_prm ELSE 0 END) -
                        (CASE WHEN msu.summary_type_id_msu IN (4, 17, 8) OR (msu.summary_type_id_msu = 14 AND msu.status_id_msu = 1) THEN prm.quantity_prm ELSE 0 END)
                    ELSE 0 END) AS stock_nvo"),
                DB::raw("SUM(CASE 
                    WHEN IFNULL(prm.status_id_prm, 4) = 2 THEN
                        (CASE WHEN msu.summary_type_id_msu IN (3, 19, 9, 18) THEN prm.quantity_prm ELSE 0 END) -
                        (CASE WHEN msu.summary_type_id_msu IN (4, 17, 8) OR (msu.summary_type_id_msu = 14 AND msu.status_id_msu = 1) THEN prm.quantity_prm ELSE 0 END)
                    ELSE 0 END) AS stock_meo"),
                DB::raw("SUM(CASE 
                    WHEN IFNULL(prm.status_id_prm, 4) = 3 THEN
                        (CASE WHEN msu.summary_type_id_msu IN (3, 19, 9, 18) THEN prm.quantity_prm ELSE 0 END) -
                        (CASE WHEN msu.summary_type_id_msu IN (4, 17, 8) OR (msu.summary_type_id_msu = 14 AND msu.status_id_msu = 1) THEN prm.quantity_prm ELSE 0 END)
                    ELSE 0 END) AS stock_rbe"),
                DB::raw("SUM(CASE 
                    WHEN IFNULL(prm.status_id_prm, 4) = 4 THEN
                        (CASE WHEN msu.summary_type_id_msu IN (3, 19, 9, 18) THEN prm.quantity_prm ELSE 0 END) -
                        (CASE WHEN msu.summary_type_id_msu IN (4, 17, 8) OR (msu.summary_type_id_msu = 14 AND msu.status_id_msu = 1) THEN prm.quantity_prm ELSE 0 END)
                    ELSE 0 END) AS stock_indefinido")
            )
            ->groupBy('prm.material_id_prm');

        return DB::table('mat_materials as mat')
            ->joinSub($mov, 'm', function ($join) {
                $join->on('mat.id_mat', '=', 'm.material_id');
            })
            ->where(function ($query) {
                // Solo materiales que tengan algún saldo registrado en almacén
                $query->where('m.stock_nvo', '!=', 0)
                      ->orWhere('m.stock_meo', '!=', 0)
                      ->orWhere('m.stock_rbe', '!=', 0)
                      ->orWhere('m.stock_indefinido', '!=', 0);
            })
            ->select(
                'mat.code_mat',
                'mat.description_mat',
                'mat.unit_of_measurement_mat',
                'm.stock_nvo',
                'm.stock_meo',
                'm.stock_rbe'
            )
            ->orderBy('mat.description_mat', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre del Material',
            'U.M.',
            'Saldo NVO',
            'Conteo NVO',
            'Saldo MEO',
            'Conteo MEO',
            'Saldo RBE',
            'Conteo RBE',
            'Observaciones / Descripción física en sitio'
        ];
    }

    public function map($row): array
    {
        return [
            $row->code_mat,
            $row->description_mat,
            $row->unit_of_measurement_mat ?? 'PZA',
            (float)$row->stock_nvo,
            '', // Celda vacía para conteo físico manual
            (float)$row->stock_meo,
            '', // Celda vacía para conteo físico manual
            (float)$row->stock_rbe,
            '', // Celda vacía para conteo físico manual
            ''  // Espacio amplio para observaciones manuales in situ
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Estilos para encabezados
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);

        // Bordes completos para facilitar la lectura y el marcado a mano
        $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'B0B0B0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Altura de fila cómoda para escribir a lapicero
        for ($r = 2; $r <= $lastRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
        }

        // Ancho específico para el campo de anotaciones manuales
        $sheet->getColumnDimension('J')->setWidth(40);

        return [];
    }
}