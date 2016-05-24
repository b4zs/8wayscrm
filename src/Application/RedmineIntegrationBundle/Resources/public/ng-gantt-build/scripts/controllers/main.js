'use strict';

gbGantt.controller('MainCtrl', function ($scope, Restangular, $state, $q) {
	  var pageSize = 100;
	  var maxPage = 5;
	  $scope.projects = [];

	  getProjectsPage(0);

	  $scope.showGantt = function(projectId) {
		  $state.go("projectgantt", {projectId: projectId});
	  };

    $scope.showCondensedGantt = function(projectId) {
      $state.go("condensedgantt", {projectId: projectId});
    };

		function getProjectsPage(page) {
			Restangular.all('projects').getList({limit: pageSize, offset: page * pageSize}).then(function(projects) {
				var filteredProjects = _.filter(projects, { parent: {name: "8 Ways"} });
				$scope.projects = $scope.projects.concat(_.map(filteredProjects, function(p) { return _.pick(p, 'id', 'name') }));

				if (++page < maxPage) {
					getProjectsPage(page);
				}
			});
		}
  });
