var map = null;
var currentLocation = null;

/**
 *  Responsible for loading all users in the current viewport
 *  @class Singleton AJAX loader for users
 */
var UsersLoader = {
    usersLayers: new Array(),
    removeUsers: function() {
        for(var i = 0; i < UsersLoader.usersLayers.length; ++i) {
            map.removeLayer(UsersLoader.usersLayers[i]);
        }
        
        UsersLoader.usersLayers = [];
    },
    requestUsers: function() {
        if(map.getZoom() >= 13) {
            var bounds = map.getBounds();
            var params = [
            {
                lat: bounds.getSouthWest().lat,
                lng: bounds.getSouthWest().lng
            },
            {
                lat: bounds.getNorthWest().lat,
                lng: bounds.getNorthWest().lng
            },
            {
                lat: bounds.getNorthEast().lat,
                lng: bounds.getNorthEast().lng
            },
            {
                lat: bounds.getSouthEast().lat,
                lng: bounds.getSouthEast().lng
            }
            ];
            $.get(Routing.generate('ctf_statistics_nearby_users_pub', { 'bounds': JSON.stringify(params) }), null, function (data) {
                UsersLoader.onUsersLoaded(JSON.parse(data));
            });
        } else {
            UsersLoader.removeUsers();
        }
    },
    onUsersLoaded: function(results) {
        if('success' == results.status) {
            UsersLoader.removeUsers();
				
            var users = results.users, i;
            for(i = 0; i < users.length; ++i) {
                var marker = new L.marker(users[i].location, {
                    title: users[i].username
                });
                marker.bindPopup('<div class="row-fluid"><div class="span12"><h2 style="color:#000;">' + users[i].fname + ' ' + users[i].lname + '</h2></div></div><div class="row-fluid"><div class="span4"><img src="' + users[i].dp + '" title="' + users[i].username + '\'s Profile Picture" class="img-polaroid" width="50"></div><div style="color:#000;" class="span8"><a href="' + Routing.generate('ctf_public_profile', { id: users[i].id }) + '" target="_blank">@' + users[i].username + '</a></div></div>');
                map.addLayer(marker);
                UsersLoader.usersLayers.push(marker);
            }
        }
    }
};