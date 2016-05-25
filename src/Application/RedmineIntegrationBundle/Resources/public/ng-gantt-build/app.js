/*
Project: angular-gantt v1.2.10 - Gantt chart component for AngularJS
Authors: Marco Schweighauser, Rémi Alvergnat
License: MIT
Homepage: https://www.angular-gantt.com
Github: https://github.com/angular-gantt/angular-gantt.git
*/
(function(){
    'use strict';
    angular.module('gantt.condensedtooltips', ['gantt', 'gantt.condensedtooltips.templates']).directive('ganttCondensedTooltips', ['$compile', '$document', function($compile, $document) {
        return {
            restrict: 'E',
            require: '^gantt',
            scope: {
                enabled: '=?',
                dateFormat: '=?',
                content: '=?',
                delay: '=?'
            },
            link: function(scope, element, attrs, ganttCtrl) {
                var api = ganttCtrl.gantt.api;

                // Load options from global options attribute.
                if (scope.options && typeof(scope.options.tooltips) === 'object') {
                    for (var option in scope.options.tooltips) {
                        scope[option] = scope.options[option];
                    }
                }

                if (scope.enabled === undefined) {
                    scope.enabled = true;
                }
                if (scope.dateFormat === undefined) {
                    scope.dateFormat = 'MMM DD, HH:mm';
                }
                if (scope.delay === undefined) {
                    scope.delay = 10;
                }
                /*if (scope.content === undefined) {
                    scope.content = 'ABC</br>'+
                                    '<small>'+
                                    '{{getFromLabel() + \' - \' + getToLabel()}}'+
                                    '</small>';
                }*/

                scope.api = api;

                api.directives.on.new(scope, function(directiveName, tooltipTargetScope, tooltipTargetElement, tooltipTargetAttributes) {
                    if (directiveName === 'ganttCondensedTaskGroupItem' || directiveName === 'ganttCondensedTaskGroupItemActiveFlag') {
                        var tooltipScope = tooltipTargetScope.$new();

                        tooltipScope.$element = angular.element(tooltipTargetElement);
                        //tooltipScope.taskGroup = tooltipTargetScope.taskGroups[0];
                        tooltipScope.tooltipText = tooltipTargetAttributes.tooltiptext;
                        tooltipScope.pluginScope = scope;

                        var ifElement = $document[0].createElement('div');
                        angular.element(ifElement).attr('data-ng-if', 'pluginScope.enabled');

                        var tooltipElement = $document[0].createElement('gantt-condensed-tooltip');
                        if (attrs.templateUrl !== undefined) {
                            angular.element(tooltipElement).attr('data-template-url', attrs.templateUrl);
                        }
                        if (attrs.template !== undefined) {
                            angular.element(tooltipElement).attr('data-template', attrs.template);
                        }

                        angular.element(ifElement).append(tooltipElement);
                        tooltipTargetElement.append($compile(ifElement)(tooltipScope));
                    }
                });
            }
        };
    }]);
}());


