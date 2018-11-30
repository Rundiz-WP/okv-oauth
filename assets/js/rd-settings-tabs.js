/**
 * rundiz settings page (wp plugin).
 * tabs functional
 */


// below this part run on page loaded ------------------------------------------------------------------------------------
(function($) {
	if(typeof(Storage) !== "undefined") {
		// get remembered actived tab from localstorage
		remembered_actived_tab_content_id = localStorage.getItem('rd-settings-tabsactive');
		if (remembered_actived_tab_content_id != null && remembered_actived_tab_content_id != '') {
			// set current active tab to this remembered.
			$('.rd-settings-tabs .tab-pane li').removeClass('active');
			$('.rd-settings-tabs .tab-pane').find('a[href=\"'+remembered_actived_tab_content_id+'\"]').closest('li').addClass('active');
		}
		delete remembered_actived_tab_content_id;
	}

	// find currently active tab.
	actived_tab = $('.rd-settings-tabs .tab-pane').find('.active');
	if (actived_tab.length == 0) {
		// not found currently active tab, set first tab as active.
		$('.rd-settings-tabs .tab-pane li').first().addClass('active');
		actived_tab = $('.rd-settings-tabs .tab-pane').find('.active');
	}

	// get target tab content of active tab.
	target_tab_content_id = actived_tab.find('a').attr('href');
	// removed all active tab content and set active tab content that match id with tab pane.
	$('.rd-settings-tabs .tab-content > div').removeClass('active');
	$('.rd-settings-tabs .tab-content '+target_tab_content_id).addClass('active');

	// listening on click on the tabs.
	$('.rd-settings-tabs .tab-pane > li > a').on('click', function(e) {
		e.preventDefault();
		target_tab_content_id = $(this).attr('href');
		// remove all active tab pane and set to current click one.
		$('.rd-settings-tabs .tab-pane li').removeClass('active');
		$(this).closest('li').addClass('active');
		// remove all active tab content and set to current click one.
		$('.rd-settings-tabs .tab-content > div').removeClass('active');
		$('.rd-settings-tabs .tab-content '+target_tab_content_id).addClass('active');

		if(typeof(Storage) !== "undefined") {
			// set current active tab to localstorage
			localStorage.setItem('rd-settings-tabsactive', target_tab_content_id);
		}
	});

	delete actived_tab, target_tab_content_id;
})(jQuery);