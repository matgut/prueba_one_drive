@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
    {{ $message }}
	<br>
	<ul>
		<li><p>Link para compartir archivo y editar: <input class="input_links" id="inputLink" value="{{Session::get('link')}}"><button class="btn btn-primary button5" onclick="copyButtonLink()" title="Copiar link!"><i class="far fa-clipboard"></i></button></p></li>
		<li><p>Link para compartir descarga directa: <input class="input_links" id="inputLinksDownloads" value="{{Session::get('linkDonwload')}}"><button class="btn btn-primary button5" onclick="copyButtonLinkDownload()" title="Copiar link!"><i class="far fa-clipboard"></i></button></p></li>
	</ul>
</div>
@endif


@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>	
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>	
	Error desconocido, favor contactar con su administrador
</div>
@endif