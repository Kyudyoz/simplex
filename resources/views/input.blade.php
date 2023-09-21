@extends('layouts.main')
@section('section')
<div id="header">
    <h2 class="heading">Input Nilai</h2>
</div>
<div id="body" class="container mt-4">
    <p><a href="/" class="btn btn-warning">Kembali</a></p>
    <form action="/hitung" method="post">
        @csrf
        <input type="hidden" name="numbers" value="{{ $var }},{{ $cons }},">
        <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="category" id="max" value="max" checked="checked">
            <label class="form-check-label" for="max">Maksimum</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="category" id="min" value="min">
            <label class="form-check-label" for="min">Minimum</label>
        </div>
        <br>
        <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="metode" id="simplex" value="simplex" checked="checked">
            <label class="form-check-label" for="simplex">Simplex</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="metode" id="aljabar" value="aljabar">
            <label class="form-check-label" for="aljabar">Aljabar</label>
        </div>
        <div class="form-group mt-3 mb-3">
            <label for="fungsiTujuan">Fungsi Tujuan</label>
            <div class="input-group">
                @for($i = 1; $i <= $var ; $i++)
                <input type="number" class="form-control" name="variabel{{$i}}" style="text-align:right;" placeholder="X{{$i}}">
                @if($i != $var)
                <div class="input-group-append">
                    <span class="input-group-text mx-1">+</span>
                </div>
                @endif
                @endfor
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="fungsiKendala">Fungsi Kendala</label>
            @for($k = 1; $k <= $cons ; $k++)
            <div class="input-group mb-3">
                @for($j = 1; $j <= $var; $j++)
                <input type="number" class="form-control" name="kendala{{$k}}{{$j}}" style="text-align:right;" placeholder=' X{{$j}}'>
                @if($j != $var)
                <div class="input-group-append">
                    <span class="input-group-text mx-1">+</span>
                </div>
                @endif
                @endfor
                <select class="form-select mx-2" name="tanda{{$k}}">
                    <option value="kurangDari"><=</option>
                    <option value="lebihDari">>=</option>
                </select>
                <input type="number" class="form-control" name="SA{{$k}}" placeholder="Solusi Awal">
            </div>
            @endfor
        </div>
        <div class="button" style="display: grid; place-items: center">
            <button type="submit" class="w-50 btn btn-primary mt-3" name="submit">Hitung</button>
        </div>
    </form>
</div>
@endsection
