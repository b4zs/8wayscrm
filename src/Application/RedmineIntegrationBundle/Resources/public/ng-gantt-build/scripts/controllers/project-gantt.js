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
