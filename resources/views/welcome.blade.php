@extends('layout')

@section('content')
<div class="jumbotron">
  @if(isset($userName))
    <h4>Bienvenido {{ $userName }}!</h4>
    <p>Seleccione un archivo para cargar a oneDrive</p>
    <form action="/uploadfile" method="post" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
       <input type="file" class="form-control-file" name="fileToUpload" id="exampleInputFile" aria-describedby="fileHelp">
       <small id="fileHelp" class="form-text text-muted">Solo cargar archivos hasta 2MB.</small>
      </div>
      <div class="form-group">
        <div class="progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%</div>
        </div>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary">Cargar</button>
      </div>
    </form>
  @else
    <a href="/signin" class="btn btn-primary btn-large">Click para iniciar sesión</a>
  @endif
</div>
@endsection