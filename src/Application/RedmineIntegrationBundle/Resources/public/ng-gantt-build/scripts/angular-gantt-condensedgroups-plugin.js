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
          console.log('ganttCtrl.$watch(gantt.rowsManager.filteredRows)');
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
