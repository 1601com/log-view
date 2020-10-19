var logView = {
	init: function () {
		document.querySelectorAll('.log-view_entry').forEach(function (value, index) {
			value.addEventListener('click', function (element) {
				var classList = this.parentNode.querySelector('.additionalInfo').classList;
				if (classList.contains('hidden')) {
					classList.remove('hidden');
					return;
				}
				classList.add('hidden');
			})
		});
	}
};
logView.init();
