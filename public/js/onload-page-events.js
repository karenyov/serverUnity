//Realiza AJAX dos detalhamentos das requisições que estão abertas
$(document).ready(function(){ 
	if(typeof selectedRequisitions !== 'undefined' && $.isArray(selectedRequisitions)){ 
		for (index = selectedRequisitions.length - 1; index >= 0; --index) {
			selectNewReq(selectedRequisitions[index], true);
		}
	}
});

$(document).ready(function(){
	//Desabilita botoes da galeria de photos caso na combobox esteja setado "Álbuns"
	if( typeof disablePhotoFields != 'undefined' ){
		disablePhotoFields();
	}
});

$(document).ready(function(){
	if($('#collapseThree').hasClass('in')){
		getRequisitions();
	}
});

//Validação do filtro de data
$(document).ready(function(){
	function verification(event){
		var startDate = $('#date01').val();
		var endDate = $('#date02').val();
		$("#date_filter_button").attr("disabled", true);
		if(!startDate && endDate.length == 10){
			$("#date_filter_button").attr("disabled", false);
			if(event.which == 13){
				changeDate();
			}
		}
		if(!endDate && startDate.length == 10){
			$("#date_filter_button").attr("disabled", false);
			if(event.which == 13){
				changeDate();
			}
		}
		if(startDate.length == 10 && endDate.length == 10){
			//Converte as datas para Inteiros, dessa maneira e possível realizar a comparação
			var startDateToNumber = parseInt(startDate.split("/")[2].toString() + startDate.split("/")[1].toString() + startDate.split("/")[0].toString());
			var endDateToNumber = parseInt(endDate.split("/")[2].toString() + endDate.split("/")[1].toString() + endDate.split("/")[0].toString());
			if(event.which != 13 && startDateToNumber > endDateToNumber){
				$("#date_filter_button").attr("disabled", true);
				$("#msgErroDataInicial").html("Data inicial não pode ser maior que a final");
		    }
			if(startDateToNumber <= endDateToNumber){
				$("#msgErroDataInicial").html("");
				$("#date_filter_button").attr("disabled", false);
				if(event.which == 13){
					changeDate();
				}
			}
		}
	}
	$("#date01").keyup(verification);
	$("#date02").keyup(verification);
});

//Enter Key eventos
$(document).ready(function(){
	$('#newName').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			$('#modal-rename-btn')[0].click();
			return false;
		}
	});
	$('#modal-remove').on('shown.bs.modal', function() {
		$('#modal-remove-btn').focus();
		$('#modal-remove-btn').blur(function(){
			$('#modal-remove-btn').focus();
		});
	});
	$('#modal-alocate-requisition').on('shown.bs.modal', function() {
		$('#allocateBtn').focus();
		$('#allocateBtn').blur(function(){
			$('#allocateBtn').focus();
		});
	});
	$('#photoError').on('shown.bs.modal', function() {
		$('#closePhotoErrorBtn').focus();
		$('#closePhotoErrorBtn').blur(function(){
			$('#closePhotoErrorBtn').focus();
		});
	});
}); 

$(document).ready(function(){
	$('[rel="tooltip"]').tooltip({html:true});
});

$(document).ready(function(){
	reqTool = $('#requisitionTool').val();
	if(reqTool){
		$("[name='requisitionTool']").bootstrapSwitch();
		$("[name='requisitionTool']").on('switchChange.bootstrapSwitch', function() {

		var choice = $("#requisitionTool").is(":checked");
		var callback = null;
		
		if(choice){
			$("#requisitionTool").attr("checked",true);
			data = {"value":1};
		}else{
			$("#requisitionTool").attr("checked",false);
			data = {"value":2};
		}
		
		var success = function(){
			
		};
		
		var fail = function(){
			
		};
		
		$.ajax({
			dataType : "json",
			url : "/configurations/changeRequisitionTool",
			data : data,
			success : success,
			error : fail,
		});
	});
  }
});