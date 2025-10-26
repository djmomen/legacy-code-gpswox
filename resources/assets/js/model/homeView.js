function HomeView() {
    var
        _this = this,
        map = null,
        settings = null;


    _this.init = function (map) {
        _this.map = map;
    };

    _this.isEnabled = function () {
        return app.settings.homeViewEnabled;
    };

    _this.show = function () {
        if (!_this.isEnabled()) {
            return;
        }

        if (_this.settings) {
            _this.applyOnMap();

            return;
        }

        $.get('/home_view', function (data) {
            _this.settings = data;

            _this.applyOnMap();
        });
    };

    _this.applyOnMap = function () {
        const data = _this.settings;

        if (!data || !data.center || typeof data.zoom === 'undefined') {
            return;
        }

        const lat = data.center.lat;
        const lon = data.center.lon;
        const zoom = data.zoom;

        _this.map.setView([lat, lon], zoom);
    };

    _this.resetSettings = function () {
        _this.settings = null;
    };
}
