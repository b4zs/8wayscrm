/*
Project: angular-gantt v1.2.10 - Gantt chart component for AngularJS
Authors: Marco Schweighauser, Rémi Alvergnat
License: MIT
Homepage: https://www.angular-gantt.com
Github: https://github.com/angular-gantt/angular-gantt.git
*/
(function(){
    'use strict';
    angular.module('gantt.condensedgroups', ['gantt', 'gantt.condensedgroups.templates']).directive('ganttCondensedGroups', ['ganttUtils', 'GanttHierarchy', '$compile', '$document', function(utils, Hierarchy, $compile, $document) {
        return {
            restrict: 'E',
            require: '^gantt',
            scope: {
                enabled: '=?',
                display: '=?'
            },
            link: function(scope, element, attrs, ganttCtrl) {
                var api = ganttCtrl.gantt.api;

                // Load options from global options attribute.
                if (scope.options && typeof(scope.options.sortable) === 'object') {
                    for (var option in scope.options.sortable) {
                        scope[option] = scope.options[option];
                    }
                }

                if (scope.enabled === undefined) {
                    scope.enabled = true;
                }

                if (scope.display === undefined) {
                    scope.display = 'group';
                }

                scope.hierarchy = new Hierarchy();

                function refresh() {
                    scope.hierarchy.refresh(ganttCtrl.gantt.rowsManager.filteredRows);
                }

                ganttCtrl.gantt.api.registerMethod('condensedgroups', 'refresh', refresh, this);
                ganttCtrl.gantt.$scope.$watchCollection('gantt.rowsManager.filteredRows', function() {
                    refresh();
                });

                api.directives.on.new(scope, function(directiveName, rowScope, rowElement) {
                    if (directiveName === 'ganttRow') {
                        if (! (rowScope.row.model.condensedGroups instanceof Array)) return;

                        var lifecycleGroupScope = rowScope.$new();
                        lifecycleGroupScope.pluginScope = scope;

                        var ifElement = $document[0].createElement('div');
                        angular.element(ifElement).attr('data-ng-if', 'pluginScope.enabled');

                        var lifecycleGroupElement = $document[0].createElement('gantt-condensed-task-group');
                        if (attrs.templateUrl !== undefined) {
                            angular.element(lifecycleGroupElement).attr('data-template-url', attrs.templateUrl);
                        }
                        if (attrs.template !== undefined) {
                            angular.element(lifecycleGroupElement).attr('data-template', attrs.template);
                        }

                        angular.element(ifElement).append(lifecycleGroupElement);

                        rowElement.append($compile(ifElement)(lifecycleGroupScope));
                    }
                });
            }
        };
    }]);
}());


(function(){
    'use strict';
    angular.module('gantt.condensedgroups').controller('GanttCondensedGroupController', ['$scope', 'GanttCondensedGroups', 'ganttUtils', function($scope, CondensedGroups, utils) {
        var updateCondensedTaskGroup = function() {
            var lifecycleGroups = $scope.row.model.condensedGroups;

            var enabledValue = utils.firstProperty([lifecycleGroups], 'enabled', $scope.pluginScope.enabled);
            if (enabledValue) {
                $scope.display = utils.firstProperty([lifecycleGroups], 'display', $scope.pluginScope.display);
                var c = new CondensedGroups($scope.row, $scope.pluginScope);
                $scope.taskGroups = c.groups;

                //$scope.row.setFromTo();
                // TODO?: átírja a row-ba a taskGroup alapján a kezdeti és végdátumokat
                //$scope.row.setFromToByValues($scope.taskGroup.from, $scope.taskGroup.to);
            } else {
                $scope.taskGroups = undefined;
                $scope.display = undefined;
            }
        };

        // TODO check
        $scope.gantt.api.tasks.on.viewChange($scope, function(task) {
            if ($scope.taskGroup !== undefined) {
                if ($scope.taskGroup.tasks.indexOf(task) > -1) {
                    updateCondensedTaskGroup();
                    if(!$scope.$$phase) {
                        $scope.$digest();
                    }
                } else {
                    var descendants = $scope.pluginScope.hierarchy.descendants($scope.row);
                    if (descendants.indexOf(task.row) > -1) {
                        updateCondensedTaskGroup();
                        if(!$scope.$$phase) {
                            $scope.$digest();
                        }
                    }
                }
            }
        });

        $scope.isActive = function(taskGroup) {
          return taskGroup.from <= $scope.gantt.currentDateManager.date &&
                 $scope.gantt.currentDateManager.date < taskGroup.to;
        };

        $scope.currentDateTooltipText = (function() {
          var details = $scope.row.model.details;
          var issuesInProgress = _.chain(details.issuesInProgress)
                                  .map(function(issue) { return issue.subject+" assignee: "+issue.assignee+"\n"; })
                                  .reduce(function(a,b) { return a + b; })
                                  .value();
          return "Project manager: "+details.projectManager+'\n'+
                 "Reported status: "+details.reportedStatus+'\n'+
                 "Issues in progress:\n"+ issuesInProgress
        })();

        var removeWatch = $scope.pluginScope.$watch('display', updateCondensedTaskGroup);

        $scope.$watchCollection('gantt.rowsManager.filteredRows', updateCondensedTaskGroup);

        $scope.gantt.api.columns.on.refresh($scope, updateCondensedTaskGroup);

        $scope.$on('$destroy', removeWatch);
    }]);
}());


