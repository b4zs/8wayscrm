'use strict';

/**
 * @ngdoc overview
 * @name ng-gantt
 * @description
 * # ng-gantt
 *
 * Main module of the application.
 */

var gbGantt = angular.module('gbGantt', [
    'ng',
    'restangular',
    'ui.router',
    'gantt',
    'gantt.tree',
    'gantt.groups',
    'gantt.tooltips',
    'gantt.progress',
    'gantt.dependencies',
    'gantt.condensedgroups',
    'gantt.condensedtooltips'
]);


gbGantt.constant('RedmineBaseUrl', 'http://127.0.0.1:9000/redmine-proxy.php/');
gbGantt.constant('_', window._);
//gbGantt.constant('RedmineBaseUrl', 'http://redmine.assist01.gbart.h3.hu')

// Configurations
gbGantt.config(function ($stateProvider, RestangularProvider, RedmineBaseUrl) {

  // States
  $stateProvider
    .state('login', {
      url: "/login",
      templateUrl: 'views/login.html',
      controller: 'LoginCtrl'
    })
    .state('default', {
      url: "/",
      templateUrl: 'views/projects.html',
      controller: 'ProjectsController',
      resolve: {
        projectIds: function($stateParams, ProjectsRepository) {
          var promise = ProjectsRepository.getAllProjects();
          promise.then(function(a){
            $stateParams.projectIds = a;
          });

          return promise;
        }
      }
    })

    .state('projectgantt', {
      url: "/project/:projectId/gantt",
      templateUrl: 'views/project-gantt.html',
      controller: 'ProjectGanttCtrl'
    })

    .state('condensedgantt', {
      url: "/project/:projectId/condensedgantt",
      templateUrl: 'views/condensed-gantt.html',
      controller: 'CondensedGanttCtrl',
      resolve: {
        projectIds: function ($stateParams, ProjectsRepository) {
          var promise = ProjectsRepository.getAllProjects();
          promise.then(function (a) {
            $stateParams.projectIds = a;
          });

          return promise;
        }
      }
    });

  RestangularProvider.setBaseUrl(RedmineBaseUrl);
  RestangularProvider.setRequestSuffix('.json');
  RestangularProvider.setDefaultHttpFields({cache: true});

  RestangularProvider.addResponseInterceptor(function(data, operation, what) {
    var extractedData;
    // .. to look for getList operations
    if (operation === "getList") {
      // .. and handle the data and meta data
      extractedData = data[what];
      if (undefined !== extractedData) {
        extractedData.meta = {totalcount: data.totalcount, offset: data.offset, limit: data.limit};
      }
    } else {
      extractedData = data.data;
    }
    return extractedData;
  });
});

gbGantt.run(function(User, Restangular, $state, $templateCache) {

  angular.module('ui.tree').config(function(treeConfig) {
    treeConfig.defaultCollapsed = true;
  });


  //Restangular.setErrorInterceptor(function(response) {
  //  if (response.status == 401) {
  //    console.log("Login required... ");
  //    $state.go('default');
  //  } else if (response.status == 404) {
  //    $state.go('default');
  //  } else {
  //    alert("There were an error while connecting to server. We redirect you to the projects page.");
  //    $state.go('default');
  //  }
  //
  //  return false;
  //});


  $templateCache.put('plugins/tree/treeBodyChildren.tmpl.html',
    '<div ng-controller="GanttTreeNodeController"\n' +
    '     class="gantt-row-label gantt-row-height"\n' +
    '     ng-class="row.model.classes"\n' +
    '     ng-style="{\'height\': row.model.height}">\n' +
    '   <div class="gantt-valign-container">\n' +
    '      <div class="gantt-valign-content">\n' +
    '         <a ng-disabled="isCollapseDisabledOnNode()" ng-controller="GanttTreeNodeToggleController" data-nodrag\n' +
    '            class="gantt-tree-handle-button btn btn-xs"\n' +
    '            ng-class="{\'gantt-tree-collapsed\': collapsed, \'gantt-tree-expanded\': !collapsed}"\n' +
    '            ng-click="toggleNode()">'+
    '             <span class="gantt-label-text">{{collapsed?"c":"e"}}</span>' +
    '             <span\n' +
    '                class="gantt-tree-handle glyphicon"\n' +
    '                ng-class="{\n' +
    '                \'glyphicon-chevron-right\': collapsed, \'glyphicon-chevron-down\': !collapsed,\n' +
    '                \'gantt-tree-collapsed\': collapsed, \'gantt-tree-expanded\': !collapsed}"></span>\n' +
    '         </a>\n' +
    '         <span class="gantt-label-text">{{collapsed?"c":"e"}}</span>' +
    '         <span gantt-row-label class="gantt-label-text" gantt-bind-compile-html="getRowContent()"></span>\n' +
    '      </div>\n' +
    '   </div>\n' +
    '</div>\n' +
    '<ol ui-tree-nodes ng-class="{hidden: collapsed}" ng-model="childrenRows">\n' +
    '  <li ng-repeat="row in childrenRows" ui-tree-node collapsed="true">\n' +
    '    <div ng-include="\'plugins/tree/treeBodyChildren.tmpl.html\'"></div>\n' +
    '  </li>\n' +
    '</ol>');

  if (User.getUser()) {
    Restangular.setDefaultRequestParams({ key: User.apiKey(), proxy_cache: true  });
	  if (!$state.is('default')) {
        $state.go('default');
	  }
  } else {
    $state.go('login');
  }
});
