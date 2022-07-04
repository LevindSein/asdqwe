@extends('layouts.app')

@section('content')
<div class="content animated fadeIn">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Edit Job Vacancy</h4>
            {{ Breadcrumbs::render('input-job') }}
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('layouts.partials.alert')
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Edit Job Vacancy</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('job.submit_edit') }}" method="POST" id="pengajuan_ptk" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $pengajuan->id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Jabatan (Job Title) <span class="required-label">*</span></label>
                                        <select name="job_id" class="form-control selectpicker" required>
                                            <option></option>
                                            @foreach ($job_titles as $item)
                                                <option value="{{ $item->id }}" {{ $pengajuan->job_id == $item->id ? 'selected':'' }}>{{ $item->job_title_code.'-'.$item->job_title_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Level Jabatan (Position Level) <span class="required-label">*</span></label>
                                        <select name="level_id" class="form-control selectpicker" required>
                                            <option></option>
                                            @foreach ($grade_titles as $item)
                                                <option value="{{ $item->id }}" {{ $pengajuan->level_id == $item->id ? 'selected':'' }}>{{ $item->grade_title_code.'-'.$item->grade_title_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Departemen (Department) <span class="required-label">*</span></label>
                                        <select name="dept_id" class="form-control selectpicker" required>
                                            <option></option>
                                            @foreach ($departments as $item)
                                                <option value="{{ $item->id }}" {{ $pengajuan->dept_id == $item->id ? 'selected':'' }}>{{ $item->department_code.'-'.$item->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Lokasi Kerja (Working Location) <span class="required-label">*</span></label>
                                        <select name="region_id" class="form-control selectpicker" required>
                                            <option></option>
                                            @foreach ($company_regions as $item)
                                                <option value="{{ $item->id }}" {{ $pengajuan->region_id == $item->id ? 'selected':'' }}>{{ $item->region_city }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Kualifikasi Umum<span class="required-label">*</span></label>
                                        <br>
										@foreach ($job_req as $item)
											{{ $loop->iteration }}.
                                            <input type="text" name="JobReq[]" placeholder="Tulis Disini" style="width: 90%" value="{{ $item->JobReq }}"><br>
                                        @endforeach                                       
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Waktu Kerja <span class="required-label">*</span></label>
                                        <br>
                                        @foreach ($workingTime as $item)
                                            <input type="radio" name="working_time" value="{{ $item->lookup_value }}" required 
                                            {{ $pengajuan->working_time == $item->lookup_value ? 'checked':'' }}> {{ $item->lookup_desc }}
                                            <br>
                                        @endforeach
                                    </div>                                    
                                    <div class="form-group">
                                        <label for="">Jenis Kelamin</label>   
										<select name="gender" class="form-control selectpicker" required>
                                            <option></option>
                                                <option value="L">Pria</option>
												<option value="P">Wanita</option>
												<option value="B">Pria / Wanita</option>
                                        </select>										
                                    </div> 
									<div class="form-group">
                                        <label for="">Kualifikasi Khusus<span class="required-label">*</span></label>
                                        <br>
										@foreach ($job_spec as $item)
											{{ $loop->iteration }}.
                                            <input type="text" name="JobSpec[]" placeholder="Tulis Disini" style="width: 90%" value="{{ $item->JobSpec }}"><br>
                                        @endforeach                                       
                                    </div>									
									<div class="form-group">
                                        <label for="">Uraian Pekerjaan <span class="required-label">*</span></label>
                                        <br>
                                        @foreach ($job_desc as $item)
                                            {{ $loop->iteration }}. 
                                            <input type="text" name="JobDesc[]" placeholder="Tulis Disini" style="width: 90%" value="{{ $item->JobDesc }}"><br>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-12">                                   
                                    <div class="form-group">
                                        <label for="">Catatan</label>
                                        <textarea name="Notes" cols="30" rows="4" class="form-control">{{ $pengajuan->Notes }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-md pull-right"><i class="fas fa-save"></i> Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    var dt = $('#training-submissions-table').dataTable({
        // responsive: true,
    }).api();
</script>
@endsection