(function(){
    'use strict';
    angular.module('gantt.condensedgroups').directive('ganttCondensedTaskGroup', ['GanttDirectiveBuilder', function(Builder) {
        var builder = new Builder('ganttCondensedTaskGroup', 'plugins/groups/condensedTaskGroup.tmpl.html');
        return builder.build();
    }]);
}());


(function(){
    'use strict';

    angular.module('gantt').factory('GanttCondensedGroups', ['ganttUtils', function(utils) {
        var CondensedGroups = function (row, pluginScope) {
            var self = this;

            self.row = row;
            self.pluginScope = pluginScope;
            self.groups = [];
            self.showGrouping = false;

            var lifecycleGroups = self.row.model.condensedGroups;
            if (lifecycleGroups.length > 0) {
                self.showGrouping = true;
                angular.forEach(lifecycleGroups, function(lifecycleGroup) {
                    var left = row.rowsManager.gantt.getPositionByDate(lifecycleGroup.from);
                    var width = row.rowsManager.gantt.getPositionByDate(lifecycleGroup.to) - left;

                    self.groups.push({
                      left: left,
                      width: width,
                      name: lifecycleGroup.name,
                      inProgress: lifecycleGroup.in_progress,
                      parentName: row.model.name
                    });
                });
            }
        };
        return CondensedGroups;
    }]);
}());

angular.module('gantt.condensedgroups.templates', []).run(['$templateCache', function($templateCache) {
    $templateCache.put('plugins/groups/condensedTaskGroup.tmpl.html',
        '<div ng-controller="GanttCondensedGroupController">\n' +
        '    <div class="gantt-task-group"\n' +
        '         ng-class="\'gantt-lifecycle-\' + taskGroup.name.substr(0,1)"\n' +
        '         ng-attr-title="{{taskGroup.name}}"\n' +
        '         ng-style="{\'left\': taskGroup.left + \'px\', \'width\': taskGroup.width + \'px\'}"\n' +
        '         ng-repeat="taskGroup in taskGroups">\n' +
        '        <div ng-if="isActive(row.model.condensedGroups[$index])" ng-style="{ \'left\': gantt.currentDateManager.position - taskGroup.left - 6 + \'px\' }" ng-attr-title="{{currentDateTooltipText}}" class="gantt-task-group-current-date"></div>' +
        '        <div class="gantt-task-group-left-main"></div>\n' +
        '        <div class="gantt-task-group-right-main"></div>\n' +
        '        <div class="gantt-task-group-left-symbol"></div>\n' +
        '        <div class="gantt-task-group-right-symbol"></div>\n' +
        '    </div>\n' +
        '</div>\n' +
        '\n' +
        '');
}]);

//# sourceMappingURL=angular-gantt-condensedgroups-plugin.js.map

;'use strict';

/**
 * @ngdoc overview
 * @name ng-gantt
 * @description
 * # ng-gantt
 *
 * Main module of the application.
 */

angular.module('ng-gantt', [
    'restangular',
    'ui.router',
    'gantt',
    'gantt.tree',
    'gantt.tooltips',
    'gantt.groups',
    'gantt.progress',
    'gantt.dependencies',
    'gantt.condensedgroups'
])

.constant('RedmineBaseUrl', 'http://redmine.assist01.gbart.h3.hu')

