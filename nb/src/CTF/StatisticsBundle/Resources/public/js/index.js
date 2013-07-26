var TargetState = {
    loaded: false
};

$('#map-target').attrchange({
    callback: function(event) {
        if (false === TargetState.loaded) {
            loadMap();
            TargetState.loaded = true;
        }
    }
});

var loadMap = function() {
    var watercolour = L.tileLayer('http://tile.stamen.com/watercolor/{z}/{x}/{y}.jpg', {
            continuousWorld: false
        }),
        normal = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            continuousWorld: false
        });

    WorldUsers.map = L.map('users-map', {
        layers: [watercolour, normal],
        minZoom: 3
    }).setView([13.0810, 80.2740], 3);
    
    var baseMaps = {
        "Normal": normal,
        "Watercolour": watercolour
    };
    
    L.control.layers(baseMaps).addTo(WorldUsers.map);
    
    WorldUsers.loadUsers();
};
