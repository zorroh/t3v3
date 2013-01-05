var onhover=false;
var stimmer;
var cur_url =  window.location.toString();
var themelist ={'simply':'http://www.themebrain.com/sites/all/modules/pubdlcnt/pubdlcnt.php?file=/sites/default/files/attachments/tb_simply.zip&nid=39',
				'blog':'http://www.themebrain.com/sites/all/modules/pubdlcnt/pubdlcnt.php?file=/sites/default/files/attachments/tb_blog.zip&nid=40',
				'palicico':'http://www.themebrain.com/sites/all/modules/pubdlcnt/pubdlcnt.php?file=/sites/default/files/attachments/tb_palicico.zip&nid=41'
				};
jQuery(function($){

	$('.tb_frame').height($(window).height()-55);
	$(window).resize(function(){
	$('.tb_frame').height($(window).height()-55);
	});

	
	  $('.tb_submenu a').click(function(){$('.tb_drop').click();});
		$('.tb_drop').toggle(showsub,hidesub);
	 
	// $('.tb_drop').hover(function(){
	   // if($('.tb_submenu:animated').length>0) return;
	    // if(onhover)
	    // {
	      // clearTimeout(stimmer);
	      // return;
	     // }
	     // onhover=true;
	     // $('.tb_submenu').show('slide',{direction:'up'},500);
		 // $(this).addClass('active');
	// },
	// function(){
	    // if($('.tb_submenu:animated').length>0) return;
	   // if(onhover){ 
	     // stimmer = setTimeout(function(){$('.tb_submenu').hide('slide',{direction:'up'},500,function(){$('.tb_drop').removeClass('active');});onhover=false; },800);
	   // return;}
	   
	 	// $('.tb_submenu').hide('slide',{direction:'up'});
	 	
	 // }
	// );
	
	// $('.tb_submenu').hover(function(){clearTimeout(stimmer);},
		// function(){
			
			 // stimmer = setTimeout(function(){$('.tb_submenu').hide('slide',{direction:'up'},500,function(){$('.tb_drop').removeClass('active');}); onhover=false;$('.tb_drop').removeClass('active');},800);
		// }
	// );
	process_url();
	setInterval(function(){
	  var new_url =  window.location.toString();
	  
	   if(new_url==cur_url)return;
	    process_url();
	   
	},1000);
	
	$('.btn_close').click(function(){
	 window.location=$('.tb_frame').attr('src');
	 return false;
	});
	 
});

function showsub(){
 var pos = $('.tb_drop').position();
	  $('.tb_submenu').css({top:pos.top+30,left:pos.left-90});
	     $('.tb_submenu').stop();
		 $('.tb_drop').addClass('active');
		  $('.tb_submenu').show('slide',{direction:'up'},300,function(){ });
		 
		  return false;
}
function hidesub(){
	 $('.tb_submenu').stop();
		  $('.tb_submenu').hide('slide',{direction:'up'},300,function(){$('.tb_drop').removeClass('active')});
		  return false;
}
function process_url(){
	cur_url = window.location.toString();
	var newsite = cur_url.split('#')[1];
	if(newsite){
		$('.tb_frame').attr('src','http://demo.themebrain.com/tb_'+newsite);
		$('.tb_drop').text('TB '+newsite);
		$('.btn_download').attr('href',eval('themelist.'+newsite));
		document.title = "TB " +newsite +" | Demo";
		
	}
}


