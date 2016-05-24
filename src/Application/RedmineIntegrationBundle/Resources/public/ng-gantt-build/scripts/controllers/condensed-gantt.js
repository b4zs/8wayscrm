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
