var disablePhotoFields = function (){
	var elements = $(".iterator")
	if(selectedAlbum.phaId == 0){
		if($("#novo-album").attr("data-acl") == "true"){
			$("#novo-album").removeClass("link-disable");
		}
		//Começa no indice i = 1, porque o link de novo-album já foi tratado acima
		for (var i = 1; i < elements.length; i++) {
			var element = $('#' + elements[i].id);
				element.addClass("link-disable");
		}
	}else{
		for (var i = 0; i < elements.length; i++) {
			var element = $('#' + elements[i].id);
			if(element.attr("data-acl") == "true"){
				element.removeClass("link-disable");
			}
		}
	}
};
var selectNewAlbum=function(selectEl, publicArea) {
	var alb=selectEl[selectEl.selectedIndex];
	$('#photo_album_label').html(alb.innerHTML);
	selectedAlbum.phaId = alb.value;
	selectedAlbum.albumName = alb.innerHTML;
	disablePhotoFields();
	if(!publicArea){
		//Verificar se relamente será dessa maneira o acomplamento dinâmico de eventos
		if (!($('#remover-album').attr("data-acl") == "false")){
			document.getElementById('remover-album').onclick = function(){
				openModalRemove('Remover album', selectedAlbum.albumName, selectedAlbum.phaId, 'removeAlbum()');
			}
		}
		if (!($('#renomear-album').attr("data-acl") == "false")){
			document.getElementById('renomear-album').onclick = function(){
				openModalRename('Renomear album', selectedAlbum.albumName, selectedAlbum.phaId, 'renameAlbum()');
			}
		}
		if (!($('#enviar-fotos').attr("data-acl") == "false")){
			document.getElementById('enviar-fotos').onclick = function(){
				openModalIframe('Realizar upload de fotos','/photo/addPhoto', selectedAlbum.phaId, null);
			}
		}
	}
	if(selectedAlbum.phaId != 0)
		loadPhotos(alb.value);
	else{
		if(carrosel.options)
			resetCarousel("Selecione um álbum...");
		else
			loadPhotos(alb.value);
	}
};

var resetCarousel=function(msg){
	var html='';
	html+='<div id="loading_photo" style="display: none;text-align: center;"><img alt="loading..." src="'+basePath+'/img/ajax-loader.gif"></div>';
	html+='<div id="msg_album" style="display: block;text-align: center;"><h3>'+ msg +'</h3></div>';
	$('#content_photos').html(html);
	$('#left_control').hide();
	$('#right_control').hide();
};

var afterOnLoad=function(){
	loadPhotos(selectedAlbum.phaId);

	if($('#combo_box_albuns')){
		var selectedText = $('#combo_box_albuns :selected').text()
		if($('#photo_album_label').html() != selectedText){
			$('#photo_album_label').html(selectedText);
		}
	}
};

var loadPhotos=function(phaId) {
	resetCarousel('Selecione um álbum...');
	var timestamp = new Date().getTime();

	var url = '';
	
	if(carrosel.options) {
		url ='/storage/listPhotos?t=' + timestamp;
	} else {
		if(phaId != undefined && phaId != 0)
			url ='/projetos/photos?t=' + timestamp;
		else
			url ='/projetos/recentPhotos?prj='+carrosel.urlBase+'&t='+timestamp;
	}
	url=basePath + url;
	var data={ id: phaId };
	$('#loading_photo').show();
	$('#msg_album').hide();

	var success=function(responseJSON){
		if(responseJSON.status) {
			if(responseJSON.photos){
				if(responseJSON.photos.length<=0){
					$('#msg_album').show();
					$('#msg_album').html("<h3>Este álbum não possui fotos.</h3>");
					$('#loading_photo').hide();
				}else
					insertPhotosInCarousel(responseJSON.photos);
			}
			else{
				$('#msg_album').show();
				$('#msg_album').html("<h3>Selecione um álbum...</h3>");
				$('#loading_photo').hide();
			}
		}else if (!responseJSON.isLogged)
			document.location.reload(true);
		else{
			$('#msg_album').show();
			$('#msg_album').html("<h3>"+responseJSON.msg+"</h3>");
			$('#loading_photo').hide();
		}
	};
	var fail=function(){
		showAjaxErrorMessage('Falhou ao conectar com o servidor.');
	};
	var always=function(){
		/*$('loading_photo').hide();
		$('msg_album').hide();*/
	};
	$.ajax({
		dataType: "json",
		url: url,
		data: data,
		success: success,
		error: fail,
		complete: always
	});
};

