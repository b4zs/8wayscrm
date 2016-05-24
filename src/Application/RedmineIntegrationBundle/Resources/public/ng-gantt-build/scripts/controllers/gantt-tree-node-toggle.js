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
