'use strict';

gbGantt.factory('User', function(Restangular, $http, RedmineBaseUrl) {
		var getHeaders = function(credentials) {
			return {
					'Content-Type': 'application/json',
					'Authorization': 'Basic '+btoa(credentials.username+':'+credentials.password)
				};
		}

		return {
			login: function(credentials) {
				/*return Restangular.one('users').customGET('current', {}, getHeaders(credentials)).then(function(resp) {
					console.log('response', resp);
				});*/
				return $http.get(RedmineBaseUrl + '/users/current.json', { 'headers': getHeaders(credentials) });
			},

			setUser: function(user){
                localStorage.user = JSON.stringify(user);
			},

			getUser: function(){
                try {
				    return localStorage.user ? JSON.parse(localStorage.user) : null;
                } catch(e) {
                    return null;
                }
			},

			apiKey: function(){
				return this.getUser() ? this.getUser().api_key : null;
			},

			isLoggedIn: function(){
				return null !== this.getUser();
			},

			logOut: function(){
				localStorage.user = '';
			}
		}
	});
