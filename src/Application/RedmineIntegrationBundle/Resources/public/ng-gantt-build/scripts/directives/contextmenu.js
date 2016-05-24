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