(function() {
    'use strict';
    angular.module('gantt.condensedtooltips').directive('ganttCondensedTooltip', ['$log','$timeout', '$compile', '$document', '$templateCache', 'ganttDebounce', 'ganttSmartEvent', '$rootScope', function($log, $timeout, $compile, $document, $templateCache, debounce, smartEvent, $rootScope) {
        // This tooltip displays more information about a group in condensed view

        return {
            restrict: 'E',
            templateUrl: function(tElement, tAttrs) {
                var templateUrl;
                if (tAttrs.templateUrl === undefined) {
                    templateUrl = 'plugins/tooltips/condensed-tooltip.tmpl.html';
                } else {
                    templateUrl = tAttrs.templateUrl;
                }
                if (tAttrs.template !== undefined) {
                    $templateCache.put(templateUrl, tAttrs.template);
                }
                return templateUrl;
            },
            scope: true,
            replace: true,
            controller: ['$scope', '$element', 'ganttUtils', function($scope, $element, utils) {
                //var bodyElement = angular.element($document[0].body);
                var showTooltipPromise;
                var visible = false;
                var mouseEnterX, mouseEnterY;

                /*var mouseMoveHandler = smartEvent($scope, bodyElement, 'mousemove', debounce(function(e) {
                    if (!visible) {
                        mouseEnterX = e.clientX;
                        mouseEnterY = e.clientY;
                        console.log('visible');
                        displayTooltip(true, false);
                    } else {
                        // TODO: parent rect
                        // check if mouse goes outside the parent
                        if(
                            !$scope.taskRect ||
                            e.clientX < $scope.taskRect.left ||
                            e.clientX > $scope.taskRect.right ||
                            e.clientY > $scope.taskRect.bottom ||
                            e.clientY < $scope.taskRect.top
                        ) {
//                            displayTooltip(false, false);
                        }

                        //updateTooltip(e.clientX, e.clientY);
                    }
                }, 5, false));*/
/*
                $scope.$element.bind('mousemove', function(evt) {
                    mouseEnterX = evt.clientX;
                    mouseEnterY = evt.clientY;
                });
*/              //var $element2 = $element.find('div')[0];
                //console.log($element2)

                $element.bind('click', function() {
                    displayTooltip(false, false);
                });
                $scope.$element.bind('mouseenter', function(evt) {
                    evt.stopPropagation();
                    mouseEnterX = evt.clientX;
                    mouseEnterY = evt.clientY;
                    displayTooltip(true, true);
                });

                $scope.$element.bind('mouseleave', function(evt) {
                    //console.log('leaving', evt)
                    displayTooltip(false);
                });

                $scope.getContent = function() {
                    return $scope.tooltipText;
                };

                var displayTooltip = function(newValue, showDelayed) {
                    if (showTooltipPromise) {
                        $timeout.cancel(showTooltipPromise);
                    }

                    var taskTooltips = true; //$scope.task.model.tooltips;
                    var rowTooltips = true; //$scope.task.row.model.tooltips;

                    if (typeof(taskTooltips) === 'boolean') {
                        taskTooltips = {enabled: taskTooltips};
                    }

                    if (typeof(rowTooltips) === 'boolean') {
                        rowTooltips = {enabled: rowTooltips};
                    }

                    var enabled = utils.firstProperty([taskTooltips, rowTooltips], 'enabled', $scope.pluginScope.enabled);
                    if (enabled && !visible && mouseEnterX !== undefined && newValue) {
                        if (showDelayed) {
                            showTooltipPromise = $timeout(function() {
                                showTooltip(mouseEnterX, mouseEnterY);
                            }, $scope.pluginScope.delay, false);
                        } else {
                            showTooltip(mouseEnterX, mouseEnterY);
                        }
                    } else if (!newValue) {
                        hideTooltip();
                    }
                };

                var showTooltip = function(x, y) {
                    visible = true;
                    //mouseMoveHandler.bind();

                    $scope.displayed = true;

                    $scope.$evalAsync(function() {
                        var restoreNgHide;
                        if ($element.hasClass('ng-hide')) {
                            $element.removeClass('ng-hide');
                            restoreNgHide = true;
                        }
                        $scope.elementHeight = $element[0].offsetHeight;
                        $scope.elementWidth = $element[0].offsetWidth;
                        if (restoreNgHide) {
                            $element.addClass('ng-hide');
                        }
                        updateTooltip(x, y);
                    });
                };

                var getViewPortDimensions = function() {
                    var d = $document[0];
                    return {
                      width: d.documentElement.clientWidth || d.documentElement.getElementById('body')[0].clientWidth,
                      height: d.documentElement.clientHeight || d.documentElement.getElementById('body')[0].clientHeight
                    }
                };

                var updateTooltip = function(x, y) {
                    var viewport = getViewPortDimensions();
                    // Check if info is overlapping with view port
                    if (x + $scope.elementWidth > viewport.width) {
                        $element.css('left', (x + 20 - $scope.elementWidth) + 'px');
                        $scope.isRightAligned = true;
                    } else {
                        $element.css('left', (x - 20) + 'px');
                        $scope.isRightAligned = false;
                    }

                    if ($scope.elementHeight < y) {
                        //$element.css('margin-top', (-$scope.elementHeight - 8) + 'px');
                        $element.css('top', y + (-$scope.elementHeight - 18) + 'px');
                        $scope.isTopAligned = true;
                        //console.log('top aligned', $element);
                    } else {
                        //$element.css('margin-top', '18px');
                        $element.css('padding-top', '23px');
                        $scope.isTopAligned = false;
                        //console.log('not top aligned', $element);
                    }
                };

                var hideTooltip = function() {
                    visible = false;
                    //mouseMoveHandler.unbind();
                    $scope.$evalAsync(function() {
                        $scope.displayed = false;
                    });
                };

                $scope.gantt.api.directives.raise.new('ganttCondensedTooltip', $scope, $element);
                $scope.$on('$destroy', function() {
                    $scope.gantt.api.directives.raise.destroy('ganttCondensedTooltip', $scope, $element);
                });
            }]
        };
    }]);
}());

angular.module('gantt.condensedtooltips.templates', []).run(['$templateCache', function($templateCache) {
    $templateCache.put('plugins/tooltips/condensed-tooltip.tmpl.html',
        '<div ng-cloak' +
        '     ng-show="displayed"\n' +
        '     class="gantt-condensed-tooltip-container">' +
        '<div ' +
        '     ng-class="{\'gantt-task-infoArrowR\': isRightAligned, \'gantt-task-infoArrow\': !isRightAligned, \'gantt-task-infoArrowT\': isTopAligned, \'gantt-task-infoArrowB\': !isTopAligned}"\n' +
        '     class="gantt-task-info"\n' +

        //'     ng-style="{top: taskRect.top + \'px\', marginTop: -elementHeight - 8 + \'px\'}">\n' +
        '     ng-style="{position: \'relative\'}">\n' +
        '    <div class="gantt-task-info-content gantt-condensedgroup-info-content">\n' +
        //'        <div gantt-bind-compile-html="pluginScope.content"></div>\n' +
        '        <div gantt-bind-compile-html="getContent()"></div>\n' +
        '    </div>\n' +
        '</div>\n' +
        '</div>\n');
}]);

//# sourceMappingURL=angular-gantt-tooltips-plugin.js.map

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
    angular.module('gantt.condensedgroups').controller('GanttCondensedGroupController', ['$scope', 'GanttCondensedGroups', 'ganttUtils', 'RedmineBaseUrl', function($scope, CondensedGroups, utils, RedmineBaseUrl) {
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

        // TODO escape / encode
        var linkIssue = function(issue) {
          return "<a href=\"" + RedmineBaseUrl + "/issues/" + issue.id + "\" target=\"_blank\">" + issue.subject + "</a>";
        };

        var assignee = function(issue) {
          return issue.assignee ? " assignee: " + issue.assignee : "";
        };

        $scope.currentDateTooltipText = (function() {
          var details = $scope.row.model.details;
          var issuesInProgress = _.chain(details.issuesInProgress)
                                  .map(function(issue) { return linkIssue(issue) + assignee(issue) +"<br>"; })
                                  .reduce(function(a,b) { return a + b; })
                                  .value();
          var html =
            "<small>Project manager: "+details.projectManager+'<br/>'+
            "Reported status: "+details.reportedStatus;
          if (issuesInProgress)
            html += "<br>Issues in progress: <br>"+ issuesInProgress;

          html += "</small>";
          return html;
        })();

        var removeWatch = $scope.pluginScope.$watch('display', updateCondensedTaskGroup);

        $scope.$watchCollection('gantt.rowsManager.filteredRows', updateCondensedTaskGroup);

        $scope.gantt.api.columns.on.refresh($scope, updateCondensedTaskGroup);

        $scope.$on('$destroy', removeWatch);
    }]);
}());

