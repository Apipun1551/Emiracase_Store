@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Ops!</strong>
        There are some problems with your input. <br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('succses'))
    <div class="alert alert-succses">{{session('succses')}}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{session('error')}}</div>
@endif
