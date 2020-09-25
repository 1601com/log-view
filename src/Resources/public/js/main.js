var logView = {
	init: function () {
		document.querySelectorAll('.log-view_row').forEach(function (value, index) {
			value.addEventListener('click', function (element) {
				var classList = this.querySelector('.additionalInfo').classList;
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
