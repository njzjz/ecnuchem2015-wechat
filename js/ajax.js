function ajax(url,did,mode){ 
	$.post(url,{ajax:1,mode:mode},function(data){   
		if(data==''){//0 
		}else{      
			$(did).html(data);
		}    
	});
}