// Configurations
.config(function ($stateProvider, RestangularProvider, RedmineBaseUrl) {

  // States
  $stateProvider
    .state('login', {
      url: "/login",
      templateUrl: 'views/login.html',
      controller: 'LoginCtrl'
    })

    .state('projects', {
      url: "/projects",
      templateUrl: 'views/projects.html',
      controller: 'ProjectsCtrl'
    })

    .state('projectgantt', {
      url: "/project/:projectId/gantt",
      templateUrl: 'views/project-gantt.html',
      controller: 'ProjectGanttCtrl'
      /*
      resolve: {
        apiKey: function($window) {
          var apiKey = $window.sessionStorage.getItem('apiKey')
          if (apiKey === null) {
            apiKey = $window.prompt('redmine api key please');
            $window.sessionStorage.setItem('apiKey', apiKey);
          }
          return apiKey;
        }
      }*/
    })

    .state('condensedgantt', {
      url: "/project/:projectId/condensedgantt",
      templateUrl: 'views/condensed-gantt.html',
      controller: 'CondensedGanttCtrl'
    });


  RestangularProvider.setBaseUrl(RedmineBaseUrl);
  RestangularProvider.setRequestSuffix('.json');
  RestangularProvider.addResponseInterceptor(function(data, operation, what, url, response, deferred) {
    var extractedData;
    // .. to look for getList operations
    if (operation === "getList") {
      // .. and handle the data and meta data
      extractedData = data[what];
      extractedData.meta = {totalcount: data.totalcount, offset: data.offset, limit: data.limit};
    } else {
      extractedData = data.data;
    }
    return extractedData;
  });
})
.constant('_', window._)
.run(function(User, Restangular, $state, $templateCache) {

  angular.module('ui.tree').config(function(treeConfig) {
    treeConfig.defaultCollapsed = true;
  });

  // TODO: remove hack, need to override template
  /*
  $templateCache.put('plugins/tree/treeBodyChildrenOriginal.tmpl.html',
    '<div ng-controller="GanttTreeNodeController"\n' +
    '     class="gantt-row-label gantt-row-height"\n' +
    '     ng-class="row.model.classes"\n' +
    '     ng-style="{\'height\': row.model.height}">\n' +
    '  <div class="gantt-valign-container">\n' +
    '    <div class="gantt-valign-content">\n' +
    '      <a ng-disabled="isCollapseDisabled()" data-nodrag\n' +
    '         class="gantt-tree-handle-button btn btn-xs"\n' +
    '         ng-class="{\'gantt-tree-collapsed\': collapsed, \'gantt-tree-expanded\': !collapsed}"\n' +
    '         ng-click="!isCollapseDisabled() && toggle()"><span\n' +
    '         class="gantt-tree-handle glyphicon glyphicon-chevron-down"\n' +
    '         ng-class="{\n' +
    '         \'glyphicon-chevron-right\': collapsed, \'glyphicon-chevron-down\': !collapsed,\n' +
    '         \'gantt-tree-collapsed\': collapsed, \'gantt-tree-expanded\': !collapsed}"></span>\n' +
    '      </a>\n' +
    '      <span gantt-row-label class="gantt-label-text" gantt-bind-compile-html="getRowContent()"/>\n' +
    '    </div>\n' +
    '  </div>\n' +
    '</div>\n' +
    '<ol ui-tree-nodes ng-class="{hidden: collapsed}" ng-model="childrenRows">\n' +
    '  <li ng-repeat="row in childrenRows" ui-tree-node>\n' +
    '    <div ng-include="\'plugins/tree/treeBodyChildrenOriginal.tmpl.html\'"></div>\n' +
    '  </li>\n' +
    '</ol>');*/

  $templateCache.put('plugins/tree/treeBodyChildren.tmpl.html',
    '<div ng-controller="GanttTreeNodeController"\n' +
    '     class="gantt-row-label gantt-row-height"\n' +
    '     ng-class="row.model.classes"\n' +
    '     ng-style="{\'height\': row.model.height}">\n' +
    '<div class="gantt-valign-container">\n' +
    '<div class="gantt-valign-content">\n' +
    '<a ng-disabled="isCollapseDisabledOnNode()" ng-controller="GanttTreeNodeToggleController" data-nodrag\n' +
    '   class="gantt-tree-handle-button btn btn-xs"\n' +
    '   ng-class="{\'gantt-tree-collapsed\': collapsed, \'gantt-tree-expanded\': !collapsed}"\n' +
    '   ng-click="toggleNode()"><span\n' +
    '   class="gantt-tree-handle glyphicon"\n' +
    '   ng-class="{\n' +
    '   \'glyphicon-chevron-right\': collapsed, \'glyphicon-chevron-down\': !collapsed,\n' +
    '   \'gantt-tree-collapsed\': collapsed, \'gantt-tree-expanded\': !collapsed}"></span>\n' +
    '</a>\n' +
    '<span gantt-row-label class="gantt-label-text" gantt-bind-compile-html="getRowContent()"/>\n' +
    '</div>\n' +
    '</div>\n' +
    '</div>\n' +
    '<ol ui-tree-nodes ng-class="{hidden: collapsed}" ng-model="childrenRows">\n' +
    '  <li ng-repeat="row in childrenRows" ui-tree-node collapsed="true">\n' +
    '    <div ng-include="\'plugins/tree/treeBodyChildren.tmpl.html\'"></div>\n' +
    '  </li>\n' +
    '</ol>');

  if (User.getUser()) {
    Restangular.setDefaultRequestParams({ key: User.apiKey() });
    $state.go('projects');
  } else
    $state.go('login');
});

