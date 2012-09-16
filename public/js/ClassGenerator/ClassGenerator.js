$(document).ready(function(){
	GeneratorForm.init();	
});

var GeneratorForm = {
	init: function(){
		GeneratorForm.persistenceOptionCheck();
		GeneratorForm.persistenceOptionTrigger();
		GeneratorForm.addAttributeTrigger();
		GeneratorForm.resultDialogboxCheck();
	},
	getPersistenceFields: function(){
		return $("#class-table," +
				"dt#table-label," +
				"#class-attributes-column," +
				"label[for=class-attributes-column]," +
				"#class-attributes-serial," +
				"label[for=class-attributes-serial],"+
				"#class-attributes-pkey," +
				"label[for=class-attributes-pkey]"
		);
	},
	addAttributeTrigger: function(){
		$("#addAttribute").click(function(){ 
			GeneratorForm.ajaxAddAttribute();
		});
	 
		$("#removeAttribute").click(function(){
			GeneratorForm.removeAttribute();
		});
	},
	persistenceOptionTrigger: function(){
		$("#class-withPersistence").click(function(){
			GeneratorForm.persistenceOptionCheck();
		});
	},
	persistenceOptionCheck: function (){
		if($("#class-withPersistence").is(":checked")){
			GeneratorForm.getPersistenceFields().show();
			GeneratorForm.getPersistenceFields().find("input,textarea").val('');
		} else {
			GeneratorForm.getPersistenceFields().hide();
		}
	},
	resultDialogboxCheck: function(){
		if($("#classGenerator div.resultBox").length > 0){
			$("#classGenerator div.resultBox").dialog({
				minHeight: 680,
				width: 800,
				modal:true,
				title:'Get your persistent class:'
			});
		}
	},
	getAddAttributeId: function(){
		//Get value of id - integer appended to dynamic form field names and ids
		return $("#addFieldDynId").val();
	},
	setAddAttributeId: function(id){
		//Set value of id - integer appended to dynamic form field names and ids
		$("#addFieldDynId").val(id);
	},
	ajaxAddAttribute: function(){
		$.ajax(
		    {
		      type: "POST",
		      url: "/index/addattribute/format/html ",
		      data: {'id':GeneratorForm.getAddAttributeId()},
		      success: function(newElement) {
		 
		    	$(newElement).prepend('<span class="ui-icon ui-icon-error" style="float:right;"></span>');
		        // Insert new element before the Add button
		        var addedElement = $("#addAttribute").parent().prev().before(newElement);
		       		 
		        // Increment and store id
		        $("#addFieldDynId").val(++id);
		      }
		    }
		  );
	},
	removeAttribute: function(){
		// Get the last used id
		var lastId = GeneratorForm.getAddAttributeId() - 1;
		 
		// Build the attribute search string.  This will match the last added  dt and dd elements.  
		// Specifically, it matches any element where the id begins with 'newName<int>-'.
		searchString = '*[id^=newName' + lastId + '-]';
		 
		// Remove the elements that match the search string.
		$(searchString).remove()
		 
		// Decrement and store id
		GeneratorForm.setAddAttributeId(id);
	}
}