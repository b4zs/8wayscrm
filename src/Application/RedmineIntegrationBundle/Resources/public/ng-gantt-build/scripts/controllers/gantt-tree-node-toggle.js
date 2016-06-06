gbGantt.controller('GanttTreeNodeToggleController', function($scope) {
  var isProjectRow = function() {
    return $scope.row.model.condensedGroups instanceof Array;
  };


  var toggleProject = function () {
    console.log('GanttTreeNodeToggleController::toggleProject');
    if ($scope.collapsed) {
      $scope.openProject();
    } else {
      $scope.closeProject();
    }
  };

    $scope.toggleNode = function() {
      if (isProjectRow()) {
        toggleProject();
      } else {
        if (!$scope.isCollapseDisabled()) {
          $scope.toggle();
        }
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
      if (!isProjectRow()) {
        return;
      }

      var closingThisProject = $scope.row.model.projectId !== projectId;
      if (closingThisProject) {
        $scope.closeProject();
      } else {
        if ($scope.collapsed) {
        }
      }
    });

    $scope.openProject = function () {
      if (!$scope.collapsed) {
        console.info('returning false $scope.openProject');
        return;
      }

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

      $scope.collapsed = true;

      if ($scope.row.model.parent) {
        $scope.$emit('closeProject', $scope);
      } else {
        $scope.toggle();
      }

      var idx = $scope.row.model.classes.indexOf('gantt-row-expanded');
      if (idx > -1) {
        $scope.row.model.classes.splice(idx, 1);
      }
    };


    if (isProjectRow()) {
      $scope.collapsed = true;
    }
  });
