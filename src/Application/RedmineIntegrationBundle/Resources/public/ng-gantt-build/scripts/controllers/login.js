'use strict';

gbGantt.controller('LoginCtrl', function ($scope, $window, $state, User, Restangular) {
    $scope.login = function(credentials){
        User.login(credentials)
            .then(function(response){
                var user = response.data.user;
                User.setUser(user);
                Restangular.setDefaultRequestParams({ key: user.api_key });
                $state.go('default');
            }, function(){
                $window.alert('Wrong username or password!');
            });
    };
  });
