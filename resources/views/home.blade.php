@extends('layouts.main')
@section('section')
<div class="row justify-content-center align-items-center ">
    <div>
        <h2 class="heading">Input Jumlah Variabel dan Kendala</h2>
    </div>
    <div class="container mt-4 text-dark">
        <div class="card w-50">
            <div class="card-body">
                <form action="/input" method="post">
                @csrf
                <div class="form-group row mb-2">
                    <label for="var" class="col-md-4 col-form-label text-dark">Banyak Variabel Keputusan</label>
                    <div class="col-md-8">
                        <input type="number" class="form-control" name="var" id="var"/>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="cons" class="col-md-4 col-form-label text-dark">Banyak Kendala</label>
                    <div class="col-md-8">
                        <input type="number" class="form-control" name="cons" id="cons"/>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="w-100 btn btn-primary mt-4" name="submit">Lanjut</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
