<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalaryReport implements FromCollection, WithHeadings, WithProperties,WithEvents{

    use Exportable;

    public $data;
    public $extraData;

    public function __construct($data, $extraData){
        $this->data = $data;
        $this->extraData = $extraData;
    }

    public function collection(){

        return collect($this->data);
    }

    public function headings(): array{
        return $this->extraData['heading'];
    }

  /*  public function sheets(): array{
         return mergeCells(['A1:E1']);
    }*/

 /*   public static function afterSheet(AfterSheet $event)
    {
        try {
            $workSheet = $event
                ->sheet
                ->getDelegate()
                ->setMergeCells([
                    'A1:A2',
                    'B1:B2',
                    'C1:D1',
                ])
                ->freezePane('A3');

            $headers = $workSheet->getStyle('A1:D2');

            $headers
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $headers->getFont()->setBold(true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }*/

    public function registerEvents(): array{

        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:P1';
                $event->sheet->getDelegate()->setMergeCells([
                                                                'A1:U1',
                                                                'A2:U2',
                                                                'A3:U3',
                                                                //'A4:B4',
                                                                // 'D4:I4',
                                                            ]);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(18);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A2:U2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A3:U3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // $event->sheet->getDelegate()->getStyle('A4:M4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
            },
        ];

    }

    public function properties(): array
    {
        return [
            'creator'        => 'RESICO INDIA PVT LTD.',
            'lastModifiedBy' => 'RESICO INDIA PVT LTD.',
            'title'          => 'Salary Sheet',
            'description'    => 'RESICO INDIA PVT LTD. - Salary Sheet',
            'subject'        => 'RESICO INDIA PVT LTD. - Salary Sheet',
            'keywords'       => 'salary,export,spreadsheet',
            'category'       => 'salary report',
            'manager'        => 'RESICO INDIA PVT LTD.',
            'company'        => 'RESICO INDIA PVT LTD.',
        ];
    }

}
