var wpOBSWidgetInit = function(parent){
	jQuery('input.wp-obs-all-services').click(function(){		
		if(jQuery(this).attr('checked') == 'checked'){
			jQuery(this).parents('div[id*="wpobs_widget"]').find('.wp-obs-service').each(function(i){
				jQuery(this).removeAttr('checked');
			});
			jQuery(this).parents('div[id*="wpobs_widget"]').find('.wp-obs-book-now').each(function(i){
				jQuery(this).removeAttr('checked');
			});	
		}
	});
	
	jQuery('input.wp-obs-book-now').click(function(){		
		if(jQuery(this).attr('checked') == 'checked'){
			jQuery(this).parents('div[id*="wpobs_widget"]').find('.wp-obs-service').each(function(i){
				jQuery(this).removeAttr('checked');
			});
			jQuery(this).parents('div[id*="wpobs_widget"]').find('.wp-obs-all-services').each(function(i){
				jQuery(this).removeAttr('checked');
			});	
		}
	});
	
	jQuery('input.wp-obs-service').click(function(){
		jQuery(this).parents('div[id*="wpobs_widget"]').find('.wp-obs-all-services').each(function(i){
			jQuery(this).removeAttr('checked');
		});
		jQuery(this).parents('div[id*="wpobs_widget"]').find('.wp-obs-book-now').each(function(i){
			jQuery(this).removeAttr('checked');
		});
	});
	
	if(parent == ''){
		jQuery('#widgets-right .wp-obs-title-color, #widgets-right .wp-obs-link-color').wpColorPicker({
			defaultColor: false,
			palettes: true
		});
	}else{
		jQuery('#widget-' + parent + '-title_color, #widget-' + parent + '-link_color').wpColorPicker({
			defaultColor: false,
			palettes: false
		});	
	}
	
}
jQuery(document).ready(function(){
	wpOBSWidgetInit('');	
});

jQuery(document).ajaxSuccess(function(e, xhr, settings) {
	var widget_id_base = 'wpobs_widget';
	if(settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=' + widget_id_base) != -1) {
		var start = settings.data.search('widget-id=');// + ' ' + settings.data.search('add_new'));
		var dataTemp = settings.data.substr(start);
		start = dataTemp.indexOf('=')
		var end = dataTemp.indexOf('&');	
		
		wpOBSWidgetInit(dataTemp.substring(start + 1, end));
	}
});