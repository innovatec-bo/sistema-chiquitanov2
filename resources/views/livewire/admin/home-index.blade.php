<div class="row g-6">
    <div class="col-md-6 col-xxl-4 mb-6">
        <div class="card h-100 position-relative">
            <div class="overlay" wire:loading.flex wire:target="previousPage, nextPage, gotoPage, search,searchSubscriber">
                <div class="sk-swing sk-primary">
                    <div class="sk-swing-dot"></div>
                    <div class="sk-swing-dot"></div>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
            <div class="card-title m-0 me-2">
                <h5 class="mb-1">Con cronograma de poda</h5>
                <a href="{{route('admin.tree-prunings.index')}}">Ver detalles...</a>
            </div>
            <div class="dropdown">
                <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0 me-n1 waves-effect" type="button" id="popularProduct" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="popularProduct">
                <a class="dropdown-item waves-effect" href="javascript:void(0);">Last 28 Days</a>
                <a class="dropdown-item waves-effect" href="javascript:void(0);">Last Month</a>
                <a class="dropdown-item waves-effect" href="javascript:void(0);">Last Year</a>
                </div>
            </div>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
                    @foreach ($budgets as $budget)
                        <li class="d-flex mb-3">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                <h6 class="mb-0">{{ $budget->project->code_pro }}</h6>
                                <small class="text-body d-block">{{!isset($budget->project->statusDetail(2)['responsible'])?'':$budget->project->statusDetail(2)['responsible']}}</small>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    {{-- <p class="mb-0">$999.29</p> --}}
                                    @if ($budget->treePruning->count() == 0)
                                        <p class="text-danger mb-0">Sin registros</p>
                                    @elseif ($budget->treePruning->count() == 1)
                                        {{$budget->treePruning->count()}} registro
                                    @elseif ($budget->treePruning->count() > 1)
                                        {{$budget->treePruning->count()}} registros
                                    @endif
                                </div>
                            </div>    
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer py-2">
                <div class="row">
                    <div class="col-md-12">
                        {{ $budgets->links(data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-4 position-relative shadow-lg">
            <div class="overlay" wire:loading.flex wire:target="previousPage, nextPage, gotoPage, search, roleId">
                <div class="sk-swing sk-primary">
                    <div class="sk-swing-dot"></div>
                    <div class="sk-swing-dot"></div>
                </div>
            </div>
            
            <div class="card-header">
                @if ($lastIncident)
                    {{ round($lastIncident->created_at->diffInDays(), 0) }} dias sin incidentes
                @else
                    Sin incidentes registrados
                @endif
                
            </div>
            <div class="card-body p-0">
                <!-- /.row-->
                <div class="table-responsive">
                    <table class="table table-striped table-hover border mb-0 small table-sm">
                        <thead class="table-light fw-semibold">
                            <tr class="align-middle">
                                <th>Proyecto</th>
                                <th class="text-center">Detalle</th>
                                <th class="text-center">Estatus en<br>incidente</th>
                                <th class="text-center">Estatus actual</th>
                                <th>Tipo de incidente</th>
                                <th>Autor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incidents as $incident)
                                <tr class="align-middle">
                                    <td class="px-2">
                                        {{ $incident->project->code_pro }}
                                    </td>
                                    <td class="text-start px-2">
                                        {{ ucfirst(strtolower($incident->detail_inc)) }}
                                    </td>
                                    <td class="text-center px-2">
                                        {{ $incident->status->status_name_pst }}
                                    </td>
                                    <td class="text-center px-2">
                                        {{ $incident->project->status->status_name_pst }}
                                    </td>
                                    <td class="text-center px-2">
                                        @if ($incident->incident_type_inc)
                                            {{ $incidentTypes[$incident->incident_type_inc] }}
                                        @endif
                                    </td>
                                    <td class="px-2">
                                        @if ($incident->author)
                                            {{ $incident->author->fullName }}
                                        @endif
                                    </td>
                                    <td class="px-2">
                                        <p class="mb-0">
                                            @if ($incident->created_at)
                                                {{ $incident->created_at->format('Y') == date('Y')? ucwords($incident->created_at->translatedFormat('l d F')): ucwords($incident->created_at->translatedFormat('l d F Y')) }},
                                                {{ $incident->created_at->diffForHumans() }}
                                            @else
                                                {{ $incident->createdon_inc->format('Y') == date('Y')? ucwords($incident->createdon_inc->translatedFormat('l d F Y')) : ucwords($incident->createdon_inc->translatedFormat('l d F Y')) }},
                                                {{ $incident->createdon_inc->diffForHumans() }}
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2">
                <div class="row">
                    <div class="col-md-12">
                        {{ $incidents->links(data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.col-->
</div>
