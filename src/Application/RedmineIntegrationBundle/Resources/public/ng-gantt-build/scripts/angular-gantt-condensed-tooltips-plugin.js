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
