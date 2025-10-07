<div class="modal fade" id="modalViewSatDetails" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl-custom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"><i class="fa-solid fa-info-circle"></i> SAT Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text w-50">Device Name</span>
                            <input type="text" class="form-control" id="txtSatDeviceNameView" name=""  readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text w-50">Operations Line</span>
                            <input type="text" class="form-control" id="txtOpLineView" name=""  readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text w-50">Assembly Line</span>
                            <input type="text" class="form-control" id="txtAssyLineView" name=""  readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text w-50">No. of Pins</span>
                            <input type="number" min="0" class="form-control" id="txtNoPinsView" name="" readonly>
                        </div>
                    </div>
                        <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text w-50">QSAT</span>
                            <input type="number" min="0" class="form-control" id="txtQsatView" name="" readonly>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="observationTab" data-bs-toggle="tab"
                            data-bs-target="#observation" type="button" role="tab" aria-controls="observation"
                            aria-selected="true">Observation</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="lineBalanceTab" data-bs-toggle="tab" data-bs-target="#lineBalance"
                            type="button" role="tab" aria-controls="lineBalance" aria-selected="false">Line
                            Balance</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="observation" role="tabpanel"
                        aria-labelledby="observationTab">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm w-100"
                                        id="tableViewSATObservation">
                                        <thead>
                                            <tr>
                                                {{-- <th rowspan="2">Action</th> --}}
                                                <th rowspan="2">Process</th>
                                                <th rowspan="2">Operator</th>
                                                <th colspan="5" class="text-center">Observation (sec. per cycle-unit)
                                                </th>
                                                <th rowspan="2">Observed Time (secs.)</th>
                                                <th rowspan="2">Allowance Factor (%)</th>
                                                <th rowspan="2">Normal Time (secs.)</th>
                                                <th rowspan="2">Standard Time (secs.)</th>
                                                <th rowspan="2">UPH</th>
                                            </tr>
                                            <tr class="text-center">
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="9" class="text-end">Total</th>
                                                <th id="totalNormalTimeView">0</th>
                                                <th id="totalStandardTimeView">0</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane fade mt-3" id="lineBalance" role="tabpanel" aria-labelledby="lineBalance">
                        <div class="card" id="id">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-12 d-flex justify-content-around">

                                         <div>
                                            Assembly SAT:
                                            <label id="assySatView"> 0</label>
                                        </div>
                                         <div>
                                            Line Balance (%):
                                            <label id="lineBalView"> 0</label>
                                        </div>
                                         <div>
                                            Output/Hr:
                                            <label id="outputPerHrView"> 0</label>
                                        </div>
                                        <div>
                                            Total Station SAT:
                                            <label id="TtlStationSatView"> 0</label>
                                        </div>
                                        <div>
                                            Total No. of Operators:
                                            <label id="ttlNoOperatorView"> 0</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered w-100" id="tableViewLineBalance">
                                        <thead>
                                            <tr>
                                                <th>Process</th>
                                                <th>Station SAT</th>
                                                <th>No. of Operators</th>
                                                <th>TACT</th>
                                                <th>UPH</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <div id="footerButton"></div>
            </div>
        </div>
    </div>
</div>