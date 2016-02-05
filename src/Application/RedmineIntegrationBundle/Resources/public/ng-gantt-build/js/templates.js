var __templateData = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;

var jade_indent = [];
buf.push("<!DOCTYPE html>\n<html lang=\"en\" ng-app=\"ng-gantt\">\n  <head>\n    <meta charset=\"utf-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n    <title>ng-gantt</title>\n    <link href=\"//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css\" rel=\"stylesheet\">\n    <link href=\"app.css\" rel=\"stylesheet\">\n    <link href=\"vendor.css\" rel=\"stylesheet\">\n  </head>\n  <body>\n    <div ui-view></div>\n  </body>\n  <script type=\"text/javascript\" src=\"vendor.js\"></script>\n  <script type=\"text/javascript\" src=\"app.js\"></script>\n</html>");;return buf.join("");
};
if (typeof define === 'function' && define.amd) {
  define([], function() {
    return __templateData;
  });
} else if (typeof module === 'object' && module && module.exports) {
  module.exports = __templateData;
} else {
  __templateData;
}
;var __templateData = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;

var jade_indent = [];
buf.push("\n<div gantt=\"gantt\" headers=\"['month', 'day']\" time-frames=\"options.timeFrames\" date-frames=\"options.dateFrames\" time-frames-non-working-mode=\"&quot;cropped&quot;\" from-date=\"options.fromDate\" to-date=\"options.toDate\" data=\"data\" api=\"registerApi\" options=\"options\" filter-row=\"filterRowFunc\" expand-to-fit=\"false\" shrink-to-fit=\"false\" currentDate=\"options.currentDate\" currentDateValue=\"options.currentDateValue\" column-width=\"options.columnWidth\" max-height=\"maxHeight()\" ng-show=\"readyToShow\">\n  <gantt-tree keep-ancestor-on-filter-row=\"true\"></gantt-tree>\n  <gantt-tooltips template-url=\"views/tooltips.tmpl.html\"></gantt-tooltips>\n  <gantt-condensed-groups display=\"group\"></gantt-condensed-groups>\n  <gantt-groups template-url=\"views/taskgroup.tmpl.html\"></gantt-groups>\n  <gantt-progress></gantt-progress>\n  <!-- gantt-dependencies(js-plumb-defaults='{ Connector: [ \"Flowchart\", { stub: 10 } ], ConnectionsDetachable: false, ConnectionOverlays: [[\"Arrow\", { location:1, length: 7, width: 7 }]], PaintStyle: { lineWidth: 0.8, strokeStyle:\"#000\" } }', endpoints='[ { anchor: [ 0.8, 1, 0, 1 ], isSource: true, enabled: false, endpoint: \"Blank\" }, { anchor: \"Left\", isTarget: true, enabled: false, endpoint: \"Blank\" } ]')-->\n</div><span id=\"menubutton\" ng-click=\"toggleMenu()\" ng-show=\"readyToShow\" class=\"glyphicon glyphicon-menu-hamburger\"></span>\n<div id=\"menu\" ng-show=\"showMenu\">\n  <div class=\"menu-content\">Show projects and rows containing:\n    <div>\n      <input type=\"text\" ng-model=\"filter.row\" ng-keypress=\"filterRow($event)\"/>\n    </div>\n  </div>\n  <div class=\"menu-content\">Show projects with active lifecycle category:\n    <div>\n      <select ng-change=\"filterCategory()\" ng-model=\"filter.category\">\n        <option value=\"\">any</option>\n        <option value=\"A\">A - ZERO VERSION DESIGN</option>\n        <option value=\"B\">B - PREPARATION</option>\n        <option value=\"C\">C - BRIEFING</option>\n        <option value=\"D\">D - DESIGN</option>\n        <option value=\"E\">E - SITEBUILD</option>\n        <option value=\"F\">F - DEVELOPMENT</option>\n        <option value=\"G\">G - FINALIZATION AND HANDOVER</option>\n      </select>\n    </div>\n  </div>\n</div>");;return buf.join("");
};
if (typeof define === 'function' && define.amd) {
  define([], function() {
    return __templateData;
  });
} else if (typeof module === 'object' && module && module.exports) {
  module.exports = __templateData;
} else {
  __templateData;
}
;var __templateData = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;

var jade_indent = [];
buf.push("\n<form ng-submit=\"login(credentials)\">\n  <label for=\"username\">Username:</label>\n  <input type=\"text\" ng-model=\"credentials.username\"/>\n  <label for=\"password\">Password:</label>\n  <input type=\"password\" ng-model=\"credentials.password\"/>\n  <button type=\"submit\">Login</button>\n</form>");;return buf.join("");
};
if (typeof define === 'function' && define.amd) {
  define([], function() {
    return __templateData;
  });
} else if (typeof module === 'object' && module && module.exports) {
  module.exports = __templateData;
} else {
  __templateData;
}
;var __templateData = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;