(function(){
  'use strict';
  angular.module('gantt.condensedgroups').directive('ganttCondensedTaskGroupItem', ['GanttDirectiveBuilder', function(Builder) {
    var builder = new Builder('ganttCondensedTaskGroupItem', 'plugins/groups/condensedTaskGroupItem.tmpl.html');
    /*builder.scope = {
      tooltipText: '=?'
    };
    builder.transclude = true;*/
    return builder.build();
  }]);
}());

(function(){
  'use strict';
  angular.module('gantt.condensedgroups').directive('ganttCondensedTaskGroupItemActiveFlag', ['GanttDirectiveBuilder', function(Builder) {
    var builder = new Builder('ganttCondensedTaskGroupItemActiveFlag', 'plugins/groups/condensedTaskGroupItemActiveFlag.tmpl.html');
    /*builder.scope = {
      tooltipText: '=?'
    };
    builder.transclude = true;*/
    return builder.build();
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
    $templateCache.put('plugins/groups/condensedTaskGroupItem.tmpl.html',
      '<div class="gantt-condensed-task-group-item">\n' +
      '        <gantt-condensed-task-group-item-active-flag tooltipText="{{currentDateTooltipText}}"></gantt-condensed-task-group-item-active-flag>' +
      '        <div class="gantt-task-group-left-main"></div>\n' +
      '        <div class="gantt-task-group-right-main"></div>\n' +
      '        <div class="gantt-task-group-left-symbol"></div>\n' +
      '        <div class="gantt-task-group-right-symbol"></div>\n' +
      '</div>\n');

    $templateCache.put('plugins/groups/condensedTaskGroupItemActiveFlag.tmpl.html',
      '<div ' +
      'ng-show="isActive(row.model.condensedGroups[$index])"' +
      '     ng-style="{ \'left\': gantt.currentDateManager.position - taskGroup.left - 6 + \'px\' }"' +
      '     class="gantt-task-group-current-date">' +
      '</div>\n');

    $templateCache.put('plugins/groups/condensedTaskGroup.tmpl.html',
      '<div class="gantt-condensed-task-group" ng-controller="GanttCondensedGroupController">\n' +
      '    <div class="gantt-task-group"\n' +
      '         ng-class="\'gantt-lifecycle-\' + taskGroup.name.substr(0,1)"\n' +
      '         ng-style="{\'left\': taskGroup.left + \'px\', \'width\': taskGroup.width + \'px\'}"\n' +
      '         ng-repeat="taskGroup in taskGroups">\n' +
      '        <gantt-condensed-task-group-item tooltipText="{{taskGroup.name}}"></gantt-condensed-task-group-item>\n' +
      '    </div>\n' +
      '</div>\n');
}]);

//# sourceMappingURL=angular-gantt-condensedgroups-plugin.js.map

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


gbGantt.constant('RedmineBaseUrl', '/redmine-proxy.php/');
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
    //'             <span class="gantt-label-text">{{collapsed?"c":"e"}}</span>' +
    '             <span\n' +
    '                class="gantt-tree-handle glyphicon"\n' +
    '                ng-class="{\n' +
    '                \'glyphicon-chevron-right\': collapsed, \'glyphicon-chevron-down\': !collapsed,\n' +
    '                \'gantt-tree-collapsed\': collapsed, \'gantt-tree-expanded\': !collapsed}"></span>\n' +
    '         </a>\n' +
    //'         <span class="gantt-label-text">{{collapsed?"c":"e"}}</span>' +
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

gbGantt.controller('ProjectsController', function($scope, Restangular, RedmineBaseUrl, $compile, moment, _, PrepareIssues, $timeout, $window, $q, ganttLayout, projectIds) {
  $scope.loading = true;

  projectIds = _.map(projectIds, function(item) {
    return item.id;
  });

  function projectId(rowId) {
    return "project_" + rowId;
  }

  function setGanttSpan(projects) {
    var projectStartDate = _.minBy(projects, function(cp) { return new Date(cp.start_date); });
    projectStartDate = projectStartDate ? projectStartDate.start_date : (function(){ var d = new Date();d.setUTCFullYear(d.getUTCFullYear()-1); return d;})().toISOString().substring(0, 10);
    var projectDueDate = _.maxBy(projects, function(cp) { return new Date(cp.due_date); });
    projectDueDate = projectDueDate ? projectDueDate.due_date : new Date();



    $scope.options.fromDate = projectStartDate;
    $scope.options.toDate = projectDueDate;
  }

  function projectsLoaded(projects) {
    var data = [];

    _.each(projects, function(project) {
      var condensedProjectRow = {
        isProject: true,
        projectId: project.id,
        id: projectId(project.id),
        name: project.name || "project " + project.projectId,
        groups: false,
        classes: ['gantt-row-lifecycle'],
        parent: projectId(project.parent_id),
        details: {
          projectManager: project.project_manager || '-',
          reportedStatus: project.reported_status,
          issuesInProgress: project.in_progress_issues
        }
      };

      var hasLifecycleCategories = !_.isEmpty(project.lifecycle_categories);
      if (false && !hasLifecycleCategories) { //TODO: ez hibas mert lehet alprojektje attol meg h vannak lifecycle categoryk alatta
        var childProjects = _.filter(projects, { 'parent_id': project.id });
        if (childProjects.length > 0) {
          var projectStartDate = _.minBy(childProjects, function(cp) { return new Date(cp.start_date); }).start_date;
          var projectDueDate = _.maxBy(childProjects, function(cp) { return new Date(cp.due_date); }).due_date;

          condensedProjectRow.groups = { enabled: true, display: 'group', from: moment(projectStartDate), to: moment(projectDueDate) };
        }
      }
      if (hasLifecycleCategories) {
        condensedProjectRow.condensedGroups = [];

        _.each(_.sortBy(project.lifecycle_categories, function (x) {
          return x.name;
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

      //condensedProjectRow.groups = false;condensedProjectRow.condensedGroups = [];//TODO: remove, this line resets the data on gantt
      data.push(condensedProjectRow);
    });

    console.log('projectsLoaded, data=',data);


    setGanttSpan(projects);

    $scope.data = data;
  }

  function applyFilters() {
    $scope.api.rows.refresh();
    $scope.toggleMenu();
  }


  var destroyOpenProjectListener = $scope.$on('openProject', function(e, projectRowScope) {
    //return;//TODO: remove
    var rowId = projectRowScope.row.model.id;
    var projectId = projectRowScope.row.model.projectId;
    var projectParent = projectRowScope.row.model.parent;

    console.log('ProjectController.on(openProject)', projectRowScope, '...loading issues...');

    Restangular.all('issues').getList({ project_id: projectId, limit: 100, include: 'relations', status_id: '*', start_date: '*' }).then(
      function(issues) {
        Restangular.stripRestangular(issues);
        console.log('ProjectController.on(openProject).issues_loaded', issues, 'broadcasting "projectOpened('+projectId+')"');
        $scope.$broadcast('projectOpened', projectId);

        var newRows = PrepareIssues(issues, rowId, projectId);
        var filteredRows = _.filter($scope.data, function(ganttRow) {
          // never get rid of the 'project rows'
          if (ganttRow.isProject) {
            return true;
          }

          return ganttRow.projectId === projectId;
        });

        $scope.data = filteredRows.concat(newRows);
        $scope.openedProject = projectId;

        console.log('ProjectController.on(openProject).issues_loaded', 'invoking projectRowScope.toggle', projectRowScope, projectRowScope.toggle);
        projectRowScope.toggle();//TODO: this probably should be disabled...

        $timeout(function() {
          $scope.api.side.setWidth(undefined);
        }, 0);
      });
  });

  var destroyCloseProjectListener = $scope.$on('closeProject', function (e, projectRowScope) {
    var projectId = projectRowScope.row.model.projectId;

    console.log('ProjectController.on(closeProject)', projectRowScope, ' projectId = ', projectId);

    $scope.data = _.filter($scope.data, function(ganttRow) {
      if (ganttRow.isProject) {
        return true;
      }

      return ganttRow.projectId !== projectId;
    });

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

  $scope.filterCategory = applyFilters;

  $scope.filterRowFunc = function (row) {
    var rowValue = $scope.filter.row;
    var categoryValue = $scope.filter.category;
    var rowVisibleByRowFilter = true;
    var rowVisibleByCategoryFilter = true;

    if (rowValue !== undefined && rowValue != '') {
      rowVisibleByRowFilter = row.model.name.indexOf(rowValue) > -1;
    }

    if (categoryValue !== undefined && categoryValue !== '') {
      if (row.model.condensedGroups === undefined) {
        console.log('hiding row', row.model.name, row.model.projectId);
        rowVisibleByCategoryFilter = false;
      } else {
        rowVisibleByCategoryFilter =
          _.chain(row.model.condensedGroups)
            .map(function (group) {
              return group.name.substr(0, 1) == categoryValue && $scope.isActiveLifecycleCategory(group)
            })
            //.tap(function(a) { console.log(a)})
            .any()
            .value();
      }
    }

    //console.log('row: ', rowVisibleByRowFilter, ', category: ', rowVisibleByCategoryFilter);
    return rowVisibleByRowFilter && rowVisibleByCategoryFilter;
  };

  $scope.isActiveLifecycleCategory = function(lifecycleCategory) {
    return lifecycleCategory.from <= $scope.api.gantt.currentDateManager.date &&
      $scope.api.gantt.currentDateManager.date < lifecycleCategory.to;
  };

  $scope.showMenu = false;
  $scope.toggleMenu = function() { $scope.showMenu = !$scope.showMenu };

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
    taskContent : '<i class="fa fa-tasks"></i> <a href="'+RedmineBaseUrl+'/issues/{{task.model.issueId}}" target="_blank">{{task.model.name}}</a>',
    columnWidth: 18,
    currentDate: 'line',
    currentDateValue: new Date(moment().format("YYYY"), moment().format("M") -1, moment().format("D"))
    //currentDateValue: new Date(2015, 5, 12)//, 9, 0, 0)
  };

  $scope.registerApi = function(api) {
    $scope.api = api;
    $controllerScope = $scope;

    api.directives.on.new($scope, function(dName, dScope, dElement, dAttrs, dController) {

      if (dName === 'ganttScrollable') {
        dScope.getScrollableCss = function() {
          var css = {};

          //var maxHeight = dScope.gantt.options.value('maxHeight');
          var maxHeight = $controllerScope.maxHeight();
          if (maxHeight > 0) {
            css['max-height'] = maxHeight - dScope.gantt.header.getHeight() + 'px';
            css['min-height'] = css['max-height'];
            css['overflow-y'] = 'auto';

            if (dScope.gantt.scroll.isVScrollbarVisible()) {
              css['border-right'] = 'none';
            }
          }

          var columnWidth = dScope.gantt.options.value('columnWidth');
          var bodySmallerThanGantt = dScope.gantt.width === 0 ? false: dScope.gantt.width < dScope.gantt.getWidth() - dScope.gantt.side.getWidth();
          if (columnWidth !== undefined && bodySmallerThanGantt) {
            css.width = (dScope.gantt.width + dScope.gantt.scroll.getBordersWidth() - 10) + 'px';
          }

          return css;
        };
      }

      // override gantt-tree-body's css to have min-height set
      if (dName === 'ganttTreeBody') {
        dScope.getLabelsCss = function() {
          var css = {};

          if (dScope.maxHeight) {
            var hScrollBarHeight = ganttLayout.getScrollBarHeight();
            var bodyScrollBarHeight = dScope.gantt.scroll.isHScrollbarVisible() ? hScrollBarHeight : 0;
            css['height'] = dScope.maxHeight - bodyScrollBarHeight - dScope.gantt.header.getHeight() + 'px';
          }

          return css;
        };
      }

      // override gantt-body-rows's css to have min-height set
      if (dName === 'ganttBodyRows') {
        dScope.getGanttBodyRowsCss = function() {
          var css = {};

          if (dScope.maxHeight) {
            var hScrollBarHeight = ganttLayout.getScrollBarHeight();
            var bodyScrollBarHeight = dScope.gantt.scroll.isHScrollbarVisible() ? hScrollBarHeight : 0;
            css['min-height'] = dScope.maxHeight - bodyScrollBarHeight - dScope.gantt.header.getHeight() + 'px';
          }

          return css;
        };

        // the compilation throws error with the ng-transclude attribute..
        dElement.removeAttr('ng-transclude');
        dElement.attr('ng-style', 'getGanttBodyRowsCss()');
        $compile(dElement)(dScope);
      }
    });

    api.core.on.ready($scope, function(api) {

      // scroll to the current date after the columns are displayed
      api.columns.on.generate($scope, function() {
        $timeout(function() {
          $scope.api.scroll.toDate($scope.options.currentDateValue);
          $scope.readyToShow = true;
        }, 0);
      });

      api.directives.on.new($scope, function(dName, dScope, dElement, dAttrs, dController) {
        if (dName === 'ganttTaskContent') {
          dElement.attr('inview', '');
          $compile(dElement)(dScope);
        }
      });

      var allProjectsData = [];
      var getNextProject = function(i) {
        $scope.loading = true;
        if (i >= projectIds.length) {
          throw 'Index overflow';
        }

        var projectId = projectIds[i];

        Restangular.one('ganttprojects', projectId).getList().then(function(data) {
          Restangular.stripRestangular(data);
          allProjectsData.push(data);

          if (i < projectIds.length-1) {
            getNextProject(i+1);
          } else {
            allProjectsLoaded(allProjectsData);
          }

        }, function(err) {
          if (i < projectIds.length) {
            getNextProject(i+1);
          } else {
            allProjectsLoaded(allProjectsData);
          }
        });
      };
      var allProjectsLoaded = function(data) {
        $scope.loading = false;

        var allProjects = _.chain(data)
          .map(function(projectRestangular) { return projectRestangular.plain(); })
          .flatten()
          .filter(function(project) { return _.filter(projectIds, project.id) })
          .value();

        projectsLoaded(allProjects);

        $timeout(function() {
          $scope.api.tree.collapseAll();

          $scope.api.side.setWidth(undefined);
        }, 0);
      };
      if (projectIds.length > 0) {
        getNextProject(0);
      } else {
        allProjectsLoaded([]);
      }
    });
  };
});

'use strict';

/**
 * @ngdoc function
 * @name ng-gantt.controller:CondensedGanttCtrl
 * @description
 * # CondensedGanttCtrl
 * Controller of the condensed gantt
 */
gbGantt.controller('CondensedGanttCtrl', function ($scope, Restangular, RedmineBaseUrl, $compile, moment, _, PrepareIssues, $timeout, $window, projectIds, $q, ganttLayout) {

    
 
    function projectId(rowId) {
      return "project_" + rowId;
    }

    var destroyOpenProjectListener = $scope.$on('openProject', function(e, projectRowScope) {
      var rowId = projectRowScope.row.model.id;
      var projectId = projectRowScope.row.model.projectId;
      var projectParent = projectRowScope.row.model.parent;

      Restangular.all('issues').getList({ project_id: projectId, limit: 100, include: 'relations', status_id: '*' }).then(
        function(issues) {

          $scope.$broadcast('projectOpened', projectId);

          var newRows = PrepareIssues(issues, rowId, projectId);
          var filteredRows = _.filter($scope.data, function(ganttRow) {
            // never get rid of the 'project rows'
            if (ganttRow.isProject) return true;

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
      var projectId = projectRowScope.row.model.projectId;
      $scope.data = _.filter($scope.data, function(ganttRow) {
        if (ganttRow.isProject) return true;

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

    /*
    var filterRowsFunc = function (rows) {
      return _.filter(rows, function(row) { return $scope.filterRowFunc(row) });
    };*/

    $scope.filterRowFunc = function (row) {
      var rowValue = $scope.filter.row;
      var categoryValue = $scope.filter.category;
      var rowVisibleByRowFilter = true;
      var rowVisibleByCategoryFilter = true;

      if (rowValue !== undefined && rowValue != '') {
        rowVisibleByRowFilter = row.model.name.indexOf(rowValue) > -1;
      }

      if (categoryValue !== undefined && categoryValue !== '') {
        if (row.model.condensedGroups === undefined) {
          console.log('hiding row', row.model.name, row.model.projectId);
          rowVisibleByCategoryFilter = false;
        } else {
          rowVisibleByCategoryFilter =
            _.chain(row.model.condensedGroups)
              .map(function (group) {
                return group.name.substr(0, 1) == categoryValue && $scope.isActiveLifecycleCategory(group)
              })
              //.tap(function(a) { console.log(a)})
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
      taskContent : '<i class="fa fa-tasks"></i> <a href="'+RedmineBaseUrl+'/issues/{{task.model.issueId}}" target="_blank">{{task.model.name}}</a>',
      columnWidth: 18,
      currentDate: 'line',
      currentDateValue: new Date(moment().format("YYYY"), moment().format("M") -1, moment().format("D"))
      //currentDateValue: new Date(2015, 5, 12)//, 9, 0, 0)
    };

    $scope.registerApi = function(api) {
      $scope.api = api;

      api.directives.on.new($scope, function(dName, dScope, dElement, dAttrs, dController) {

        // TODO: same in project-gantt
        // override the gantt-scrollable's css to have min-height set
        if (dName === 'ganttScrollable') {
          dScope.getScrollableCss = function() {
            var css = {};

            var maxHeight = dScope.gantt.options.value('maxHeight');
            if (maxHeight > 0) {
              css['max-height'] = maxHeight - dScope.gantt.header.getHeight() + 'px';
              css['min-height'] = css['max-height'];
              css['overflow-y'] = 'auto';

              if (dScope.gantt.scroll.isVScrollbarVisible()) {
                css['border-right'] = 'none';
              }
            }

            var columnWidth = dScope.gantt.options.value('columnWidth');
            var bodySmallerThanGantt = dScope.gantt.width === 0 ? false: dScope.gantt.width < dScope.gantt.getWidth() - dScope.gantt.side.getWidth();
            if (columnWidth !== undefined && bodySmallerThanGantt) {
              css.width = (dScope.gantt.width + dScope.gantt.scroll.getBordersWidth()) + 'px';
            }

            return css;
          };
        }

        // override gantt-tree-body's css to have min-height set
        if (dName === 'ganttTreeBody') {
          dScope.getLabelsCss = function() {
            var css = {};

            if (dScope.maxHeight) {
              var hScrollBarHeight = ganttLayout.getScrollBarHeight();
              var bodyScrollBarHeight = dScope.gantt.scroll.isHScrollbarVisible() ? hScrollBarHeight : 0;
              css['height'] = dScope.maxHeight - bodyScrollBarHeight - dScope.gantt.header.getHeight() + 'px';
            }

            return css;
          };
        }

        // override gantt-body-rows's css to have min-height set
        if (dName === 'ganttBodyRows') {
          dScope.getGanttBodyRowsCss = function() {
            var css = {};

            if (dScope.maxHeight) {
              var hScrollBarHeight = ganttLayout.getScrollBarHeight();
              var bodyScrollBarHeight = dScope.gantt.scroll.isHScrollbarVisible() ? hScrollBarHeight : 0;
              css['min-height'] = dScope.maxHeight - bodyScrollBarHeight - dScope.gantt.header.getHeight() + 'px';
            }

            return css;
          };

          // the compilation throws error with the ng-transclude attribute..
          dElement.removeAttr('ng-transclude');
          dElement.attr('ng-style', 'getGanttBodyRowsCss()');
          $compile(dElement)(dScope);
        }
      });

      api.core.on.ready($scope, function(api) {

        // scroll to the current date after the columns are displayed
        api.columns.on.generate($scope, function() {
          $timeout(function() {
            $scope.api.scroll.toDate($scope.options.currentDateValue);
            $scope.readyToShow = true;
          }, 0);
        });

        api.directives.on.new($scope, function(dName, dScope, dElement, dAttrs, dController) {
          if (dName === 'ganttTaskContent') {
            dElement.attr('inview', '');
            $compile(dElement)(dScope);
          }
        });

        var promises = [];
        var allProjectsData = [];

        var getNextProject = function(i){
          console.log('getNextProject.invoke',i);
          var projectId = projectIds[i];
          var promise = Restangular.one('ganttprojects', projectId).getList();
          promises.push(promise);

          promise.then(function(data){
            allProjectsData.push(data);
            if (i<projectIds.length) {
              console.log('getNextProject.then',i);
              getNextProject(i+1);
            } else {
              console.log('getNextProject.then - last project loaded',i);
              allProjectsLoaded(allProjectsData);
            }
          }, function(){
            console.warn('getNextProject.then - error', arguments);
          });

        }

        var allProjectsLoaded = function(data) {
          console.log('$q.all(promises)', arguments);
          var allProjects = _.chain(data)
                             .map(function(projectRestangular) { return projectRestangular.plain(); })
                             .flatten()
                             .filter(function(project) { return _.filter(projectIds, project.id) })
                             .value();

          projectsLoaded(allProjects);

          // collapse this way or need to override another tree tmpl..
          $timeout(function() {
            $scope.api.tree.collapseAll();

            $scope.api.side.setWidth(undefined);
          }, 0);
        };

        getNextProject(0);
      });
    };

    function projectsLoaded(projects) {
      var data = [];

      _.each(projects, function(project) {
        var condensedProjectRow = {
          isProject: true,
          projectId: project.id,
          id: projectId(project.id),
          name: project.name || "project " + project.projectId,
          groups: false,
          classes: ['gantt-row-lifecycle'],
          parent: projectId(project.parent_id),
          details: {
            projectManager: project.project_manager || '-',
            reportedStatus: project.reported_status,
            issuesInProgress: project.in_progress_issues
          }
        };

        if (_.isEmpty(project.lifecycle_categories)) {
          var childProjects = _.filter(projects, { 'parent_id': project.id });
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

      setGanttSpan(projects);

      $scope.data = data;
    }

    function setGanttSpan(projects) {
      var projectStartDate = _.min(projects, function(cp) { return new Date(cp.start_date) }).start_date;
      var projectDueDate = _.max(projects, function(cp) { return new Date(cp.due_date) }).due_date;

      $scope.options.fromDate = projectStartDate;
      $scope.options.toDate = projectDueDate;
    }


    console.log('CondensedGanttCtrl');
  });

'use strict';

gbGantt.controller('GanttTreeNodeToggleController', function($scope) {
    $scope.toggleNode = function() {
      console.log('GanttTreeNodeToggleController.toggleNode, $scope.collapsed = ',$scope.collapsed, 'isProjectRow() = ', isProjectRow());
      if (isProjectRow()) {
        toggleProject();
      } else {
        if (!$scope.isCollapseDisabled()) {
          $scope.toggle();
        }
      }
    };

    $scope.isCollapseDisabledOnNode = function() {
      //console.log('GanttTreeNodeToggleController.isCollapseDisabledOnNode, isProjectRow()= ', isProjectRow());
      if (isProjectRow()) {
        return false;
      } else {
        return $scope.isCollapseDisabled();
      }
    };

    $scope.$on("projectOpened", function (e, projectId) {
      if (!isProjectRow()) {
        return;
      }

      var closingThisProject = $scope.row.model.projectId !== projectId;
      console.log('GanttTreeNodeToggleController.projectOpened event caught, args.projectId', projectId, 'this.row.model.projectId', $scope.row.model.projectId, ' closingThis? ', closingThisProject, 'collapsed = ',$scope.collapsed);
      if (closingThisProject) {
        $scope.closeProject();
      } else {
        if ($scope.collapsed) {
          console.warn('GanttTreeNodeToggleController.projectOpened event caught', 'this project got opened but still collapsed = ',$scope.collapsed);
        }
      }
    });

    $scope.openProject = function () {
      if (!$scope.collapsed) {
        return;
      }
      console.log('GanttTreeNodeToggleController.openProject, model = ', $scope.row.model)
      $scope.collapsed = false;

      $scope.$emit('openProject', $scope);

      var idx = $scope.row.model.classes.indexOf('gantt-row-expanded');
      if (idx === -1) {
        $scope.row.model.classes.push('gantt-row-expanded');
      }
    };

    $scope.closeProject = function () {
      if ($scope.collapsed) {
        return;
      }
      console.log('GanttTreeNodeToggleController.closeProject, model = ', $scope.row.model)
      $scope.collapsed = true;

      if ($scope.row.model.parent) {
        console.log('GanttTreeNodeToggleController.closeProject, emitting "closeProject"');
        $scope.$emit('closeProject', $scope);
      } else {
        $scope.toggle();
      }

      var idx = $scope.row.model.classes.indexOf('gantt-row-expanded');
      if (idx > -1) {
        $scope.row.model.classes.splice(idx, 1);
      }
    };

    var isProjectRow = function() {
      return $scope.row.model.condensedGroups instanceof Array;
    };

    var toggleProject = function () {
      console.log('GanttTreeNodeToggleController.toggleProject, $scope.collapsed = ',$scope.collapsed);
      if ($scope.collapsed) {
        $scope.openProject();
      } else {
        $scope.closeProject();
      }
    };

    if (isProjectRow()) {
      //$scope.collapsed = true;
    }
  });

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

'use strict';

/**
 * @ngdoc function
 * @name ng-gantt.controller:ProjectGanttCtrl
 * @description
 * # ProjectGanttCtrl
 * Controller of the project gantt
 */
gbGantt.controller('ProjectGanttCtrl', function ($scope, Restangular, $stateParams, RedmineBaseUrl, $compile, moment, _, PrepareIssues, $window, $timeout) {
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

            Restangular.all('issues').getList({ project_id: $stateParams.projectId, limit: 100, include: 'relations', status_id: '*' })
                .then(function(issues) {

                    // hacky solution; expand-to-fit attribute seems to have problems
                    //expandGanttTimeSpan(issues);

                    $scope.data = PrepareIssues(issues);
                  console.log($scope.data)

                    $timeout(function() {
                        $scope.api.side.setWidth(undefined);
                    }, 0);
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
        // if the whole project's span is less than about 4 months, expand it by setting the dates explicitly
        //fromDate: new Date(2016,0,20),
        //toDate: new Date(2016,4,20),
        columnWidth: 18,
        currentDate: 'line',
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

        //contextMenuOptions: contextMenuOptions,
        rowContent: '<i class="fa fa-align-justify"></i> {{row.model.name}}',
        taskContent : '<i class="fa fa-tasks"></i> <span ng-context-menu="contextMenuOptions"><a href="'+RedmineBaseUrl+'/issues/{{task.model.issueId}}" target="_blank">{{task.model.name}}</a></span>',
    };

    $scope.maxHeight = function() {
        return $window.innerHeight;
    };

    function expandGanttTimeSpan(issues) {
        var start = moment(_.min(issues, function(issue) { return moment(issue.start_date) }).start_date);
        var end = moment(_.max(issues, function(issue) { return moment(issue.due_date) }).due_date);

        var durationDays = moment.duration(end.diff(start)).asDays();
        if (durationDays < 130) {
          $scope.options.fromDate = start.calendar();
          $scope.options.toDate = start.add(130, 'days').calendar();
        }
    }
});

'use strict';
// borrowed from https://stackoverflow.com/questions/29764079/angularjs-creating-context-menu-with-submenu

gbGantt.directive('ngContextMenu', function ($parse) {
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

gbGantt.directive('inview', function () {
    var getGanttBodyRight = function() {
        return document.querySelector('.gantt-body').getClientRects()[0].right;
    };

    return {
        link: function(scope, element) {
            scope.$watch(
                function() {
                    var clientRects = element[0].getClientRects();
                    if (clientRects.length == 0) return true;
                    return clientRects[0].right <= getGanttBodyRight();
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

gbGantt.factory('PrepareIssues', function() {
    return function (issues, root, rootId) {
      /* debug
       console.log(issues);
       _.each(issues, function (issue) {
       var parentId = issue.parent ? issue.parent.id : null;
       var lifecycleCategory = _.find(issue.custom_fields, {name: 'Lifecycle category'}).value;

       if (/^A/.test(lifecycleCategory))
       console.log(issue.custom_fields[4], issue);
       //console.log(issue.id, issue.subject, parentId, lifecycleCategory);
       });*/

      var data = [];
      var issuesDependencies = getIssuesDependencies(issues);
      var issuesByLifecycle = getIssuesByLifecycle(issues);

      _.each(_.keys(issuesByLifecycle).sort(), function (lifecycle) {
        var lifecycleRow = {
          id: lifecycleId(lifecycle, root),
          name: lifecycle ? lifecycle : '? - OTHER',
          groups: true,
          classes: 'gantt-row-lifecycle',
          projectId: rootId
        };

        if (root) lifecycleRow.parent = root;

        var sortedLifecycleChildIssues =
          _.chain(issuesByLifecycle[lifecycle])
          .sortBy(function (issue) {
            return getCustomFieldValue(issue, 'Position');
          })
          .sortBy(function(issue) {
            return moment(issue.start_date);
          }).value();

        _.each(sortedLifecycleChildIssues, function (issue) {
          var parentRowId = null;
          if (issue.parent) {
            var parentTicket = _.find(issues, {id: issue.parent.id});
            if (parentTicket) {
              parentRowId = rowId(parentTicket.id);
            } else {
              console.warn('Parent ticket not found: ', issue.parent.id);
              parentRowId = lifecycleId(lifecycle, root);
            }
          } else {
            parentRowId = lifecycleId(lifecycle, root);
          }

          var assigneeRole = getCustomFieldValue(issue, 'Assignee role');
          var dependencies = getTaskDependencyParameters(issue, issuesDependencies);

          var row =
          {
            id: rowId(issue.id),
            name: issue.subject,
            parent: parentRowId,
            projectId: rootId,
            tasks: [
              {
                issueId : issue.id,
                id: taskId(issue.id),
                name: issue.subject,
                from: issue.start_date,
                to: (function(issue){
                  var due = issue.due_date ? moment(issue.due_date) : null;
                  var start = moment(issue.start_date);
                  return due < start ? issue.start_date : issue.due_date; // -.-
                })(issue),
                type: issue.tracker.name,
                status: issue.status.name,
                priority: issue.priority.name,
                assignee: { role: assigneeRole, fullname: (issue.assigned_to && issue.assigned_to.name) || '-' },
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

    function lifecycleId(lifecycle, root) {
      return (root || 'root') + '_lifecycle_' + lifecycle;
    }

    function rowId(issueId) {
      return 'row_' + issueId;
    }

    function taskId(issueId) {
      return 'task_' + issueId;
    }

    function getTaskDependencyParameters(issue, issuesDependencies) {
      var issueDependencies = _.find(issuesDependencies, {id: issue.id});
      if (issueDependencies === undefined) return [];

      return _.map(issueDependencies.dependencies, function(dependencyId) {
        return { from: taskId(dependencyId) };
      });
    }

    function getIssuesDependencies(issues) {
      var issuesDependencies = [];

      _.each(issues, function (issue) {
        if (issue.relations.length == 0) return;

        _.filter(issue.relations, { relation_type: 'precedes' })
          .forEach(function(precedesRelation) {
            var issueDependencies;
            if (issueDependencies = _.find(issuesDependencies, { id: precedesRelation.issue_to_id })) {
              if (! _.includes(issueDependencies.dependencies, precedesRelation.issue_id))
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
      var customFieldByName = _.find(issue.custom_fields, {name: name});
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


gbGantt.service('ProjectsRepository', function(Restangular, $q){
  function getAllProjects(){
    var deferred = $q.defer();
    var pageSize = 100;
    var maxPage =  5;
    var projects = [];

    //pageSize = 10; maxPage = 1; //TODO: Remove

    function getProjectsPage(page) {
      Restangular.all('projects').getList({limit: pageSize, offset: page * pageSize, proxy_cache: true }).then(function(response) {
        response = Restangular.stripRestangular(response);
        var filteredProjects = _.filter(response, { parent: {name: "8 Ways"} });

        for (var i=0; i < filteredProjects.length; i++) {
          if (filteredProjects[i].name !== '8 Ways') {
            var shouldBeShownInGlobalGantt = false;
            var customFields = filteredProjects[i].custom_fields;
            for (var j=0; j<customFields.length; j++) {
              if ('show_on_global_gantt' === customFields[j].name && customFields[j].value) {
                shouldBeShownInGlobalGantt = true;
              }
            }
            if (!shouldBeShownInGlobalGantt) {
              continue;
            }
            projects.push(_.pick(filteredProjects[i], 'id', 'name'));
          }
        }

        if (response.length && page < maxPage) {
          getProjectsPage(page+1);
        } else {
          deferred.resolve(projects);
        }
      });
    }

    try {
      getProjectsPage(0);
    } catch(e) {
      console.error(e);
    }

    return deferred.promise;
  }


  return {
    getAllProjects: getAllProjects
  };
})

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

//# sourceMappingURL=app.js.map