var insertPhotosInCarousel=function(photos) {
	var MAX_PER_PAGE=carrosel.max_photo;// Max photos per page.
	var total_len=photos.length;
	var len=total_len;
	var total_pags=1;
	if(total_len>MAX_PER_PAGE){
		total_pags=parseInt(total_len/MAX_PER_PAGE);
		len=MAX_PER_PAGE;
	}
	var html='';
	if(len) {
		var activeItem=true;
		var i=0;
		var pags=0;
		do{
			if( i>=len ) break;
			html+='<div id="pag_'+pags+'" class="item'+((activeItem)?(' active'):(''))+'"><div class="row">';// .item.active
			if(!selectedAlbum.selectedPhotos) selectedAlbum.selectedPhotos={};
			selectedAlbum.selectedPhotos['pag_'+pags]=false;//controle de seleção dos checkboxs

			while( i<len ) {
				var url=photos[i].url;
				var pho_id=photos[i].id;
				var isPublic=photos[i].isPublic;
				var strPublic=((isPublic)?("pública"):("privada"));
				html+='<div class="thumb" id="thumb_'+pho_id+'">';
					if(carrosel.options) {
						html+='<input name="photos" type="checkbox" class="pull-right" value="'+pho_id+'" />';
						html+='<a class="glyphicon pull-left'+((isPublic)?(' glyphicon-globe'):(''))+'" id="icon_'+pho_id+'"></a>';
					}
				html+='<a class="thumbnail" href="javascript:openPhotoModal(\'\',\'/storage/getPhoto\','+pho_id+');">';
				html+='<img class="img-responsive" src="'+url+'" title="Foto de visualização '+strPublic+'." alt="Foto de visualização '+strPublic+'." />';
				html+='</a>';
				html+='</div>';
				i++;
			}
			html+='</div></div>';
			pags++;
			if(pags*MAX_PER_PAGE < total_len && (total_len-(pags*MAX_PER_PAGE))<MAX_PER_PAGE){
				len+=(total_len-(pags*MAX_PER_PAGE));
			}else{
				if(pags*MAX_PER_PAGE < total_len) len+=MAX_PER_PAGE;
			}

			activeItem=false;
		}while(pags<=total_pags);
		$('#content_photos').html(html);
		if(pags > 1){
			$('#left_control').show();
			$('#right_control').show();
		}
	}
};
var removePhotos = function(route, action) {
	var list = $('#remove-id').val();

	closeModal();

	var url = basePath + "/storage/removePhotos";
	var data = {
		photo_ids : list
	};
	var htmlPhotos = "";
	//if (!$('#loading_photo')) {
		htmlPhotos = $('#content_photos').html();// armazenando fotos para
													// restaurar a lista
		resetCarousel('Este álbum não possui fotos.');
	//}
	$('#loading_photo').show();
	$('#msg_album').hide();

	var success = function(responseJSON) {
		if (responseJSON.status) {
			$('#content_photos').html("");
			$('#content_photos').append(htmlPhotos);
			$('#left_control').show();
			$('#right_control').show();
			// removendo fotos
			removePhotoThumb(list);
			showAjaxSuccessMessage(responseJSON.msg);
		} else if (!responseJSON.isLogged){
			document.location.reload(true);
		} else {
			$('#msg_album').show();
			$('#msg_album').html( "<h3>" + responseJSON.msg + "</h3>");
			$('#loading_photo').hide();
		}
	};
	var fail = function() {
		if ($('#loading_photo'))
			$('#loading_photo').hide();
		showAjaxErrorMessage('Falhou ao conectar com o servidor.');
		$('#content_photos').html("");
		$('#content_photos').append(htmlPhotos);
		$('#left_control').show();
		$('#right_control').show();
	};
	$.ajax({
		dataType : "json",
		url : url,
		data : data,
		success : success,
		error : fail,
	});
};

var removeAlbum = function() {
	var albumId = selectedAlbum.phaId;
	closeModal();
	var url = basePath + "/album/removeAlbum";
	var data = {
		id : albumId
	};
	var success = function(responseJSON) {
		if (responseJSON.status) {
			document.location.reload(true);
		}
	};
	var fail = function() {
		showAjaxErrorMessage('Falhou ao conectar com o servidor.');
	};
	$.ajax({
		dataType : "json",
		url : url,
		data : data,
		success : success,
		error : fail,
	});
};
var renameAlbum = function() {
	var albumId = selectedAlbum.phaId;
	var newName = $('#newName').val();
	selectedAlbum.albumName = newName;
	var match = regexName(newName);
	if (match != null && match[0].trim() == newName.trim()) {
		var url = basePath + "/album/renameAlbum";
		var data = {
			newName : newName,
			id : albumId
		};
		
		closeModal();
		
		var success = function(responseJSON) {
			document.location.reload(true);
		};
		var fail = function() {
			showAjaxErrorMessage('Falhou ao conectar com o servidor.');
		};
		$.ajax({
			dataType : "json",
			url : url,
			data : data,
			success : success,
			error : fail,
		});
	}
	else {
		$("#modal-rename-error").html("Somente os seguintes caracteres são permitidos: a-z, A-Z, 0-9, '(', ')', '/', '.'");
	}
};

