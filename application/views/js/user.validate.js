$(document).ready(function() {
	jQuery.validator.addMethod("mobile", function(value, element) { 
		var length = value.length; 
		var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/ 
		return this.optional(element) || (length == 11 && mobile.test(value)); 
		}, "手机号码格式错误"); 
     $("#user_form").validate({       //表单id
    	 errorElement: "em",
    	 errorClass: "text-error",
          rules: {
               username: {
                    required: true
               },
               password:{
            	   required:true
               },
               confirm_password:{
            	   equalTo:"#password"
               }
          },
          messages: {
               username: {
                    required: "英文ID不能为空!"
               },
               password: {
                    required: "密码不能为空!"
               },
               confirm_password:{
            	    equalTo:"两次输入密码不一致!"
               }
          }
     });
     
     
     
}); 