'use strict';

/**
 * @ngdoc function
 * @name ng-gantt.controller:CondensedGanttCtrl
 * @description
 * # CondensedGanttCtrl
 * Controller of the condensed gantt
 */
angular.module('ng-gantt')
  .controller('CondensedGanttCtrl', function ($scope, Restangular, $stateParams, RedmineBaseUrl, $compile, moment, _, PrepareIssues, $timeout, $window) {

    var destroyOpenProjectListener = $scope.$on('openProject', function(e, projectRowScope) {
      var projectId = projectRowScope.row.model.id;
      var projectParent = projectRowScope.row.model.parent;
      Restangular.all('issues').getList({ project_id: projectId, limit: 100, include: 'relations' }).then(
        function(issues) {
          if (projectParent) $scope.$broadcast('projectOpened', projectId);

          var newRows = PrepareIssues(issues, projectId);
          var filteredRows = _.filter($scope.data, function(ganttRow) {
            // never get rid of the 'project rows'
            // TODO: mark the project rows explicitly
            if (ganttRow.hasOwnProperty('parent')) return true;

            // keep the rows under the opened project
            //console.log('keeping row: ', ganttRow.projectId == projectId);
            return ganttRow.projectId == projectId;
          });
          $scope.data = filteredRows.concat(newRows);
          $scope.openedProject = projectId;

          console.log("(open) now toggle", projectId);
          projectRowScope.toggle();

          $timeout(function() {
            $scope.api.side.setWidth(undefined);
          }, 0);
        });
    });

    var destroyCloseProjectListener = $scope.$on('closeProject', function (e, projectRowScope) {
      var projectId = projectRowScope.row.model.id;
      $scope.data = _.filter($scope.data, function(ganttRow) {
        // TODO: mark the project rows explicitly
        if (ganttRow.hasOwnProperty('parent')) return true;

        return ganttRow.projectId != projectId;
      });

      console.log("(close) now toggle", projectId);
      projectRowScope.toggle();
    });

    $scope.$on('destroy', function() {
      destroyOpenProjectListener();
      destroyCloseProjectListener();
    });

    $scope.filter = {
      row: "",
      category: ""
    };

    $scope.filterRow = function ($event) {
      if ($event.which === 13) {
        applyFilters();
      }
    };

    $scope.filterCategory = function() { applyFilters(); };

    var applyFilters = function() {
      $scope.api.rows.refresh();
      $scope.toggleMenu();
    };

    $scope.filterRowFunc = function (row) {
      var rowValue = $scope.filter.row;
      var categoryValue = $scope.filter.category;
      var rowVisibleByRowFilter = true;
      var rowVisibleByCategoryFilter = true;

      // do not hide the opened project's rows
      if (row.model.projectId == $scope.openedProject) {
        rowVisibleByRowFilter = true;
      } else if (rowValue !== undefined && rowValue != '') {
        rowVisibleByRowFilter = row.model.name.indexOf(rowValue) > -1;
      }

      if (categoryValue !== undefined && categoryValue != '') {
        if (row.model.condensedGroups === undefined) {
          //console.log('hiding row', row.model.name);
          rowVisibleByCategoryFilter = true;
        } else {
          rowVisibleByCategoryFilter =
            _.chain(row.model.condensedGroups)
              .map(function (group) {
                return $scope.isActiveLifecycleCategory(group) && group.name.substr(0, 1) == categoryValue
              })
              .any()
              .value();
        }
      }

      //console.log('row: ', rowVisibleByRowFilter, ', category: ', rowVisibleByCategoryFilter);
      return rowVisibleByRowFilter && rowVisibleByCategoryFilter;
    };

    // TODO: remove duplicated logic (see condensedgroups plugin)
    $scope.isActiveLifecycleCategory = function(lifecycleCategory) {
      return lifecycleCategory.from <= $scope.api.gantt.currentDateManager.date &&
        $scope.api.gantt.currentDateManager.date < lifecycleCategory.to;
    };

    $scope.showMenu = false;
    $scope.toggleMenu = function() { $scope.showMenu = !$scope.showMenu };

    // TODO: causes flicker when opening a project
    $scope.maxHeight = function() {
      return $window.innerHeight;
    };

    $scope.options = {
      timeFrames: {
        'day': {
          start: moment('10:00', 'HH:mm'),
          end: moment('18:00', 'HH:mm'),
          working: true,
          default: true
        },
        'weekend': {
          working: false
        }
      },
      dateFrames: {
        'weekend': {
          evaluator: function(date) {
            return date.isoWeekday() === 6 || date.isoWeekday() === 7;
          },
          targets: ['weekend']
        }
      },
      rowContent: '<i class="fa fa-align-justify"></i> {{row.model.name}}',
      taskContent : '<i class="fa fa-tasks"></i> <a href="'+RedmineBaseUrl+'/issues/{{task.model.id}}" target="_blank">{{task.model.name}}</a>',
      columnWidth: 18,
      currentDate: 'line',
      currentDateValue: new Date(2015, 5, 12)//, 9, 0, 0)
    };

    $scope.registerApi = function(api) {
      $scope.api = api;

      api.core.on.ready($scope, function(api) {
        api.directives.on.new($scope, function(dName, dScope, dElement, dAttrs, dController) {
          if (dName === 'ganttTaskContent') {
            dElement.attr('inview', '');
            $compile(dElement)(dScope);
          }
        });

        Restangular.one('ganttprojects', $stateParams.projectId).getList().then(function(projects) {
          projectsLoaded(projects);

          // collapse this way or need to override another tree tmpl..
          $timeout(function() {
            $scope.api.tree.collapseAll();

            $scope.api.side.setWidth(undefined);
            $scope.readyToShow = true;
          }, 0);
        });
      });
    };

    function projectsLoaded(projects) {
      //console.log(projects);
      var data = [];

      _.each(projects, function(project) {
        var condensedProjectRow = {
          id: project.id,
          name: project.name || "project " + project.id,
          groups: false,
          classes: ['gantt-row-lifecycle'],
          parent: project.parent_id,
          details: {
            projectManager: project.project_manager || '-',
            reportedStatus: project.reported_status,
            issuesInProgress: project.in_progress_issues
          }
        };

        if (_.isEmpty(project.lifecycle_categories)) {
          var childProjects = _.where(projects, { 'parent_id': project.id });
          var projectStartDate = _.min(childProjects, function(cp) { return new Date(cp.start_date) }).start_date;
          var projectDueDate = _.max(childProjects, function(cp) { return new Date(cp.due_date) }).due_date;

          // TODO
          $scope.options.fromDate = projectStartDate;
          $scope.options.toDate = projectDueDate;

          condensedProjectRow.groups = { enabled: true, display: 'group', from: moment(projectStartDate), to: moment(projectDueDate) };
        } else {
          condensedProjectRow.condensedGroups = [];

          _.each(_.sortBy(project.lifecycle_categories, function (x) {
            return x.name
          }), function (lifecycleCategory) {
            var lifecycleGroup = {
              from: moment(lifecycleCategory.start_date),
              to: moment(lifecycleCategory.due_date),
              name: lifecycleCategory.name,
              in_progress: lifecycleCategory.in_progress
            };

            condensedProjectRow.condensedGroups.push(lifecycleGroup);
          });
        }
        data.push(condensedProjectRow);
      });

      $scope.data = data;
    }
  });

