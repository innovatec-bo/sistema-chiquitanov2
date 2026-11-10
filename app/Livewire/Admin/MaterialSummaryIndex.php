<?php

namespace App\Livewire\Admin;

use App\Models\MaterialSummary;
use App\Models\SummaryType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\MaterialPhysicalInventoryExport;
use Maatwebsite\Excel\Facades\Excel;

Class MaterialSummaryIndex extends Component
{
    use WithPagination;
    
    public $search;
    public $idMSU;
    public $projectCode;
    public $from;
    public $to;
    public $sort = 'id_msu';
    public $direction = 'desc';
    public $deleteId;
    public $materialSummaryTypeSelected;
    public $materialSummaryTypes;
    protected $queryString = [
        'idMSU' => ['except' => '', 'as' => 'id-de-movimiento'],
        'projectCode' => ['except' => '', 'as' => 'codigo'],
        'materialSummaryTypeSelected' => ['except' => '', 'as' => 'tipo-de-movimiento'],
        'from' => ['except' => '', 'as' => 'desde'],
        'to' => ['except' => '', 'as' => 'hasta']
    ];

    protected $listeners = [
        'object-deleted' => 'render'
    ];

    protected $messages = [
        'from.date_format' => 'Formato incorrecto.',
        'to.date_format' => 'Formato incorrecto.',
    ];

    public function mount()
    {
        $this->materialSummaryTypes = SummaryType::all();
    }

    public function updating($attribute)
    {
        $toValidate = ['idMSU','projectCode','materialSummaryTypeSelected','from', 'to'];
        if (array_search($attribute, $toValidate) !== FALSE) 
        {
            $this->resetPage();
        }
    }
    
    public function rules()
    {
        return [
            'from' => ['date_format:d-m-Y'],
            'to' => ['date_format:d-m-Y'],
        ];
    }

    public function render()
    {
        // $this->validate();
        $materialSummaryList = MaterialSummary::whereNotNull('id_msu')
        ->when($this->idMSU, function(Builder $query, $idMSU){
            $query->where('id_msu', $this->idMSU);
        })
        ->when($this->projectCode, function(Builder $query, $projectCode){
            $query->whereHas('project', function(Builder $query){
                $query->where('code_pro','like', '%'.$this->projectCode.'%');
            });
        })
        ->when($this->from, function(Builder $query, $from){
            $from = Carbon::createFromFormat('d-m-Y',$from)->format('Y-m-d 00:00:00');
            $query->where('entry_date_msu', '>=', $from);
        })
        ->when($this->to, function(Builder $query, $to){
            $to = Carbon::createFromFormat('d-m-Y', $to)->format('Y-m-d 23:59:59');
            $query->where('entry_date_msu', '<=', $to);
        })
        ->when($this->materialSummaryTypeSelected, function(Builder $query, $materialSummaryTypeSelected){
            $query->whereHas('summaryType', function(Builder $query){
                $query->where('id_mqt',$this->materialSummaryTypeSelected);
            });
        })
        ->orderBy($this->sort, $this->direction)
        ->paginate(6);

        return view('livewire.admin.material-summary-index', compact('materialSummaryList'));
    }

    public function triggerLoading()
    {
        return 0;
    }

    public function order($sort)
    {
        if ($this->sort == $sort) 
        {
            if ($this->direction == 'desc') 
            {
                $this->direction = 'asc';
            } 
            else 
            {
                $this->direction = 'desc';
            }
            
        } 
        else 
        {
            $this->sort = $sort;
            $this->direction = 'asc';
        }   
    }

    public function downloadMaterialPhysicalInventory()
    {
        $fileName = 'Planilla_Conteo_Fisico_Materiales_' . now()->format('Y.m.d H.i.s') . '.xlsx';
        return Excel::download(new MaterialPhysicalInventoryExport, $fileName);
    }
}
