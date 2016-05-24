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