'use strict';

angular.module('ng-gantt')
  .controller('GanttTreeNodeToggleController', function($scope) {
    $scope.toggleNode = function() {
      if (isProjectRow()) {
        toggleProject();
      } else {
        !$scope.isCollapseDisabled() && $scope.toggle();
      }
    };

    $scope.isCollapseDisabledOnNode = function() {
      if (isProjectRow()) {
        return false;
      } else {
        return $scope.isCollapseDisabled();
      }
    };

    $scope.$on("projectOpened", function (e, projectId) {
      if (!isProjectRow()) return;

      if ($scope.row.model.parent && $scope.row.model.id != projectId) {
        $scope.closeProject();
      }
    });

    $scope.openProject = function () {
      if (!$scope.collapsed) return;

      console.log('clicked project', $scope.row.model.id);
      $scope.$emit('openProject', $scope);
      var idx = $scope.row.model.classes.indexOf('gantt-row-expanded');
      if (idx == -1) $scope.row.model.classes.push('gantt-row-expanded');
    };

    $scope.closeProject = function () {
      if ($scope.collapsed) return;

      if ($scope.row.model.parent) {
        $scope.$emit('closeProject', $scope);
      } else {
        $scope.toggle();
      }
      var idx = $scope.row.model.classes.indexOf('gantt-row-expanded');
      if (idx > -1) $scope.row.model.classes.splice(idx, 1);
    };

    var isProjectRow = function() {
      return $scope.row.model.condensedGroups instanceof Array;
    };

    var toggleProject = function () {
      $scope.collapsed ? $scope.openProject() : $scope.closeProject();
    };
  });

