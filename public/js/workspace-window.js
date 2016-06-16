function abstractCallAjax(url,successFunc,method, data, beforeSendFunction){
	$.ajax({
	    beforeSend: beforeSendFunction,
	    type: method,  
	    url: url,
	    data: data, 
	    success: successFunc,
	    error: function(XMLHttpRequest, textStatus, errorThrown) { 
	    	document.location.reload(true);
	    }       
	});
}

var showAjaxSuccessMessage = function (msg, appendMsg){
    if(appendMsg)
        $("#ajax_workspace_success_msg").append( "<div>" + msg + "</div>" );
    else
        $("#ajax_workspace_success_msg").html(msg);
    $("#ajax_workspace_success").fadeIn(); 
    $("#ajax_workspace_error").hide();
    $(".flash-messages").remove();
};

var showAjaxErrorMessage = function (msg, appendMsg){
    if(appendMsg)
    	$("#ajax_workspace_error_msg").append( "<div>" + msg + "</div>" );
    else
        $("#ajax_workspace_error_msg").html(msg);
    $("#ajax_workspace_error").fadeIn();
    $("#ajax_workspace_success").hide();
    $(".flash-messages").remove();
};


var regexName = function(newName) {
	var regexGetNameDoc = /([^\'\"]+)+/;
	var match = regexGetNameDoc.exec(newName);
	return match;
};

var generateReport = function() {
	$('#loadingReport').show();
	var current_prj = $('#currentPrj').val();
	var coordInpe = $('#coordInpe').val();

	var url = basePath + "/report/generateReport";
	
	var requisitions = document.getElementsByName("checkedRequisitions[]");
	
	var cont = 0;
	
    var checkedRequisitions = [];
    
    $('input[name="checkedRequisitions[]"]:checked').each(function () {
    	checkedRequisitions[cont] = this.value;
    	cont++;
    });
    
	var data = {
		"checkedRequisitions":checkedRequisitions,
		"currentPrj":current_prj,
		"coordInpe":coordInpe
	};
	
	var success = function(responseJSON) {
		if (responseJSON.status) {
			openGenerateReportModal(responseJSON.reportId, true, 0);
			$('#loadingReport').hide();
		} else if (!responseJSON.isLogged) {
			document.location.reload(true);
		} else if(!responseJSON.permitted){
			showAjaxErrorMessage('Você não possui permissões para realizar essa operação.');
		} else{
			showAjaxErrorMessage('Não foi possível gerar o relatório.');
		}
	};

	var fail = function() {
		showAjaxErrorMessage('Falhou ao conectar com o servidor.');
	};
	$.ajax({
		dataType : "json",
		url : url,
		type: "post",
		data: data,
		success : success,
		error : fail
	});
};

var openGenerateReportModal = function(reportId, reloadOnClose, uploaded){
	$("#modal-generate-report").modal({backdrop: "static"});
	$("#modal-generate-report").modal('show');
	$("#btnDownload").prop('href', 'javascript:downloadReport('+ uploaded +')');
	$("#btnOpen").prop('href', 'javascript:openReport('+ uploaded +')');
	$("#closeGenReport").prop('href', 'javascript:closeModal(' + reloadOnClose +')');
	$('#reportId').val(reportId);
}

var openReport = function(uploaded) {
	var url = "/report/downloadReport?reportId=" + $('#reportId').val()+"&open=1&uploaded="+uploaded;
	window.open(basePath + url);
	location.reload(true);
};

var downloadReport = function(uploaded) {
	var url = "/report/downloadReport?reportId=" + $('#reportId').val()+"&download=1&uploaded="+uploaded;
	window.open(basePath + url);
	location.reload(true);
};

var showLoading = function(doc_id){
	$('.btn').prop('class', 'link-hide');
	$('#loading_'+doc_id).show();
};

var closeModal = function(reload){
	$("#modal-iframe-body").prop('src', '');
	$('.modal').modal('hide');
	if(reload)
		document.location.reload(true);
}
