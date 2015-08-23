$().ready(function(){

    $("label[for=type-lease]").append("<div id='chooseOwn'></div>");
    $("label[for=type-want]").append("<div id='chooseWant'></div>");
    $("#isBusiness-element").append("<div id='chooseBusiness'></div>");
   $("<div class='dashline'></div>").insertAfter("#fieldset-aboutYou");
    
    $("#type-want").click(function(){
    	$("#address-label,#address-element").hide();
    	$("label[for=rent]").text("最高租金");
    	$("label[for=area]").text("最小面积(平方米)");
    	$("label[for=num_of_room]").text("最少房间数");
    });
    
    $("#type-lease").click(function(){
    	$("#address-label,#address-element").show();
    	$("label[for=rent]").text("租金");
    	$("label[for=area]").text("面积(平方米)");
    	$("label[for=num_of_room]").text("房间数");
    });
   
    if($("#type-lease").attr("checked")){
    	$("#type-lease").click();
    }
    
    if($("#type-want").attr("checked")){
    	$("#type-want").click();
    }
    
    $('#start_date,#stop_date').datePicker({});
    
    $.validator.addMethod('mobileSweden', function(phone_number, element) {
    	return this.optional(element) || phone_number.length > 9 &&
    	phone_number.match(/^((0|\+46)\d{9})$/);
    	}, 'Please specify a valid mobile number');

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
 
    // Method for lease required 
    jQuery.validator.addMethod(
    		"requiredLease",
    		function(value) {
    			console.log($("#type-lease").attr("checked") && value.length < 1);
    			if($("#type-lease").attr("checked") && value.length < 1)
    				return false;
    			return true;
    		}, 
    		"告诉别人您要出租的房子在哪里吧？"
    	);

    
    $("#createAdForm").validate({
    	rules: {
		name: {
			required: true,
			minlength: 2,
			maxlength: 30
		},
		email: {
			required: true,
			email: true
		},
		mobile: {
			mobileSweden: true
		},
		title: {
			required: true,
			minlength: 5,
			maxlength: 100
		},
		address:{
			requiredLease: true
		},
		rent:{
			required: true,
			number:true
		},
		start_date: {
			required: true,
			dateITA: true
		},
		stop_date: {
			dateITA: true
		},
		area:{
			number:true
		},
		num_of_room: {
			number:true
		}
	},
	messages: {
		name: {
			required: "称呼不能为空哦！",
			minlength: "您的称呼这么短呀。。。至少要两个字哦！",
			maxlength: "这。。。来个30字以下的简称吧。。。"
		},
		email:{
			required: "电子邮箱不能为空哦！",
			email: "电子邮箱的格式好像不对耶。。。再检查检查？"
		} ,
		mobile: {
			mobileSweden: "电话号码格式好像不对呀。。。能打通哇。。。"
		},
		title:{
			required: "帖子标题是最醒目的信息哦！",
			minlength: "好短呀。。。至少5个字吧。。。",
			maxlength: "太长了。。。简练下下。。。"
		},
		rent:{
			required: "出个价吧！",
			number:"租金该是个数字吧~~"
		},
		start_date: {
			required: "从哪天开始呢？",
			dateITA: "格式好像不对哦。。。参考2010-10-10"
		},
		stop_date: {
			dateITA: "格式好像不对哦。。。参考2010-10-10，或者留空表示长期"
		},
		area:{
			number:"面积该是个数字吧。。。"
		},
		num_of_room: {
			number:"房间数量。。。"
		}
	}
    });
});