'use strict';

angular.module('ng-gantt')
  .controller('LoginCtrl', function ($scope, $window, $state, User, Restangular) {
    $scope.login = function(credentials){
        User.login(credentials)
            .then(function(response){
                var user = response.data.user;
                User.setUser(user);
                Restangular.setDefaultRequestParams({ key: user.api_key });
                $state.go('projects');
            }, function(){
                $window.alert('Wrong username or password!');
            });
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name ng-gantt.controller:ProjectGanttCtrl
 * @description
 * # ProjectGanttCtrl
 * Controller of the project gantt
 */
angular.module('ng-gantt')
.controller('ProjectGanttCtrl', function ($scope, Restangular, $stateParams, RedmineBaseUrl, $compile, moment, _, PrepareIssues) {
    $scope.timeFrames = {
        'day': {
            start: moment('10:00', 'HH:mm'),
            end: moment('18:00', 'HH:mm'),
            working: true,
            default: true
        },
        'weekend': {
            working: false
        }
    };

    $scope.dateFrames = {
        'weekend': {
            evaluator: function(date) {
                return date.isoWeekday() === 6 || date.isoWeekday() === 7;
            },
            targets: ['weekend']
        }
    };

    $scope.registerApi = function(api) {
        $scope.api = api;

        api.core.on.ready($scope, function(api) {
            console.log('ready');
            api.directives.on.new($scope, function(dName, dScope, dElement, dAttrs, dController) {
                if (dName === 'ganttTaskContent') {
                    dElement.attr('inview', '');
                    $compile(dElement)(dScope);
                }
            });

            Restangular.all('issues').getList({ project_id: $stateParams.projectId, limit: 100, include: 'relations' })
                .then(function(issues) {
                    $scope.data = PrepareIssues(issues);
                });
        });
    };

    var contextMenuOptions = [
            ['Context item 1', function (a) {

            }],
            null,
            ['Context item 2', function (a) {

            }],
            null,
            ['More...', [
                ['Sub item 1', function ($itemScope) {

                }],
                null,
                ['Sub item 2', function ($itemScope) {

                }]
            ]]
    ];

    $scope.options = {
        contextMenuOptions: contextMenuOptions,
        rowContent: '<i class="fa fa-align-justify"></i> {{row.model.name}}',
        taskContent : '<i class="fa fa-tasks"></i> <span ng-context-menu="contextMenuOptions"><a href="'+RedmineBaseUrl+'/issues/{{task.model.id}}" target="_blank">{{task.model.name}}</a></span>',
    };
});

'use strict';

angular.module('ng-gantt')
  .controller('ProjectsCtrl', function ($scope, Restangular, $state) {
	  var pageSize = 100;
	  var maxPage = 5;
	  $scope.projects = [{id: 484, name: 'Teszt projekt'}];

	  getProjectsPage(0);

	  $scope.showGantt = function(projectId) {
		  $state.go("projectgantt", {projectId: projectId});
	  };

    $scope.showCondensedGantt = function(projectId) {
      $state.go("condensedgantt", {projectId: projectId});
    };

	  function getProjectsPage(page) {
		Restangular.all('projects').getList({limit: pageSize, offset: page * pageSize}).then(function(projects) {
			var filteredProjects = _.where(projects, { parent: {name: "8 Ways"} });
			$scope.projects = $scope.projects.concat(_.map(filteredProjects, function(p) { return _.pick(p, 'id', 'name') }));

			if (++page < maxPage) getProjectsPage(page);
		});
	  }
  });

'use strict';
// borrowed from https://stackoverflow.com/questions/29764079/angularjs-creating-context-menu-with-submenu
angular.module('ng-gantt')
.directive('ngContextMenu', function ($parse) {
    var buildMenuItem = function($scope, list, item) {
        var $li = angular.element('<li>');
        if (item === null) {
            $li.addClass('divider');
        } else if(item[1] instanceof Array) {
            $li.addClass("dropdown-submenu");
            var $subMenu = angular.element('<ul class="dropdown-menu">');
            
            item[1].forEach(function (subItem, x) {
                buildMenuItem($scope, $subMenu, subItem);
            });
            
            var $a = angular.element('<a>');
            $a.text(item[0]);
            $li.append($a);
            $li.append($subMenu);
        } else {
            var $a = angular.element('<a>');
            $a.attr({ tabindex: '-1', href: '#' });
            $a.text(item[0]);
            $li.append($a);
            $li.on('click', function () {
                $scope.$apply(function() {
                    item[1].call($scope, $scope);
                });
            });
        }
        list.append($li);
    };
    
    var renderContextMenu = function ($scope, event, options) {
        angular.element(event.currentTarget).addClass('context');
        var $contextMenu = angular.element('<div>');
        $contextMenu.addClass('dropdown clearfix');
        var $ul = angular.element('<ul>');
        $ul.addClass('dropdown-menu');
        $ul.attr({ 'role': 'menu' });
        $ul.css({
            display: 'block',
            position: 'absolute',
            left: event.pageX + 'px',
            top: event.pageY + 'px'
        });
        angular.forEach(options, function (item, i) {
            buildMenuItem($scope, $ul, item);
        });
        $contextMenu.append($ul);
        $contextMenu.css({
            width: '100%',
            height: '100%',
            position: 'absolute',
            top: 0,
            left: 0,
            zIndex: 9999
        });
        angular.element(document).find('body').append($contextMenu);
        $contextMenu.on("click", function (e) {
            angular.element(event.currentTarget).removeClass('context');
            $contextMenu.remove();
        }).on('contextmenu', function (event) {
            angular.element(event.currentTarget).removeClass('context');
            event.preventDefault();
            $contextMenu.remove();
        });
    };
    return function ($scope, element, attrs) {
        element.on('contextmenu', function (event) {
            $scope.$apply(function () {
                event.preventDefault();
                var options = $scope.$eval(attrs.ngContextMenu);
                if (options instanceof Array) {
                    renderContextMenu($scope, event, options);
                } else {
                    throw '"' + attrs.ngContextMenu + '" not an array';                    
                }
            });
        });
    };
});

'use strict';

angular.module('ng-gantt')
.directive('inview', function ($document, $compile) {
    var getViewPortWidth = function() {
        var d = $document[0];
        return d.documentElement.clientWidth || d.documentElement.getElementById('body')[0].clientWidth;
    };
    
    return {
        link: function(scope, element) {
            scope.$watch(
                function() {
                    var clientRect = element[0].getClientRects()[0];
                    return clientRect.right <= getViewPortWidth();
                },
                function(newInviewStatus, oldInviewStatus) {
                    if (! newInviewStatus) {
                        element[0].style.right = '100%';
                        element[0].style.left = 'auto';
                    }
                }
            );
        }
    }
});

'use strict';

angular.module('ng-gantt')
  .factory('PrepareIssues', function() {
    return function (issues, root) {
      /* debug
       console.log(issues);
       _.each(issues, function (issue) {
       var parentId = issue.parent ? issue.parent.id : null;
       var lifecycleCategory = _.findWhere(issue.custom_fields, {name: 'Lifecycle category'}).value;

       if (/^A/.test(lifecycleCategory))
       console.log(issue.custom_fields[4], issue);
       //console.log(issue.id, issue.subject, parentId, lifecycleCategory);
       });*/

      var data = [];
      var issuesDependencies = getIssuesDependencies(issues);
      var issuesByLifecycle = getIssuesByLifecycle(issues);

      _.each(_.keys(issuesByLifecycle).sort(), function (lifecycle) {
        var lifecycleRow = { id: lifecycle + root, name: lifecycle, groups: true, classes: 'gantt-row-lifecycle', projectId: root };
        if (root) lifecycleRow.parent = root;

        var sortedLifecycleChildIssues = _.sortBy(issuesByLifecycle[lifecycle], function (issue) {
          return getCustomFieldValue(issue, 'Position');
        });

        _.each(sortedLifecycleChildIssues, function (issue) {
          var parent = issue.parent ? _.findWhere(issues, {id: issue.parent.id}).subject : lifecycle + root;

          var assigneeRole = getCustomFieldValue(issue, 'Assignee role');

          var dependencies = getIssueDependencyParameters(issue, issuesDependencies);

          var row =
          {
            id: issue.id,
            name: issue.subject,
            parent: parent,
            projectId: root,
            tasks: [
              {
                id: issue.id,
                name: issue.subject,
                from: issue.start_date,
                to: issue.due_date,
                type: issue.tracker.name,
                status: issue.status.name,
                priority: issue.priority.name,
                assignee: { role: assigneeRole, fullname: issue.assigned_to.name },
                progress: { percent: issue.done_ratio, classes: ['ng-gantt-progress'] },
                classes: getAssigneeClass(assigneeRole),
                dependencies: dependencies
              }
            ]
          };

          // don't show the group if its a sub-task
          if (issue.parent) row.groups = false;
          data.push(row);
        });
        data.push(lifecycleRow);
      });

      return data;
    };

    function getIssueDependencyParameters(issue, issuesDependencies) {
      var issueDependencies = _.findWhere(issuesDependencies, {id: issue.id});
      if (issueDependencies === undefined) return [];

      return _.map(issueDependencies.dependencies, function(dependencyId) {
        return { from: dependencyId };
      });
    }

    function getIssuesDependencies(issues) {
      var issuesDependencies = [];

      _.each(issues, function (issue) {
        if (issue.relations.length == 0) return;

        _.where(issue.relations, { relation_type: 'precedes' })
          .forEach(function(precedesRelation) {
            var issueDependencies;
            if (issueDependencies = _.findWhere(issuesDependencies, { id: precedesRelation.issue_to_id })) {
              if (! _.contains(issueDependencies.dependencies, precedesRelation.issue_id))
                issueDependencies.dependencies.push(precedesRelation.issue_id);
            } else
              issuesDependencies.push({ id: precedesRelation.issue_to_id, dependencies: [ precedesRelation.issue_id ] });
          });
      });

      return issuesDependencies;
    }

    function getIssuesByLifecycle(issues) {
      return _.groupBy(issues, function (issue) {
        return getCustomFieldValue(issue, 'Lifecycle category', '');
      });
    }

    function getCustomFieldValue(issue, name, defaultValue) {
      var customFieldByName = _.findWhere(issue.custom_fields, {name: name});
      if (customFieldByName === undefined) return defaultValue;

      return customFieldByName.value;
    }

    function getAssigneeClass(assignee) {
      switch (assignee) {
        case 'Sales': return "sales-task";
        case 'Team': return "team-task";
        case 'PM': return "pm-task";
        case 'Client': return "client-task";
        case '':
        case undefined: return '';
        default: throw new Error('Unknown assignee ' + assignee);
      }
    }
  });

'use strict';

angular.module('ng-gantt')
	.factory('User', function(Restangular, $http, RedmineBaseUrl) {
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
(function(){
    'use strict';

    angular.module('gantt').factory('GanttTaskGroup', ['ganttUtils', 'GanttTask', function(utils, Task) {
        var TaskGroup = function (row, pluginScope) {
            var self = this;

            self.row = row;
            self.pluginScope = pluginScope;

            self.descendants = self.pluginScope.hierarchy.descendants(self.row);

            self.tasks = [];
            self.overviewTasks = [];
            self.promotedTasks = [];
            self.showGrouping = false;

            var groupRowGroups = self.row.model.groups;
            if (typeof(groupRowGroups) === 'boolean') {
                groupRowGroups = {enabled: groupRowGroups};
            }

            var getTaskDisplay = function(task) {
                var taskGroups = task.model.groups;
                if (typeof(taskGroups) === 'boolean') {
                    taskGroups = {enabled: taskGroups};
                }

                var rowGroups = task.row.model.groups;
                if (typeof(rowGroups) === 'boolean') {
                    rowGroups = {enabled: rowGroups};
                }

                var enabledValue = utils.firstProperty([taskGroups, rowGroups, groupRowGroups], 'enabled', self.pluginScope.enabled);

                if (enabledValue) {
                    var display = utils.firstProperty([taskGroups, rowGroups, groupRowGroups], 'display', self.pluginScope.display);
                    return display;
                }
            };

            angular.forEach(self.descendants, function(descendant) {
                angular.forEach(descendant.tasks, function(task) {
                    var taskDisplay = getTaskDisplay(task);
                    if (taskDisplay !== undefined) {
                        self.tasks.push(task);
                        var clone = new Task(self.row, task.model);

                        if (taskDisplay === 'overview') {
                            self.overviewTasks.push(clone);
                        } else if(taskDisplay === 'promote'){
                            self.promotedTasks.push(clone);
                        } else {
                            self.showGrouping = true;
                        }
                    }
                });
            });

            self.from = undefined;
            if (groupRowGroups) {
                self.from = groupRowGroups.from;
            }
            if (self.from === undefined) {
                angular.forEach(self.tasks, function (task) {
                    if (self.from === undefined || task.model.from < self.from) {
                        self.from = task.model.from;
                    }
                });
            }

            self.to = undefined;
            if (groupRowGroups) {
                self.to = groupRowGroups.to;
            }
            if (self.to === undefined) {
                angular.forEach(self.tasks, function (task) {
                    if (self.to === undefined || task.model.to > self.to) {
                        self.to = task.model.to;
                    }
                });
            }

            if (self.from && self.to) self.showGrouping = true;

            if (self.showGrouping) {
                self.left = row.rowsManager.gantt.getPositionByDate(self.from);
                self.width = row.rowsManager.gantt.getPositionByDate(self.to) - self.left;
            }
        };
        return TaskGroup;
    }]);
}());


//# sourceMappingURL=app.js.map