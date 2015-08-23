$().ready(function(){
		
    $("label[for=type-lease]").css('text-indent','-99999px').append("<div id='chooseOwnSmall'></div>");
    $("label[for=type-want]").css('text-indent','-99999px').append("<div id='chooseWantSmall'></div>");
    $("#search_business-element").append("<div id='onlyBusiness'></div>");
	 
    
    $('#checkin_date,#checkout_date').datePicker({});
    
    $(".itemAction").hide();
    $(".items li").hover(
    		function(){$(this).css("cursor","pointer");$(this).addClass("hover"); },
    		function(){$(this).css("cursor","default");$(this).removeClass("hover");});
    
    $(".items li").click(
    		function(){
    			location = $(this).find("h3 a").attr("href");
    		});
    
    $(".items div.dotline:last").remove();
    
    if($.cookie("notificationHide") != "true"){
    	$("#notification").show();
    } 
    
    $("#closeNotification").click(function(){
		$("#notification").hide();
		$.cookie("notificationHide", "true");
    });
    
    
   /* if($.cookie("searchHide") == "true"){
    	hideSearch();
    } 
    
    function hideSearch(){
		$("#searchSwitch").text("展开搜索").addClass('hide');
		$("#searchSection").hide();
		$.cookie("searchHide", "true");
		}
    
    function expandSearch(){
		$("#searchSwitch").text("隐藏搜索").removeClass('hide');
		$("#searchSection").show();
		$.cookie("searchHide", "false");
		}
    
    
    $("#searchSwitch").click(function(){
    	
    	if($("#searchSwitch").text() == "隐藏搜索"){
    		hideSearch();
    		
    	} else {
    		expandSearch();
    	}   	
    });
    
    $("#searchSwitch").hover(
    		function(){$(this).css("cursor","pointer");},
    		function(){$(this).css("cursor","default");});*/
    		
//  Method for date format yyyy-mm-dd
    jQuery.validator.addMethod(
    		"dateITA",
    		function(value, element) {
    			var check = false;
    			var re = /^\d{4}-\d{2}-\d{2}$/;
    			if( re.test(value)){
    				var adata = value.split('-');
    				var gg = parseInt(adata[2],10);
    				var mm = parseInt(adata[1],10);
    				var aaaa = parseInt(adata[0],10);
    				var xdata = new Date(aaaa,mm-1,gg);
    				
    				if ( ( xdata.getFullYear() == aaaa ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == gg ) )
    					check = true;
    				else
    					check = false;
    			} else
    				check = false;
    			return this.optional(element) || check;
    		}, 
    		"时间不对哦，请按格式  2012-12-12"
    	);
    
    $("#searchForm").validate({
    	rules: {
    	checkin_date: {
			dateITA: true
		},
		checkout_date: {
			dateITA: true
		}
	},
	messages: {
		start_date: {
			dateITA: "格式好像不对哦。。。参考2010-10-10"
		},
		stop_date: {
			dateITA: "格式好像不对哦。。。参考2010-10-10，或者留空表示长期"
		}
	}
    });
    
    
});