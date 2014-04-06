var ShortcutsBar = Garnish.Base.extend(
{
	$quickAdd: null,

	init: function() {
		// Find key elements
		this.$quickAdd = $('#shortcuts-quickAdd');

		// Activate quick add buttons
		this.$quickAdd.click(function() {
			var uri = window.location.href.replace(Craft.baseCpUrl + '/', '');;
			var params = {
				uri: uri
			};
			window.location.href = Craft.getCpUrl('shortcuts/new', params);
		});
	}
});
