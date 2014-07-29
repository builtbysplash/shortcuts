var ShortcutsBar = Garnish.Base.extend(
{
	$quickAdd: null,
	$mobileButton: null,

	init: function() {
		// Find key elements
		this.$quickAdd = $('#shortcuts-quickAdd');
		this.$mobileButton = $('#shortcuts-mobileButton');

		// Activate quick add buttons
		this.$quickAdd.click(function() {
			var uri = window.location.href.replace(Craft.baseCpUrl + '/', '');;
			var params = {
				uri: uri
			};
			window.location.href = Craft.getCpUrl('shortcuts/new', params);
		});

		// Activate mobile button
		this.$mobileButton.click(function() {
			$('#shortcuts-bar').toggleClass('mobile');
		});
	}
});