var jade_indent = [];
buf.push("\n<div gantt=\"gantt\" headers=\"['month', 'day']\" time-frames=\"timeFrames\" date-frames=\"dateFrames\" time-frames-non-working-mode=\"&quot;cropped&quot;\" data=\"data\" api=\"registerApi\" options=\"options\">\n  <gantt-tree></gantt-tree>\n  <gantt-tooltips template-url=\"views/tooltips.tmpl.html\"></gantt-tooltips>\n  <gantt-groups template-url=\"views/taskgroup.tmpl.html\"></gantt-groups>\n  <gantt-progress></gantt-progress>\n  <gantt-dependencies js-plumb-defaults=\"{ Connector: [ &quot;Flowchart&quot;, { stub: 10 } ], ConnectionsDetachable: false, ConnectionOverlays: [[&quot;Arrow&quot;, { location:1, length: 7, width: 7 }]], PaintStyle: { lineWidth: 0.8, strokeStyle:&quot;#000&quot; } }\" endpoints=\"[ { anchor: [ 0.8, 1, 0, 1 ], isSource: true, enabled: false, endpoint: &quot;Blank&quot; }, { anchor: &quot;Left&quot;, isTarget: true, enabled: false, endpoint: &quot;Blank&quot; } ]\"> </gantt-dependencies>\n</div>");;return buf.join("");
};
if (typeof define === 'function' && define.amd) {
  define([], function() {
    return __templateData;
  });
} else if (typeof module === 'object' && module && module.exports) {
  module.exports = __templateData;
} else {
  __templateData;
}
;var __templateData = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;

var jade_indent = [];
buf.push("\n<select ng-model=\"selectedProject\" ng-options=\"project as project.name for project in projects\"></select>\n<button ng-click=\"selectedProject &amp;&amp; showGantt(selectedProject.id)\">Show project</button>\n<button ng-click=\"selectedProject &amp;&amp; showCondensedGantt(selectedProject.id)\">Show overview</button>");;return buf.join("");
};
if (typeof define === 'function' && define.amd) {
  define([], function() {
    return __templateData;
  });
} else if (typeof module === 'object' && module && module.exports) {
  module.exports = __templateData;
} else {
  __templateData;
}
;var __templateData = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;

var jade_indent = [];
buf.push("\n<div ng-controller=\"GanttGroupController\">\n  <div ng-if=\"taskGroup.overviewTasks.length &gt; 0\" class=\"gantt-task-group-overview\">\n    <gantt-task-overview ng-repeat=\"task in taskGroup.overviewTasks\"></gantt-task-overview>\n  </div>\n  <div ng-if=\"taskGroup.row._collapsed &amp;&amp; taskGroup.promotedTasks.length &gt; 0\" class=\"gantt-task-group-promote\">\n    <gantt-task ng-repeat=\"task in taskGroup.promotedTasks\"></gantt-task>\n  </div>\n  <div ng-if=\"taskGroup.showGrouping\" ng-style=\"{'left': taskGroup.left + 'px', 'width': taskGroup.width + 'px'}\" style=\"position: absolute\" ng-class=\"'gantt-lifecycle-' + taskGroup.row.model.name.substr(0,1)\" class=\"gantt-task-group\">\n    <div class=\"gantt-task-group-left-main\"></div>\n    <div class=\"gantt-task-group-right-main\"></div>\n    <div class=\"gantt-task-group-left-symbol\"></div>\n    <div class=\"gantt-task-group-right-symbol\"></div>\n    <div ng-if=\"taskGroup.row.model.parent\" inview=\"inview\" class=\"gantt-task-group-label\">{{taskGroup.row.model.name}}</div>\n  </div>\n</div>");;return buf.join("");
};
if (typeof define === 'function' && define.amd) {
  define([], function() {
    return __templateData;
  });
} else if (typeof module === 'object' && module && module.exports) {
  module.exports = __templateData;
} else {
  __templateData;
}
;var __templateData = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;

var jade_indent = [];
buf.push("\n<div ng-cloak=\"ng-cloak\" ng-show=\"displayed\" ng-class=\"isRightAligned ? 'gantt-task-infoArrowR' : 'gantt-task-infoArrow'\" ng-style=\"{top: taskRect.top + 'px', marginTop: -elementHeight - 8 + 'px'}\" class=\"gantt-task-info\">\n  <div class=\"gantt-task-info-content\"><b>{{task.model.name}}</b><br/><small>Type: {{task.model.type}}<br/>Status: {{task.model.status}}<br/>Priority: {{task.model.priority}}<br/>Assignee: {{task.model.assignee.fullname}} ({{task.model.assignee.role}})</small></div>\n</div>");;return buf.join("");
};
if (typeof define === 'function' && define.amd) {
  define([], function() {
    return __templateData;
  });
} else if (typeof module === 'object' && module && module.exports) {
  module.exports = __templateData;
} else {
  __templateData;
}
;
//# sourceMappingURL=templates.js.map