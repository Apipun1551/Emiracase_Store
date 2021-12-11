@extends('admin.layout')

@section('content')
<!-- kondisi untuk title -->
@php
    $formTitle=!empty($category) ? 'Update' : 'New' //jika objek categori tidak kosong maka update dan sebaliknya
@endphp
<!-- Form -->
<div class="content">
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-default">
                <div class="card-header card-header-border-bottom">
                    <h2>{{$formTitle}} Category</h2>
                </div>
                <div class="card-body">
                    @include('admin.partials.flash',['$errors'=>$errors]) <!-- menampilkan view pesan  -->
                    @if (!empty($category)) <!-- tidak memiliki kategori -->
                        {!! Form::model($category,['url' => ['admin/categories',$category->id],'method'=>'PUT']) !!}
                        {!! Form::hidden('id') !!}
                    @else
                        <!-- Jika kosong masuk ke view -->
                        {!! Form::open(['url' => 'admin/categories']) !!}
                    @endif
                        <div class="form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', null, ['class'=>'form-control','placeholder'=>'category name']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('parent_id', 'Parent') !!}
                            {!! General::selectMultiLevel('parent_id', $categories, ['class' => 'form-control', 'selected' => !empty(old('parent_id')) ? old('parent_id') : (!empty($category['parent_id']) ? $category['parent_id'] : ''), 'placeholder' => '-- Pilih Kategori --']) !!}
                        </div>
                        <div class="form-footer pt-5 borter-top">
                            <button type="submit" class="btn btn-primary btn-default"> Simpan </button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