var getSelectedPhotos = function() {
	var checkboxes = $('[name="photos"]');
	var listPhotos = "";
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) {
			listPhotos += ((listPhotos == "") ? ("") : (","))
					+ checkboxes[i].value;
		}
	}
	return listPhotos != "" ? listPhotos : null;
};

var selectAllPhotos = function() {
	var activeItem = $('.item.active');
	var checkboxes = activeItem.find('input');
	selectedAlbum.selectedPhotos[activeItem.attr('id')] = (!selectedAlbum.selectedPhotos[activeItem
			.attr('id')]);
	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = selectedAlbum.selectedPhotos[activeItem
				.attr('id')];
	}
};

var switPhotoIcon = function(ids) {
	if (ids == undefined || ids == "")
		return false;
	ids = ids.split(',');
	var len = ids.length;
	for (var i = 0; i < len; i++) {
		var aIcon = $('#icon_' + ids[i]);
		if (!aIcon.hasClass('glyphicon-globe'))
			aIcon.addClass('glyphicon-globe');
		else
			aIcon.removeClass('glyphicon-globe');
	}
};

var removePhotoThumb = function(ids) {
	if (ids == undefined || ids == "")
		return;
	ids = ids.split(',');
	var len = ids.length;
	for (var i = 0; i < len; i++) {
		$('#thumb_' + ids[i]).remove();
	}
	var listContents = $('#content_photos').contents();
	len = listContents.length;
	for (var i = 0; i < len; i++) {
		if (listContents.get(i).childNodes[0].childElementCount == 0) {
			listContents[i].remove();
		}
	}
	// atualizando a lista e indicando um novo item como ativo
	listContents = $('#content_photos').contents();
	len = listContents.length;
	if (!len) {
		resetCarousel('Este álbum não possui fotos.');
		return;
	}else{
		$('#content_photos').html();
	}
	var hasActive = false;
	for (var i = 0; i < len; i++) {
		if ($('#'+listContents[i].id).hasClass('active')) {
			hasActive = true;
			break;
		}
	}
	if (!hasActive)
		listContents[0].className = 'item active';
};

var setAccessPhoto = function() {
	var list = getSelectedPhotos();

	if (!list) {
		$("#photoError").modal('show');

	} else {

		var url = basePath + "/storage/setAccessPhotos";
		var data = {
			photo_ids : list
		};
		var htmlPhotos = "";
		//if (!$('#loading_photo')) {
			htmlPhotos = $('#content_photos').html();// armazenando fotos
														// para restaurar a
														// lista
			resetCarousel('Este álbum não possui fotos.');
		//}
		$('#loading_photo').show();
		$('#msg_album').hide();

		var success = function(responseJSON) {
			if (responseJSON.status) {
				$('#content_photos').html("");
				$('#content_photos').append(htmlPhotos);
				$('#left_control').show();
				$('#right_control').show();
				// trocando o icone de publica privada
				switPhotoIcon(list);
				showAjaxSuccessMessage(responseJSON.msg);
			} else if (!responseJSON.isLogged){
				document.location.reload(true);
			} else {
				$('#msg_album').show();
				$('#msg_album').html("<h3>" + responseJSON.msg + "</h3>");
				$('#loading_photo').hide();
			}
		};
		var fail = function() {
			if ($('#loading_photo'))
				$('#loading_photo').hide();
			showAjaxErrorMessage('Falhou ao conectar com o servidor.');
			$('#content_photos').html("");
			$('#content_photos').append(htmlPhotos);
			$('#left_control').show();
			$('#right_control').show();
		};
		$.ajax({
			dataType : "json",
			url : url,
			data : data,
			success : success,
			error : fail,
		});
	}
};

var checkSelected = function() {
	var list = getSelectedPhotos();

	if (!list) {
		$("#photoError").modal('show');
	} else {
		openModalRemove('Remover fotos selecionadas','As fotos selecionadas ',list, 'removePhotos()');
	}
}
